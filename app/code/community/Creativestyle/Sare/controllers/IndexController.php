<?php
class Creativestyle_Sare_IndexController extends Mage_Core_Controller_Front_Action
{
    public function abandonedcartsAction(){

               die('Disabled in this version.');

               $key = $this->getRequest()->getParam('key');
               if($key!=Mage::getStoreConfig('sare/settings/key')){
                    die('Wrong key!');
               }

               $collection = Mage::getResourceModel('reports/quote_collection');
               $collection->prepareForAbandonedReport($this->_storeIds);

               $items= array();
               foreach($collection as $order){

                   $updatedAt = $order->getUpdatedAt();
                   $item = array('updated_at'=>$updatedAt,
                                 'created_at'=>$order->getCreatedAt(),
                                 'grand_total'=>$order->getGrandTotal(),
                                 'customer_name'=>$order->getCustomerName(),
                                 'customer_id'=>$order->getCustomerId(),
                                 'customer_email'=>$order->getCustomerEmail(),
                    );

                   $productItems = '';
                   foreach($order->getAllVisibleItems() as $quoteItem){
                       $productItems = implode('|',  array('sku'=>$quoteItem->getSku(),
                                               'name'=>$quoteItem->getName(),
                                               'qty'=>$quoteItem->getQty(),
                                               'price'=>$quoteItem->getPrice()
                                        ));
                   }

                   $item['items'] = $productItems;
                   $items[] = $item;
               }
                echo serialize($items);
                die();
    }

    public function getcartAction(){
        $key = $this->getRequest()->getParam('key');
        if($key!=Mage::getStoreConfig('sare/settings/key')){
            die('Wrong key!');
        }

        $block = Mage::app()->getLayout()->createBlock('Creativestyle_Sare_Block_Abandonedcartitems', 'Sare_ACItems', array('template' => 'sare/abandonedcarts.phtml'));
        echo $block->toHtml();
        die();
    }
}