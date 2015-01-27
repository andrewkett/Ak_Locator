<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */


/**
 * Location url rewrite resource model
 */
class Ak_Locator_Model_Resource_Url extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Stores configuration array
     *
     * @var array
     */
    protected $_stores;


    /**
     * location attribute properties cache
     *
     * @var array
     */
    protected $_locationAttributes = array();

    /**
     * Limit locations for select
     *
     * @var int
     */
    protected $_locationLimit = 250;

    /**
     * Cache of root category children ids
     *
     * @var array
     */
    protected $_rootChildrenIds = array();

    /**
     * Load core Url rewrite model
     *
     */
    protected function _construct()
    {
        $this->_init('core/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
        if ($this->_stores === null) {
            $this->_stores = Mage::app()->getStores();
        }
        if ($storeId && isset($this->_stores[$storeId])) {
            return $this->_stores[$storeId];
        }
        return $this->_stores;
    }


    /**
     * Retrieve location model singleton
     *
     * @return Ak_Locator_Model_Location
     */
    public function getLocationModel()
    {
        return Mage::getSingleton('ak_locator/location');
    }

    /**
     * Retrieve rewrite by idPath
     *
     * @param string $idPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByIdPath($idPath, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('id_path = :id_path');
        $bind = array(
            'store_id' => (int)$storeId,
            'id_path' => $idPath
        );
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Retrieve rewrite by requestPath
     *
     * @param string $requestPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByRequestPath($requestPath, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('request_path = :request_path');
        $bind = array(
            'request_path' => $requestPath,
            'store_id' => (int)$storeId
        );
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Get last used increment part of rewrite request path
     *
     * @param string $prefix
     * @param string $suffix
     * @param int $storeId
     * @return int
     */
    public function getLastUsedRewriteRequestIncrement($prefix, $suffix, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $requestPathField = new Zend_Db_Expr($adapter->quoteIdentifier('request_path'));
        //select increment part of request path and cast expression to integer
        $urlIncrementPartExpression = Mage::getResourceHelper('eav')
            ->getCastToIntExpression($adapter->getSubstringSql(
                $requestPathField,
                strlen($prefix) + 1,
                $adapter->getLengthSql($requestPathField) . ' - ' . strlen($prefix) . ' - ' . strlen($suffix)
            ));
        $select = $adapter->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('MAX(' . $urlIncrementPartExpression . ')'))
            ->where('store_id = :store_id')
            ->where('request_path LIKE :request_path')
            ->where($adapter->prepareSqlCondition('request_path', array(
                'regexp' => '^' . preg_quote($prefix) . '[0-9]*' . preg_quote($suffix) . '$'
            )));
        $bind = array(
            'store_id' => (int)$storeId,
            'request_path' => $prefix . '%' . $suffix,
        );

        return (int)$adapter->fetchOne($select, $bind);
    }

    /**
     * Validate array of request paths. Return first not used path in case if validations passed
     *
     * @param array $paths
     * @param int $storeId
     * @return false | string
     */
    public function checkRequestPaths($paths, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'request_path')
            ->where('store_id = :store_id')
            ->where('request_path IN (?)', $paths);
        $data = $adapter->fetchCol($select, array('store_id' => $storeId));
        $paths = array_diff($paths, $data);
        if (empty($paths)) {
            return false;
        }
        reset($paths);

        return current($paths);
    }

    /**
     * Prepare rewrites for condition
     *
     * @param int $storeId
     * @param int|array $locationIds
     * @return array
     */
    public function prepareRewrites($storeId, $locationIds = null)
    {
        $rewrites = array();
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('is_system = ?', 1);
        $bind = array('store_id' => $storeId);

        if ($locationIds === null) {
            $select->where('location_id IS NULL');
        } elseif ($locationIds) {
            $select->where('location_id IN(?)', $locationIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        foreach ($rowSet as $row) {
            $rewrite = new Varien_Object($row);
            $rewrite->setIdFieldName($this->getIdFieldName());
            $rewrites[$rewrite->getIdPath()] = $rewrite;
        }

        return $rewrites;
    }

    /**
     * Save rewrite URL
     *
     * @param array $rewriteData
     * @param int|Varien_Object $rewrite
     * @return Mage_Catalog_Model_Resource_Url
     */
    public function saveRewrite($rewriteData, $rewrite)
    {
        $adapter = $this->_getWriteAdapter();
        try {

            $adapter->insertOnDuplicate($this->getMainTable(), $rewriteData);

            if (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')
                && Mage::getConfig()->getModuleConfig('Enterprise_UrlRewrite')
            ) {

                //@todo - this isn't a good way of achieving this, need to investigate 1.13 enterprise rewrites more

                $entityType = Mage::getModel('eav/config')->getEntityType('ak_locator_location');
                $entityTypeId = $entityType->getEntityTypeId();
                $suffix = '.html';
                $rewriteData['request_path'] = str_replace($suffix, '', $rewriteData['request_path']);

                $enterpriseData = array(
                    'request_path' => $rewriteData['request_path'],
                    'target_path' => $rewriteData['target_path'],
                    'is_system' => $rewriteData['is_system'],
                    'identifier' => $rewriteData['request_path'],
                    'guid' => Mage::helper('core')->uniqHash(),
                    'entity_type' => $entityTypeId
                );

                foreach ($enterpriseData as $data) {
                    if ($data === '' || $data === null) {
                        //can't save this one as the data isn't correct
                        Mage::log('Cannot create rewrite as data is bad, data: ' . print_r($enterpriseData, 1));
                        return;
                    }
                }

                $adapter->insertOnDuplicate($this->getTable('enterprise_urlrewrite/url_rewrite'), $enterpriseData);
            } else {
                //@todo, shouldn't need to index both url tables, put community code here
            }


        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('catalog')->__('An error occurred while saving the URL rewrite'));
        }

        if ($rewrite && $rewrite->getId()) {
            if ($rewriteData['request_path'] != $rewrite->getRequestPath()) {
                // Update existing rewrites history and avoid chain redirects
                $where = array('target_path = ?' => $rewrite->getRequestPath());
                if ($rewrite->getStoreId()) {
                    $where['store_id = ?'] = (int)$rewrite->getStoreId();
                }
                $adapter->update(
                    $this->getMainTable(),
                    array('target_path' => $rewriteData['request_path']),
                    $where
                );
            }
        }
        unset($rewriteData);

        return $this;
    }

    /**
     * Saves rewrite history
     *
     * @param array $rewriteData
     * @return Mage_Catalog_Model_Resource_Url
     */
    public function saveRewriteHistory($rewriteData)
    {
        $rewriteData = new Varien_Object($rewriteData);
        // check if rewrite exists with save request_path
        $rewrite = $this->getRewriteByRequestPath($rewriteData->getRequestPath(), $rewriteData->getStoreId());
        if ($rewrite === false) {
            // create permanent redirect
            $this->_getWriteAdapter()->insert($this->getMainTable(), $rewriteData->getData());
        }

        return $this;
    }


    /**
     * Save location attribute
     *
     * @param Varien_Object $location
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Url
     */
    public function saveLocationAttribute(Varien_Object $location, $attributeCode)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_locationAttributes[$attributeCode])) {

            $attribute = $this->getLocationModel()->getResource()->getAttribute($attributeCode);

            $this->_locationAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        $attributeTable = $this->_locationAttributes[$attributeCode]['table'];

        $attributeData = array(
            'entity_type_id' => $this->_locationAttributes[$attributeCode]['entity_type_id'],
            'attribute_id' => $this->_locationAttributes[$attributeCode]['attribute_id'],
            'store_id' => $location->getStoreId(),
            'entity_id' => $location->getId(),
            'value' => $location->getData($attributeCode)
        );

        if ($this->_locationAttributes[$attributeCode]['is_global'] || $location->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $adapter->select()
            ->from($attributeTable)
            ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
            ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
            ->where('store_id = ?', (int)$attributeData['store_id'])
            ->where('entity_id = ?', (int)$attributeData['entity_id']);

        $row = $adapter->fetchRow($select);
        if ($row) {
            $whereCond = array('value_id = ?' => $row['value_id']);
            $adapter->update($attributeTable, $attributeData, $whereCond);
        } else {
            $adapter->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $adapter->select()
                ->from($attributeTable)
                ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
                ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
                ->where('store_id = ?', (int)$attributeData['store_id'])
                ->where('entity_id = ?', (int)$attributeData['entity_id']);

            $row = $adapter->fetchRow($select);
            if ($row) {
                $whereCond = array('value_id = ?' => $row['value_id']);
                $adapter->update($attributeTable, $attributeData, $whereCond);
            } else {
                $adapter->insert($attributeTable, $attributeData);
            }
        }
        unset($attributeData);

        return $this;
    }

    /**
     * Retrieve product attribute
     *
     * @param string $attributeCode
     * @param int|array $locationIds
     * @param string $storeId
     * @return array
     */
    public function _getLocationAttribute($attributeCode, $locationIds, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        if (!isset($this->_locationAttributes[$attributeCode])) {
            $attribute = $this->getLocationModel()->getResource()->getAttribute($attributeCode);

            $this->_locationAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        if (!is_array($locationIds)) {
            $locationIds = array($locationIds);
        }
        $bind = array('attribute_id' => $this->_locationAttributes[$attributeCode]['attribute_id']);
        $select = $adapter->select();
        $attributeTable = $this->_locationAttributes[$attributeCode]['table'];
        if ($this->_locationAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('attribute_id = :attribute_id')
                ->where('store_id = ?', 0)
                ->where('entity_id IN(?)', $locationIds);
        } else {
            $valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select
                ->from(
                    array('t1' => $attributeTable),
                    array('entity_id', 'value' => $valueExpr)
                )
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=:store_id',
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.entity_id IN(?)', $locationIds);
            $bind['store_id'] = $storeId;
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        $attributes = array();
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($locationIds as $locationId) {
            if (!isset($attributes[$locationId])) {
                $attributes[$locationId] = null;
            }
        }

        return $attributes;
    }

    /**
     * Retrieve Product data objects
     *
     * @param int|array $locationIds
     * @param int $storeId
     * @param int $entityId
     * @param int $lastEntityId
     * @return array
     */
    protected function _getLocations($locationIds, $storeId, $entityId, &$lastEntityId)
    {
        $locations = array();
        $adapter = $this->_getReadAdapter();
        if ($locationIds !== null) {
            if (!is_array($locationIds)) {
                $locationIds = array($locationIds);
            }
        }
        $bind = array(
            // 'website_id' => (int)$websiteId,
            'entity_id' => (int)$entityId,
        );
        $select = $adapter->select()
            ->useStraightJoin(true)
            ->from(array('e' => $this->getTable('ak_locator/location')), array('entity_id'))
//            ->join(
//                array('w' => $this->getTable('ak_locator/location_website')),
//                'e.entity_id = w.location_id AND w.website_id = :website_id',
//                array()
//            )
            ->where('e.entity_id > :entity_id')
            ->order('e.entity_id')
            ->limit($this->_locationLimit);
        if ($locationIds !== null) {
            $select->where('e.entity_id IN(?)', $locationIds);
        }


        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $location = new Varien_Object($row);
            $location->setIdFieldName('entity_id');
            //$product->setCategoryIds(array());
            $location->setStoreId($storeId);
            $locations[$location->getId()] = $location;
            $lastEntityId = $location->getId();
        }

        unset($rowSet);

        if ($locations) {

            //foreach (array('title', 'url_key', 'url_path') as $attributeCode) {
            foreach (array('title', 'url_key') as $attributeCode) {
                $attributes = $this->_getLocationAttribute($attributeCode, array_keys($locations), $storeId);
                foreach ($attributes as $locationId => $attributeValue) {
                    $locations[$locationId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $locations;
    }

    /**
     * Retrieve Product data object
     *
     * @param int $locationId
     * @param int $storeId
     * @return Varien_Object
     */
    public function getLocation($locationId, $storeId)
    {
        $entityId = 0;
        $locations = $this->_getLocations($locationId, $storeId, 0, $entityId);
        if (isset($locations[$locationId])) {
            return $locations[$locationId];
        }
        return false;
    }

    /**
     * Retrieve Location data obects for store
     *
     * @param int $storeId
     * @param int $lastEntityId
     * @return array
     */
    public function getLocationsByStore($storeId, &$lastEntityId)
    {
        return $this->_getLocations(null, $storeId, $lastEntityId, $lastEntityId);
    }

    /**
     * Remove unused rewrites for locations
     *
     * @param int $locationId Product entity Id
     * @param int $storeId Store Id for rewrites
     * @return Mage_Catalog_Model_Resource_Url
     */
    public function clearLocationRewrites($locationId, $storeId)
    {
        $where = array(
            'product_id = ?' => $locationId,
            'store_id = ?' => $storeId
        );

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Finds and deletes product rewrites (that are not assigned to any category) for store
     * left from the times when product was assigned to this store's website and now is not assigned
     *
     * Notice: this routine is different from clearProductRewrites() and clearCategoryProduct() because
     * it handles direct rewrites to product without defined category (category_id IS NULL) whilst that routines
     * handle only product rewrites within categories
     *
     * @param int $storeId
     * @param int|array|null $locationId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function clearStoreLocationsInvalidRewrites($storeId, $locationId = null)
    {
        $store = $this->getStores($storeId);
        $adapter = $this->_getReadAdapter();
        $bind = array(
            'website_id' => (int)$store->getWebsiteId(),
            'store_id' => (int)$storeId
        );
        $select = $adapter->select()
            ->from(array('rewrite' => $this->getMainTable()), $this->getIdFieldName());
//            ->joinLeft(
//                array('website' => $this->getTable('ak_locator/location_website')),
//                'rewrite.location_id = website.location_id AND website.website_id = :website_id',
//                array()
//            )->where('rewrite.store_id = :store_id');
        //->where('rewrite.category_id IS NULL');
        if ($locationId) {
            $select->where('rewrite.location_id IN (?)', $locationId);
        } else {
            $select->where('rewrite.location_id IS NOT NULL');
        }
        //$select->where('website.website_id IS NULL');

        $rewriteIds = $adapter->fetchCol($select, $bind);
        if ($rewriteIds) {
            $where = array($this->getIdFieldName() . ' IN(?)' => $rewriteIds);
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }

        return $this;
    }

    /**
     * Finds and deletes old rewrites for store
     * a) product rewrites left from products that once belonged to this site,
     *    but then deleted or just removed from website
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function clearStoreInvalidRewrites($storeId)
    {
        //$this->clearStoreCategoriesInvalidRewrites($storeId);
        $this->clearStoreLocationsInvalidRewrites($storeId);
        return $this;
    }

    /**
     * Retrieve rewrites and visibility by store
     * Input array format:
     * product_id as key and store_id as value
     * Output array format (product_id as key)
     * store_id     int; store id
     * visibility   int; visibility for store
     * url_rewrite  string; rewrite URL for store
     *
     * @param array $locations
     * @return array
     */
    public function getRewriteByLocationStore(array $locations)
    {
        $result = array();

        if (empty($locations)) {
            return $result;
        }
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('i' => $this->getTable('catalog/category_product_index')),
                array('product_id', 'store_id', 'visibility')
            )
            ->joinLeft(
                array('r' => $this->getMainTable()),
                'i.product_id = r.product_id AND i.store_id=r.store_id AND r.category_id IS NULL',
                array('request_path')
            );

        $bind = array();
        foreach ($locations as $locationId => $storeId) {
            $locationBind = 'location_id' . $locationId;
            $storeBind = 'store_id' . $storeId;
            $cond = '(' . implode(' AND ', array(
                    'i.location_id = :' . $locationBind,
                    'i.store_id = :' . $storeBind,
                    //'i.category_id = :' . $catBind,
                )) . ')';
            $bind[$locationBind] = $locationId;
            $bind[$storeBind] = $storeId;
            $select->orWhere($cond);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $result[$row['location_id']] = array(
                'store_id' => $row['store_id'],
                'visibility' => $row['visibility'],
                'url_rewrite' => $row['request_path'],
            );
        }

        return $result;
    }

    /**
     * Find and return final id path by request path
     * Needed for permanent redirect old URLs.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param array $_checkedPaths internal varible to prevent infinite loops.
     * @return string | bool
     */
    public function findFinalTargetPath($requestPath, $storeId, &$_checkedPaths = array())
    {
        if (in_array($requestPath, $_checkedPaths)) {
            return false;
        }

        $_checkedPaths[] = $requestPath;

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), array('target_path', 'id_path'))
            ->where('store_id = ?', $storeId)
            ->where('request_path = ?', $requestPath);

        if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
            $idPath = $this->findFinalTargetPath($row['target_path'], $storeId, $_checkedPaths);
            if (!$idPath) {
                return $row['id_path'];
            } else {
                return $idPath;
            }
        }

        return false;
    }

    /**
     * Delete rewrite path record from the database.
     *
     * @param string $requestPath
     * @param int $storeId
     * @return void
     */
    public function deleteRewrite($requestPath, $storeId)
    {
        $this->deleteRewriteRecord($requestPath, $storeId);
    }

    /**
     * Delete rewrite path record from the database with RP checking.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param bool $rp whether check rewrite option to be "Redirect = Permanent"
     * @return void
     */
    public function deleteRewriteRecord($requestPath, $storeId, $rp = false)
    {
        $conditions = array(
            'store_id = ?' => $storeId,
            'request_path = ?' => $requestPath,
        );
        if ($rp) {
            $conditions['options = ?'] = 'RP';
        }
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }
}
