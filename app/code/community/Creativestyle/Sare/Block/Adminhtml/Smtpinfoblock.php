<?php
class Creativestyle_Sare_Block_Adminhtml_Smtpinfoblock  extends  Mage_Adminhtml_Block_Abstract  implements  Varien_Data_Form_Element_Renderer_Interface
{
    public function __construct(){
        $this->setTemplate("sare/smtp.phtml");
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        return $this->toHtml();
    }

    public function getList(){
        $model = new Creativestyle_Sare_Model_Customer();
        return $model->labels;
    }
}