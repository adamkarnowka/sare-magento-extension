<?php

class Creativestyle_Sare_Adminhtml_IndexController
    extends Mage_Adminhtml_Controller_Action
    {
        private $_fieldSeparator = ";";
        private $_lineSeparator = "\n";

    /**
     * @deprecated This is not needed now...
     */
    public function csvAction(){
            return $this;
            $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
            $collection->showCustomerInfo(true)
                       ->addSubscriberTypeField()
                       ->showStoreInfo();

            $csv = '';
            $csvArr = array();
            $subscribersArr = $collection->toArray();

            foreach($subscribersArr['items'] as $subscriberArray){
                $csvArr[] =  implode($this->_fieldSeparator, $subscriberArray);
            }

            $csvContent = implode($this->_lineSeparator, $csvArr);

            $this->getResponse()
                 ->clearHeaders()
                 ->setHeader('Content-Disposition', 'attachment; filename=sare_subscribers.csv')
                 ->setHeader('Content-Type', 'text/csv')
                 ->setBody($csvContent);
        }

    /**
     * This one is used for generating last-added subscribers in dashboard grid.
     */
     public function subscribersgridAction(){
            $this->loadLayout();
            $this->renderLayout();
     }

    /**
     * View used for displaying logs in nicely formatted view.
     */
    public function logsAction(){
            $logContent = file_get_contents(getcwd().DS.'var'.DS.'log'.DS.'sare.log');
            echo '<link rel="stylesheet" type="text/css" media="screen" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/base/default/css/sare.css" />';
            $logEntries = explode("\n", $logContent);
            foreach($logEntries as $logEntry){
                echo '<div class="logEntry">'.$logEntry.'</div>';
            }
            die();
    }

        public function synchroAction(){
            $processMode = $this->getRequest()->getParam('process');
            if($processMode==1){
                $subscriberId = $this->getRequest()->getParam('subscriber_id');
                $responseArr = array();

                // Logic for subscribe
                $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
                $collection->showCustomerInfo(true)
                    ->addSubscriberTypeField()
                    ->showStoreInfo();

                $collection->getSelect()->order('subscriber_id ASC');
                $collection->addFieldToFilter('subscriber_id', array('gt'=>$subscriberId));
                $currentSubscriber = $collection->getFirstItem();
                if(!$currentSubscriber->getId()){
                    echo json_encode($responseArr);
                    Mage::log('Batch process finished!', null, 'sare.log');
                    return false;
                }
                $responseArr['messages'][] = array('text'=>$this->__('Processing: %s', $currentSubscriber->getSubscriberEmail()), 'class'=>'user');

                if($currentSubscriber->getSubscriberStatus()==Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED){
                    // Should be subscriber
                    $responseArr['messages'][] = array('text'=>$this->__('<b>%s</b> should be subscribed...', $currentSubscriber->getSubscriberEmail()), 'class'=>'info');
                    $sare = Mage::getModel('creativestyle_sare/sare');
                    $mkey = $sare->subscribe($currentSubscriber->getEmail(), $currentSubscriber->getCustomerId(), $currentSubscriber->getUnsubscriptionLink());
                    if($mkey){
                        $responseArr['messages'][] = array('text'=> $this->__('Subscribed with mkey = %s', $mkey), 'class'=>'success');
                        if($currentSubscriber->getCustomerId()>0){
                            // This is existing customer, let's generate his data!
                            $customerData = Mage::getModel('creativestyle_sare/customer')->populateCustomerData(Mage::getModel('customer/customer')->load($currentSubscriber->getCustomerId()));
                            Mage::getModel('creativestyle_sare/sare')->updateCustomerData(Mage::getModel('customer/customer')->load($currentSubscriber->getCustomerId()), $customerData, $mkey);
                            $responseArr['messages'][] = array('text'=>$this->__('Populated detailed customer data and sent to SARE'), 'class'=>'success');
                        }
                        $responseArr['class'] = 'success';

                    } else {
                        if($sare->_responseCode){
                            $responseArr['messages'][] = array('text'=> $this->__('Failed to subscribe - SARE returned error %s (%s)', $sare->_responseCode, Mage::getModel('creativestyle_sare/response')->getErrorDescription($sare->_responseCode)), 'class'=>'error');
                        } else {
                            $responseArr['messages'][] = array('text'=> $this->__('Failed to subscribe - check logs!'), 'class'=>'error');
                        }
                    }
                } else {
                    // Should be unsubscribed
                    $responseArr['messages'][] = array('text'=>$this->__('<b>%s</b> should be unsubscribed...', $currentSubscriber->getSubscriberEmail()), 'class'=>'remove');
                    if(Mage::getModel('creativestyle_sare/sare')->unsubscribe($currentSubscriber->getEmail())){
                        $responseArr['messages'][] = array('text'=>$this->__('%s has been unsubscribed successfully', $currentSubscriber->getSubscriberEmail()), 'class'=>'success');
                        $responseArr['class'] = 'success';
                    } else {
                        $responseArr['messages'][] = array('text'=>$this->__('Failed to unsubscribe - check logs!'), 'class'=>'error');
                        $responseArr['class'] = 'error';
                    }
                }
                $responseArr['subscriber_id'] = $currentSubscriber->getSubscriberId();

                echo json_encode($responseArr);
                die();
            } else {

                $html  = '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'sare/synchro.js"></script>';
                $html .= '<script type="text/javascript" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'sare/jquery-1.10.2.min.js"></script>';

                $html .= '<link rel="stylesheet" type="text/css" media="screen" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/base/default/css/sare.css" />';
                $html .= '<style>* {font-family: Helvetica, "Tahoma", courier;font-size:12px;}    </style>';

                $html .= '<div id="wrapper">';
                $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
                $collection->showCustomerInfo(true)
                    ->addSubscriberTypeField()
                    ->showStoreInfo();

                $html .= '<div class="transfer"><div class="saretext"><b>'.$this->__('Whats going on?').'</b><ul class="sare-batch">
                          <li>'.$this->__('every confirmed subscriber is added to SARE').'</li>
                          <li>'.$this->__('if subscriber is not confirmed - appropriate status is set in SARE').'</li>
                          <li>'.$this->__('if subscriber is a customer - detailed data is populated and sent to SARE').'</li>
                          </ul>
                          <br/>'.$this->__('<b>NOTE:</b> Do not close this window.').'
                </div></div>';
                $html .= '<div class="row">'.$this->__('Started mass synchro...').'</div>';
                $html .= '<div class="row">'.$this->__('Subscribers to process: <b>%s</b>', $collection->getSize()).'</div>';

                $collection->getSelect()->order('subscriber_id ASC');
                $subscriber = $collection->getFirstItem();

                if($collection->getSize()>0){
                    $html.= '<script type="text/javascript">
                    finalMessage = "'.$this->__("<div class='row complete'>All done! <a href='https://ww0.enewsletter.pl/index2.php'>Login to SARE panel</a> now</div>").'";
                    currentUrl = "'.Mage::helper('core/url')->getCurrentUrl().'";sareProcessSubscriber("0");</script>';
                }

                $html .= '</div>';
                echo $html;
            }
        }
    }