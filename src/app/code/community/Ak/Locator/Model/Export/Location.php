<?php
/**
 * Export entity Location model
 *
 * @category    Ak
 * @package     Ak_Locator
 * 
 */
class Ak_Locator_Model_Export_Location extends Mage_ImportExport_Model_Export_Entity_Abstract
{
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
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'created_at'                  => array('backend_type' => 'datetime'),
        'updated_at'                  => array('backend_type' => 'datetime'),        
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array();

   
    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COL_LOCATION_KEY, self::COL_LAT,self::COL_LON);

   
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initAttrValues();
               
    }

   
   
    /**
     * Export process.
     *
     * @return string
     */
    public function export()
    {
        $collection     = $this->_prepareEntityCollection(Mage::getResourceModel('ak_locator/location_collection'));
        $validAttrCodes = $this->_getExportAttrCodes();
        $writer         = $this->getWriter();
                     

        // create export file
        $writer->setHeaderCols(array_merge(
            $this->_permanentAttributes, $validAttrCodes            
        ));
        foreach ($collection as $itemId => $item) { // go through all customers
            $row = array();

            // go through all valid attribute codes
            foreach ($validAttrCodes as $attrCode) {
                $attrValue = $item->getData($attrCode);

                if (isset($this->_attributeValues[$attrCode])
                    && isset($this->_attributeValues[$attrCode][$attrValue])
                ) {
                    $attrValue = $this->_attributeValues[$attrCode][$attrValue];
                }
                if (null !== $attrValue) {
                    $row[$attrCode] = $attrValue;
                }
            }
                                              
            $writer->writeRow($row);
            
        }
        return $writer->getContents();
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function filterAttributeCollection(Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection)
    {
        foreach (parent::filterAttributeCollection($collection) as $attribute) {
            if (!empty($this->_attributeOverrides[$attribute->getAttributeCode()])) {
                $data = $this->_attributeOverrides[$attribute->getAttributeCode()];

                if (isset($data['options_method']) && method_exists($this, $data['options_method'])) {
                    $data['filter_options'] = $this->$data['options_method']();
                }
                $attribute->addData($data);
            }
        }
        return $collection;
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('ak_locator/attribute_collection');
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'ak_locator_location';
    }
}
