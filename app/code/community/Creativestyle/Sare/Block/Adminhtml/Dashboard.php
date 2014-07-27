<?php
class Creativestyle_Sare_Block_Adminhtml_Dashboard  extends  Mage_Adminhtml_Block_Abstract
{
    public function __construct(){
        $this->setTemplate("sare/dashboard.phtml");
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        return Mage::app()->getLayout()->createBlock('Mage_Core_Block_Template', 'Sare_Infoblock', array('template' => 'sare/infoblock.phtml'))->toHtml();
    }

    public function getSubscribedCount(){
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()->addFieldToFilter('subscriber_status', array('eq'=>Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED));
        return $collection->getSize();
    }

    public function getNonActivated(){
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()->addFieldToFilter('subscriber_status', array('eq'=>Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE));
        return $collection->getSize();
    }

    public function getUnsubscribedCount(){
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()->addFieldToFilter('subscriber_status', array('eq'=>Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED));
        return $collection->getSize();
    }
}