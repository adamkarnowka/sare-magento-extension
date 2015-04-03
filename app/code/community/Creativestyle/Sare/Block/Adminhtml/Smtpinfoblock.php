<?php
class Creativestyle_Sare_Block_Adminhtml_Smtpinfoblock  extends  Mage_Adminhtml_Block_Abstract  implements  Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct(){
        $this->setTemplate("sare/smtp.phtml");
    }
}