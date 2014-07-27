<?php
class Creativestyle_Sare_Block_Abandonedcartitems extends Mage_Core_Block_Template
{
    public function __construct(){
        $this->setTemplate("sare/abandonedcarts.phtml");
    }

    public function getItems(){
        $customerId = $this->getRequest()->getParam('customer_id');
        $collection = Mage::getResourceModel('reports/quote_collection');
        $collection->prepareForAbandonedReport($this->_storeIds)->addFieldToFilter('customer_id', array('eq'=>$customerId));
        $this->collection = $collection;

        return $collection;
    }
}