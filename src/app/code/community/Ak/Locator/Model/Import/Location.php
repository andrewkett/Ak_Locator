<?php
/**
 * Import entity location model
 *
 * @category    Ak
 * @package     Ak_Locator
 */
class Ak_Locator_Model_Import_Location extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    /**
     * Size of bunch - part of entities to save in one step.
     */
    const BUNCH_SIZE = 20;

    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COL_LOCATION_KEY   = 'location_key';
    const COL_LAT            = 'latitude';
    const COL_LON            = 'longitude';
    
    /**
     * Error codes.
     */
   
    const ERROR_DUPLICATE_LOCATION_KEY = 'duplicateLocationKey';
    const ERROR_LOCATION_KEY_IS_EMPTY  = 'locationKeyIsEmpty';
    const ERROR_ROW_IS_ORPHAN          = 'rowIsOrphan';
    const ERROR_VALUE_IS_REQUIRED      = 'valueIsRequired';
    const ERROR_LOCATION_KEY_NOT_FOUND = 'locationKeyNotFound';
    const ERROR_GEOCODE                = 'geocodeError';
    const ERROR_GEOCODE_RET            =  'geocodeErrorRet';

    const XML_IMPORT_GEO_ENABLED = "locator_settings/import/geocode_enabled";
    const XML_IMPORT_VALID_LOCATIONTYPES = "locator_settings/import/geocode_valid_location_types";
    

    /**
     * attributes parameters.
     *
     *  [attr_code_1] => array(
     *      'options' => array(),
     *      'type' => 'text', 'price', 'textarea', 'select', etc.
     *      'id' => ..
     *  ),
     *  ...
     *
     * @var array
     */
    protected $_attributes = array();
        
    /**
     * location entity DB table name.
     *
     * @var string
     */
    protected $_entityTable;
    
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    
    protected $_messageTemplates = array (
        self::ERROR_DUPLICATE_LOCATION_KEY  => 'Location Key is duplicated in import file',
        self::ERROR_LOCATION_KEY_IS_EMPTY   => 'Location Key is not specified',
        self::ERROR_ROW_IS_ORPHAN           => 'Orphan rows that will be skipped due default row errors',
        self::ERROR_VALUE_IS_REQUIRED       => "Required attribute '%s' has an empty value",
        self::ERROR_LOCATION_KEY_NOT_FOUND  => 'Location Key  is not found',
        self::ERROR_GEOCODE                 => '%s could not be geocoded due to errors',
        self::ERROR_GEOCODE_RET             => 'Error geocoding %s'
    );

    /**
     * Dry-runned locations information from import file.
     *
     * @var array
     */
    protected $_newLocations = array();

    /**
     * Existing locations information. In form of:
     *
     * [location key] => array(
     *  location_id,...,    
     * )
     *
     * @var array
     */
    protected $_oldLocations = array();

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_particularAttributes = array();

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COL_LOCATION_KEY, self::COL_LAT,self::COL_LON);

    
    /**
     * @var array
     */
    protected $_ignoredAttributes = array();

    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initAttributes()
             ->_initLocations();

        $this->_entityTable   = Mage::getModel('ak_locator/location')->getResource()->getEntityTable();
        
    }

    /**
     * Delete Locations.
     *
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _deleteLocations()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $idToDelete = array();

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum)) {
                    $idToDelete[] = $this->_oldLocations[$rowData[self::COL_LOCATION_KEY]];
                }
            }
            if ($idToDelete) {
                $this->_connection->query(
                    $this->_connection->quoteInto(
                        "DELETE FROM `{$this->_entityTable}` WHERE `entity_id` IN (?)", $idToDelete
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Save location data to DB.
     *
     * @throws Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (Mage_ImportExport_Model_Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->_deleteLocations();
        } else {
            $this->_saveLocations();
        }
        return true;
    }

    /**
     * Initialize location attributes.
     *
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _initAttributes()
    {
        $collection = Mage::getResourceModel('ak_locator/attribute_collection')->addSystemHiddenFilter();
        foreach ($collection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = array(
                'id'          => $attribute->getId(),
                'is_required' => $attribute->getIsRequired(),
                'is_static'   => $attribute->isStatic(),
                'rules'       => $attribute->getValidateRules() ? unserialize($attribute->getValidateRules()) : null,
                'type'        => Mage_ImportExport_Model_Import::getAttributeType($attribute),
                'options'     => $this->getAttributeOptions($attribute)
            );
        }
        return $this;
    }

    

    /**
     * Initialize existent location data.
     *
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _initLocations()
    {
        foreach (Mage::getResourceModel('ak_locator/location_collection') as $location) {
            $locationKey = $location->getLocationKey();

            if (!isset($this->_oldLocations[$locationKey])) {
                $this->_oldLocations[$locationKey] = array();
            }
            $this->_oldLocations[$locationKey] = $location->getId();
        }
        
        return $this;
    }

    

    /**
     * Gather and save information about location entities.
     *
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _saveLocations()
    {
        /** @var $resource Ak_Location_Model_Location */
        $resource       = Mage::getModel('ak_locator/location');
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        $table = $resource->getResource()->getEntityTable();
        $nextEntityId   = Mage::getResourceHelper('importexport')->getNextAutoincrement($table);
        
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = array();
            $entityRowsUp = array();
            $attributes   = array();

            $oldLocationsToLower = array_change_key_case($this->_oldLocations, CASE_LOWER);
                      
            foreach ($bunch as $rowNum => $rowData) {
                              
                //prepare
                $rowData = $this->_prepareRow($rowData, $rowNum);
                                
                if (!$this->validateRow($rowData, $rowNum, false)) {
                    continue;
                }
                
                // entity table data
                $entityRow = array(
                    'created_at' => empty($rowData['created_at'])
                                    ? now() : gmstrftime($strftimeFormat, strtotime($rowData['created_at'])),
                    'updated_at' => now()
                );

                $locationKeyToLower = strtolower($rowData[self::COL_LOCATION_KEY]);
                if (isset($oldLocationsToLower[$locationKeyToLower])) { // edit
                    $entityId = $oldLocationsToLower[$locationKeyToLower];
                    $entityRow['entity_id']        = $entityId;
                    $entityRow['latitude']         = $rowData[self::COL_LAT];
                    $entityRow['longitude']        = $rowData[self::COL_LON];
                    $entityRowsUp[] = $entityRow;
                } else { // create
                    $entityId                      = $nextEntityId++;
                    $entityRow['entity_id']        = $entityId;
                    $entityRow['location_key']     = $rowData[self::COL_LOCATION_KEY];
                    $entityRow['latitude']         = $rowData[self::COL_LAT];
                    $entityRow['longitude']        = $rowData[self::COL_LON];
                    $entityRowsIn[]                = $entityRow;

                    $this->_newLocations[$rowData[self::COL_LOCATION_KEY]] = $entityId;
                }
                // attribute values
                foreach (array_intersect_key($rowData, $this->_attributes) as $attrCode => $value) {
                    if (!$this->_attributes[$attrCode]['is_static'] && (strlen($value) || $value == null)) {
                        /** @var $attribute Ak_Location_Model_Attribute */
                        $attribute  = $resource->getAttribute($attrCode);
                        $backModel  = $attribute->getBackendModel();
                        $attrParams = $this->_attributes[$attrCode];
                        if ($value != null) {
                            if ('select' == $attrParams['type']) {
                                $value = $attrParams['options'][strtolower($value)];
                            } elseif ('datetime' == $attrParams['type']) {
                                $value = gmstrftime($strftimeFormat, strtotime($value));
                            } elseif ($backModel) {
                                $attribute->getBackend()->beforeSave($resource->setData($attrCode, $value));
                                $value = $resource->getData($attrCode);
                            }
                        }
                        $attributes[$attribute->getBackend()->getTable()][$entityId][$attrParams['id']] = $value;

                        // restore 'backend_model' to avoid default setting
                        $attribute->setBackendModel($backModel);
                    }
                }
                    
            }
            $this->_saveLocationEntity($entityRowsIn, $entityRowsUp)->_saveLocationAttributes($attributes);
        }
        return $this;
    }

    /**
     * Save location attributes.
     *
     * @param array $attributesData
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _saveLocationAttributes(array $attributesData)
    {
        foreach ($attributesData as $tableName => $data) {
            $tableData = array();

            foreach ($data as $locationId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = array(
                        'entity_id'      => $locationId,
                        'entity_type_id' => $this->_entityTypeId,
                        'attribute_id'   => $attributeId,
                        'value'          => $value
                    );
                }
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, array('value'));
        }
        return $this;
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return Ak_Locator_Model_Import_Location
     */
    protected function _saveLocationEntity(array $entityRowsIn, array $entityRowsUp)
    {
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($this->_entityTable, $entityRowsIn);
        }
        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate(
                $this->_entityTable,
                $entityRowsUp,
                array('updated_at', 'created_at','latitude','longitude')
            );
        }
        return $this;
    }

    /**
     * Get Location ID. Method tries to find ID from old and new Locations. If it fails - it returns NULL.
     *
     * @param string $locationKey     
     * @return string|null
     */
    public function getLocationId($locationKey)
    {
        if (isset($this->_oldLocations[$locationKey])) {
            return $this->_oldLocations[$locationKey];
        } elseif (isset($this->_oldLocations[$locationKey])) {
            return $this->_oldLocations[$locationKey];
        } else {
            return null;
        }
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'ak_locator_location';
    }



    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param $rowNum
     * @param bool $prepareRow
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum, $prepareRow = true)
    {
        static $locationKey   = null; // locationKey is remembered through all location rows
                
        if (isset($this->_validatedRows[$rowNum])) { // check that row is already validated
            return !isset($this->_invalidRows[$rowNum]);
        }
        $this->_validatedRows[$rowNum] = true;
        
        $this->_processedEntitiesCount ++;
       
       
        $locationKey        = $rowData[self::COL_LOCATION_KEY];
        $locationKeyToLower = strtolower($rowData[self::COL_LOCATION_KEY]);
        

        $oldLocationsToLower = array_change_key_case($this->_oldLocations, CASE_LOWER);
        $newLocationsToLower = array_change_key_case($this->_newLocations, CASE_LOWER);

        // BEHAVIOR_DELETE use specific validation logic
        if (Mage_ImportExport_Model_Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($oldLocationsToLower[$locationKeyToLower])) {
                $this->addRowError(self::ERROR_LOCATION_KEY_NOT_FOUND, $rowNum);
            }
        } elseif (strlen(trim($rowData[self::COL_LOCATION_KEY]))) { // new location block begins
            //prepare row if upadate or insert
            if ($prepareRow) {
                $rowData = $this->_prepareRow($rowData, $rowNum);
            }
        
            if (isset($newLocationsToLower[$locationKeyToLower])) {
                $this->addRowError(self::ERROR_DUPLICATE_LOCATION_KEY, $rowNum);
            }
            $this->_newLocations[$locationKey] = false;

            // check simple attributes
            foreach ($this->_attributes as $attrCode => $attrParams) {
                if (in_array($attrCode, $this->_ignoredAttributes)) {
                     continue;
                }
                if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                     $this->isAttributeValid($attrCode, $attrParams, $rowData, $rowNum);
                } elseif ($attrParams['is_required'] && !isset($oldLocationsToLower[$locationKeyToLower])) {
                     $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNum, $attrCode);
                }
            }

            if (isset($this->_invalidRows[$rowNum])) {
                $locationKey = false; // mark row as invalid for next address rows
            }
        } else {
            if (null === $locationKey) { // first row is not valid
                $this->addRowError(self::ERROR_LOCATION_KEY_IS_EMPTY, $rowNum);
            } elseif (false === $locationKey) { //  row is invalid
                $this->addRowError(self::ERROR_ROW_IS_ORPHAN, $rowNum);
            }
        }
        return !isset($this->_invalidRows[$rowNum]);
    }
    
    protected function geocode($rowData, $rowNum, $depth = 0)
    {
        if (empty($rowData['address'])) {
            return $rowData;
        }
        $storeTitle = '';

        try {
            if (isset($rowData['title'])) {
                $storeTitle = $rowData['title'];
            }
            
            $geocodeAddr = null;
            $_userAddress = $rowData['address'];

            $string = str_replace(" ", "+", urlencode($_userAddress));
            $api_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&sensor=false";

            $cache = Mage::app()->getCache('locator_geo');
            $cacheKey = "LOCATOR_IMPORT_GEO_" . $string;
            
            if (false !== ($geocode = $cache->load($cacheKey))) {
                $geocodeAddr = unserialize($geocode);
            } else {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = json_decode(curl_exec($ch), true);

                if ($response['status'] == 'OK') {
                    $geocodeAddr  = array();
                    $geocodeAddr['address_components'] = $response['results'][0]['address_components'];
                    $geocodeAddr['geometry']           = $response['results'][0]['geometry'];
                    $geocodeAddr['formatted_address']  = $response['results'][0]['formatted_address'];
                    $cache->save(serialize($geocodeAddr), $cacheKey);
                } else {
                    $msg = @$response['error_message']?$response['error_message']:$response['status'];
                    $msg = $storeTitle.' : '.$msg;

                    // Handle Google throttling
                    // If we hit the max requests per second, wait 2 seconds and then try again
                    if ($response['status'] === 'OVER_QUERY_LIMIT') {
                        if ($depth > 10) {
                            Mage::log('max retry depth hit');
                            die();
                        }
                        sleep(2);
                        Mage::log('limit hit, sleeping');
                        $this->geocode($rowData, $rowNum, $depth+1);
                    }
                    $this->addRowError(self::ERROR_GEOCODE_RET, $rowNum, $msg);
                }
            }
            if ($geocodeAddr) {
                $rowData['address']   = $geocodeAddr['formatted_address'];
                $rowData['latitude']  = $geocodeAddr['geometry']['location']['lat'];
                $rowData['longitude'] =  $geocodeAddr['geometry']['location']['lng'];

                //address components
                $components = array(
                                    'sub_premise'             => 'subpremise',
                                    'premise'                 => 'street_number',
                                    'thoroughfare'            => 'route',
                                    'locality'                => 'locality',
                                    'dependent_locality'      => 'sublocality',
                                    'administrative_area'     => 'administrative_area_level_1',
                                    'sub_administrative_area' => 'administrative_area_level_2',
                                    'country'                 => 'country',
                                    'postal_code'             => 'postal_code'
                );
               
                foreach ($components as $colKey => $geoKey) {
                    foreach ($geocodeAddr['address_components'] as $addressComponent) {
                        if ($addressComponent['types'][0] == $geoKey) {
                            $rowData[$colKey] = $addressComponent['long_name'];
                        }
                    }
                }

                $rowData['geocoded'] = 1;
            }
        } catch (Exception $ex) {
            Mage::log($ex->getMessage());
            $this->addRowError(self::ERROR_GEOCODE, $rowNum, $storeTitle);
        }

        $types = explode(',', Mage::getStoreConfig(self::XML_IMPORT_VALID_LOCATIONTYPES));

        //If the response location type isnt in our valid types array treat this as invalid
        if (!in_array($geocodeAddr['geometry']['location_type'], $types)) {
            $msg = $storeTitle.' : could not find accurate location';
            $this->addRowError(self::ERROR_GEOCODE_RET, $rowNum, $msg);
        }

        return $rowData;
    }
 
    //if blank value found unest the data if null value found set data to blank
    protected function _cleanRow($rowData)
    {
        $row = array();
        foreach ($rowData as $key => $value) {
            $value = trim($value);
            if (strtolower($value) == 'null') {
                $row[$key] = null;
            } elseif (strlen($value)) {
                $row[$key] = $value;
            }
        }
        return $row;
    }
    
    protected function _prepareRow($rowData, $rowNum)
    {
        $rowData = $this->_cleanRow($rowData);
        
        //geocode if enabled
        if (Mage::getStoreConfig(self::XML_IMPORT_GEO_ENABLED)) {
            $rowData = $this->geocode($rowData, $rowNum);
        }
        
        return $rowData;
    }
        
    /**
     * Change row data before saving in DB table.
     *
     * @param array $rowData
     * @return array
     */
 
    protected function _prepareRowForDb(array $rowData)
    {
        return $rowData;
    }
}
