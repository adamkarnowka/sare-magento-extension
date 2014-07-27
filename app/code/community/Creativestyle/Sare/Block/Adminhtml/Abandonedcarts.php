<?php
class Creativestyle_Sare_Block_Adminhtml_Abandonedcarts  extends  Mage_Adminhtml_Block_Abstract  implements  Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct(){
        $this->setTemplate("sare/abandonedcarts.phtml");
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        return $this->toHtml();
    }

    public function getInterfaceUrl(){
        $url = Mage::getUrl('sare/index/abandonedcarts', array('key'=>Mage::getStoreConfig('sare/settings/key')));
        return $url;
    }
}