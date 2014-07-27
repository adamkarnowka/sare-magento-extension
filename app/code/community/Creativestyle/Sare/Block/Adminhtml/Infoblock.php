<?php
class Creativestyle_Sare_Block_Adminhtml_Infoblock  extends  Mage_Adminhtml_Block_Abstract  implements  Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct(){
        $this->setTemplate("sare/infoblock.phtml");
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        return Mage::app()->getLayout()->createBlock('Mage_Core_Block_Template', 'Sare_Infoblock', array('template' => 'sare/infoblock.phtml'))->toHtml();
    }
}