<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Adam
 * Date: 10.09.13
 * Time: 08:57
 * To change this template use File | Settings | File Templates.
 */
class Creativestyle_Sare_Block_Adminhtml_Dashboard_Grids extends Mage_Adminhtml_Block_Dashboard_Grids {

   public function _prepareLayout(){
       parent::_prepareLayout();

       $this->addTab('subscribers', array(
           'label'     => $this->__('Recent subscribers'),
           'url'       => $this->getUrl('sare/adminhtml_index/subscribersgrid', array('_current'=>true)),
           'class'     => 'ajax'
       ));

       return parent::_prepareLayout();
   }
}