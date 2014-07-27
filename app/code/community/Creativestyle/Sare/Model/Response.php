<?php
class Creativestyle_Sare_Model_Response{

    public $ERROR_CODES = array();

    public function __construct(){
        $this->ERROR_CODES =  array(1=>Mage::helper('sare')->__('Address added successfully.'),
                                    4=>Mage::helper('sare')->__('Address already exists, but has not been confirmed yet.'),
                                    7=>Mage::helper('sare')->__('Address already exists, but it is blocked by SARE operator.'),
                                    8=>Mage::helper('sare')->__('Address already exists, and it is confirmed already.'),
                                   -1=>Mage::helper('sare')->__('One the required parameters is missing.'),
                                   -2=>Mage::helper('sare')->__('E-mail address is not formed correctly.'),
                                   -3=>Mage::helper('sare')->__('UID number is not formed correctly.'),
                                   -4=>Mage::helper('sare')->__('Wrong integration key.'),
                                   -5=>Mage::helper('sare')->__('GSM number is not formed correctly.'),
                                   -97=>Mage::helper('sare')->__('API limit is set.'),
                                   -98=>Mage::helper('sare')->__('Wrong UID.'),
                                   -99=>Mage::helper('sare')->__('Database connection error.'),
        );
    }

    public function getErrorDescription($errorCode){
        return isset($this->ERROR_CODES[$errorCode]) ? $this->ERROR_CODES[$errorCode] : Mage::helper('sare')->__('Unknown error.');
    }

}