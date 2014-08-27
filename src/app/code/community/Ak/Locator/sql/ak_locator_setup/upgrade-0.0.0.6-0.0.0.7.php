<?php
/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('ak_locator/location'), 'location_key', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'nullable'  => true,
        'comment'   => 'Location Key'
    ));
 
  $installer->getConnection()->addIndex(
    $installer->getTable('ak_locator/location')
    , $installer->getIdxName('ak_locator/location'
                            , array('location_key'))
    , 'location_key');
    
 $installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'location_key', array(
    'input'           => 'text',
    'type'            => 'static',
    'label'           => 'Location Key',
    'validate_rules'  => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 1,
    'sort_order'    => 2,
    'position'      => 30,
    'unique'        => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


 $keyGenerator = new LocationKeys();
 
 $locationCollection = Mage::getResourceModel('ak_locator/location_collection')
                                        ->addAttributeToSelect(array('title'));
 
foreach($locationCollection as $location)
{           
    $locationKey = $keyGenerator->getLocationKey($location->getTitle());
    $location->setLocationKey($locationKey)
             ->save();
}
 
 //add new attribute to location edit form
$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, 'location_key');
$attribute->setData('used_in_forms', array('location_edit','location_create'));
$attribute->save();

$installer->endSetup();
 

 
class LocationKeys
{
    
    private $hashTable = array();

    public function getLocationKey($title)
    {
        $hash = $this->getHash($title);
        if (isset($this->hashTable[$hash])) {
            $i =  $this->hashTable[$hash];
            $i++;
            $this->hashTable[$hash] = $i;
            $hash .= '_'.$i;
        }
        $this->hashTable[$hash] = 1;
        return $hash;
    }

    private function getHash($title)
    {    
        $digits = 3;
        if (!$title) {
            return 'locator_key_'.str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
        }
        $title  =  preg_replace('/\s+/', '_', $title);
        $title = strtolower($title);
        return $title;
    }
}