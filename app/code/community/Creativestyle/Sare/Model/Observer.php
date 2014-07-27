<?php
class Creativestyle_Sare_Model_Observer{


    public function newsletterSubscriberSaveCommitAfter(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('sare/settings/enabled')){
            return $this;
        }

        $event = $observer->getEvent();
        $subscriber = $event->getDataObject();

        $statusChange = $subscriber->getIsStatusChanged();

        if($subscriber->getCustomerId()==0){
            // This is guest subscriber, check if we should synchro them
            if(!Mage::getStoreConfig('sare/guest_settings/synchro_enabled')){
                return false;
            }
        } else {
            // Customer subscriber, again - need to check if we can save him/her
            if(!Mage::getStoreConfig('sare/customer_settings/synchro_enabled')){
                return false;
            }
        }

        // Trigger if user is now subscribed and there has been a status change:
        if ($statusChange == true) {
            if($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
               Mage::getModel('creativestyle_sare/sare')->subscribe($subscriber->getEmail(), $subscriber->getCustomerId(), $subscriber->getUnsubscriptionLink());


            } elseif($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
               Mage::getModel('creativestyle_sare/sare')->unsubscribe($subscriber->getEmail(), $subscriber->getCustomerId());
            }
        } else {
            // Seems to be Magento bug - when unsubscribing, flag: isStatusChanged is false - should be true?
            // Therefore we need to check if action == unsubscribe
            $moduleName = Mage::app()->getRequest()->getModuleName();
            $actionName = Mage::app()->getRequest()->getActionName();

            if($moduleName=='newsletter'&&$actionName=='unsubscribe'){
                Mage::getModel('creativestyle_sare/sare')->unsubscribe($subscriber->getEmail());
            }
        }
        return $observer;
    }

    public function test($event){
        if(!Mage::getStoreConfig('sare/settings/enabled')||!Mage::getStoreConfig('sare/settings/enabled_addresschange')){
            return $event;
        }
        $customerId = $event->getCustomerAddress()->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $this->updateCustomerData($event, $customer);
    }


    public function customerDeletedAction(Varien_Event_Observer $observer) {
        $subscriber = $observer->getEvent()->getSubscriber();
        Mage::getModel('creativestyle_sare/sare')->unsubscribe($subscriber->getEmail(), $subscriber->getCustomerId());
    }

    public function updateCustomerDataAfterOrder($observer){

        if(!Mage::getStoreConfig('sare/settings/enabled')||!Mage::getStoreConfig('sare/settings/enabled_afterorder')){
            return $observer;
        }

        $order = $observer->getOrder();
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $subscriberModel = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());

        if(!$subscriberModel->isSubscribed()){
            return false;
        }

        $sareSubscriberModel = Mage::getModel('creativestyle_sare/email')->loadByAttribute($customer->getEmail(), 'email');

        // Now we know we have customer already subscribed in SARE as well (we have valid mkey!)
        $customerData = Mage::getModel('creativestyle_sare/customer')->populateCustomerData($customer);
        if(is_object($sareSubscriberModel)&&$sareSubscriberModel->getMkey()!=''){
            Mage::getModel('creativestyle_sare/sare')->updateCustomerData($customer, $customerData, $sareSubscriberModel->getMkey());
        }

        return $observer;
    }

    public function updateCustomerData($observer, $customer = null){

        if(!Mage::getStoreConfig('sare/settings/enabled')){
            return $this;
        }

        if(!$customer){
            $customer = $observer->getCustomer();
        }

        // First, check if customer is subscribed in Magento NL
        $subscriberModel = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());

        if(!$subscriberModel->isSubscribed()){
            // When someone is now unsubscribed, we will not update data, to be fair.

            return false;
        }



        // Now check if we have mkey for this email address
        $sareSubscriberModel = Mage::getModel('creativestyle_sare/email')->loadByAttribute($customer->getEmail(), 'email');

        // Now we know we have customer already subscribed in SARE as well (we have valid mkey!)
        // Let's populate customer data
        $customerData = Mage::getModel('creativestyle_sare/customer')->populateCustomerData($customer);

        // Update customer data at SARE side
        if(is_object($sareSubscriberModel)&&$sareSubscriberModel->getMkey()!=''){
            Mage::getModel('creativestyle_sare/sare')->updateCustomerData($customer, $customerData, $sareSubscriberModel->getMkey());
        }
    }

    // This is ugly hack for getting into dashboard blocks
    // Alan Storm allowed it, so blame him!
    public function coreBlockAbstractPrepareLayoutAfter(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('sare/settings/enabled')){
            return $this;
        }
        if (is_object(Mage::app()->getFrontController()->getAction())&&Mage::app()->getFrontController()->getAction()->getFullActionName() === 'adminhtml_dashboard_index')
        {
            $block = $observer->getBlock();
            if ($block->getNameInLayout() === 'dashboard')
            {
                $block->getChild('lastOrders')->setUseAsDashboardHook(true);
            }
        }
    }

    // This is ugly hack for getting into dashboard blocks
    // Alan Storm allowed it, so blame him!
    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('sare/settings/enabled')){
            return $this;
        }

        if (is_object(Mage::app()->getFrontController()->getAction())&&Mage::app()->getFrontController()->getAction()->getFullActionName() === 'adminhtml_dashboard_index')
        {
            if ($observer->getBlock()->getUseAsDashboardHook())
            {
                $html = $observer->getTransport()->getHtml();
                $myBlock = $observer->getBlock()->getLayout()
                    ->createBlock('sare/adminhtml_dashboard')
                    ->setTheValuesAndTemplateYouNeed('HA!');
                $html .= $myBlock->toHtml();
                $observer->getTransport()->setHtml($html);
            }
        }
    }
}