<?php
class Creativestyle_Sare_Model_Sare{

    private $_requestParams = array();
    private $_apiEndPoint = '';
    private $_method = '';
    private $_currentApiUrl = '';

    public $_responseCode = null;

    const SARE_API_SUBSCRIBE_METHOD = 'acq.php';
    const SARE_API_UNSUBSCRIBE_METHOD = 'rm.php';
    const SARE_API_UPDATE_METHOD = 'upd.php';

    const SETTINGS_KEY_CUSTOMER = 'customer';
    const SETTINGS_KEY_GUEST = 'guest';


    private function validateSettings(){
        if(!Mage::getStoreConfig('sare/settings/uid') || !Mage::getStoreConfig('sare/settings/key')){
            Mage::log('Wrong settings, cannot do anything...', Zend_Debug::ALERT, 'sare.log');
            return false;
        }
        return true;
    }

    private function buildDefaultParamsForRemoval(){
        $this->_requestParams['u'] = Mage::getStoreConfig('sare/settings/uid');
        $this->_requestParams['key'] = Mage::getStoreConfig('sare/settings/key');
        $this->_apiEndPoint = Mage::getStoreConfig('sare/settings/sare_endpoint_url');
    }

    private function buildDefaultParams(){
        $now = Mage::getModel('core/date')->timestamp(time());

        $this->_requestParams['s_uid'] = Mage::getStoreConfig('sare/settings/uid');
        $this->_requestParams['s_key'] = Mage::getStoreConfig('sare/settings/key');
        $this->_requestParams['s_rv'] = 1; // This one will force numerical response from SARE side
        $this->_requestParams['s_status'] = Mage::getStoreConfig('sare/settings/save_as');
        $this->_requestParams['s_cust_data_last_updated_at'] = date('Y-m-d H:i:s', $now);

        $this->_apiEndPoint = Mage::getStoreConfig('sare/settings/sare_endpoint_url');

        if(!Mage::getStoreConfig('sare/settings/send_confirmation_email')){
            // s_no_send = 1 will not trigger default confirmation emails
            $this->_requestParams['s_no_send'] = 1;
        }
    }

    private function send(){
        $url = $this->_apiEndPoint.$this->_method;
        $pairs = array();
        foreach($this->_requestParams as $key => $value){
            $pairs[] = $key.'='.urlencode($value);
        }

        $url .= '?'.implode('&', $pairs);

        $this->_currentApiUrl = $url;

        Mage::log(' Sent request / : '.$this->_method.' - '.$url,1,'sare.log');
        try{
            $response = file_get_contents($url);
        } catch (Exception $e){
            Mage::log('Fatal exception: '.$e->getMessage(),null, 'sare.log');
            return false;
        }
        return $this->processResponse($response);
    }


    private function processResponse($response, $email = null){
        if(is_numeric($response)){
            $this->_responseCode = $response;
        }

        $keyArr = explode('=', $response);
        if($this->_method==self::SARE_API_SUBSCRIBE_METHOD){
            if(count($keyArr)==2){
                $model = Mage::getModel('creativestyle_sare/email');
                $collection = $model->getCollection()->addFieldToFilter('email', array('eq'=>$this->_requestParams['s_email']));
                foreach($collection as $item){
                    $modeItem = Mage::getModel('creativestyle_sare/email')->load($item->getId());
                    $modeItem->delete();
                }
                $model->setId(NULL)->setEmail($this->_requestParams['s_email'])->setCreatedAt(date('Y-m-d H:i:s'))->setMkey($keyArr[1])->save();
                Mage::log('  -> Got acq response: '.$keyArr[1].' / '.$this->_requestParams['s_email'],Zend_Log::INFO,'sare.log');

                // Populate customer data
                $customersCollection = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email', array('eq'=>$this->_requestParams['s_email']));
                foreach($customersCollection as $customerModel){
                    $customerData = Mage::getModel('creativestyle_sare/customer')->populateCustomerData(Mage::getModel('customer/customer')->load($customerModel->getId()));
                    Mage::getModel('creativestyle_sare/sare')->updateCustomerData($customerModel, $customerData, $keyArr[1]);
                }






                return $keyArr[1];
            } else {

                if(is_numeric($response)){
                    Mage::log('  --> System returned problem: #'.$response, null, 'sare.log');
                    $this->_responseCode = $response;
                }
                Mage::helper('sare')->sendProblemNotification('Subscribe problem', Mage::getModel('creativestyle_sare/response')->getErrorDescription($this->_responseCode).' ('.$this->_responseCode.')', $this->_requestParams, $this->_currentApiUrl);
                return false;
            }
        }
        if($this->_method==self::SARE_API_UPDATE_METHOD){
            if(trim($response)=="1"){
                Mage::log('  -> Got upd response: '.$response, Zend_Log::INFO, 'sare.log');
                return true;
            } else {
                Mage::helper('sare')->sendProblemNotification('Update problem', Mage::getModel('creativestyle_sare/response')->getErrorDescription($this->_responseCode).' ('.$this->_responseCode.')', $this->_requestParams, $this->_currentApiUrl);
                Mage::log('  -> Got upd response (failed): '.$response, Zend_Log::ALERT,'sare.log');
                return false;
            }
        }
        if($this->_method==self::SARE_API_UNSUBSCRIBE_METHOD){
            if(!empty($this->_requestParams['mkey'])){
                $model = Mage::getModel('creativestyle_sare/email')->loadByAttribute($this->_requestParams['mkey'], 'mkey');
                $fullModel = Mage::getModel('creativestyle_sare/email')->load($model->getId());

                $fullModel->delete();
                Mage::log('  -> Got rm.php response: '.htmlentities(nl2br($response)), Zend_Log::ALERT, 'sare.log');
                return false;
            }

            // We're just gonna assume it was ok
            return true;
        }
    }

    public function subscribe($email, $customerId, $unsubscriptionUrl){
        if(!$this->validateSettings()){
            return false;
        }

        if($customerId==0){
            // Guest subscriber
            $settingsKey = self::SETTINGS_KEY_GUEST;
        } else {
            // Customer subscriber
            $settingsKey = self::SETTINGS_KEY_CUSTOMER;
        }

        $this->buildDefaultParams();
        $this->_requestParams['s_email'] = $email;
        $this->_requestParams['s_cust_unsubscription_link'] = $unsubscriptionUrl; // Unsubscription link MUST BE always sent!

        $targetGroups = explode(',', Mage::getStoreConfig('sare/'.$settingsKey.'_settings/group_id'));
        foreach($targetGroups as $targetGroup){
            $this->_requestParams['s_group_'.$targetGroup] = 1;
        }

        $this->_method = self::SARE_API_SUBSCRIBE_METHOD;
        return $this->send();
    }

    public function unsubscribe($email){
        if(!$this->validateSettings()){
            return false;
        }

        $this->buildDefaultParamsForRemoval();

        $emailsCollection = Mage::getModel('creativestyle_sare/email')->getCollection()->addFieldToFilter('email', array('eq'=>$email));
        $emailObject = $emailsCollection->getFirstItem();

        $this->_requestParams['mkey'] = $emailObject->getMkey();
        $this->_requestParams['s_status'] = 7;
        $this->_method = self::SARE_API_UNSUBSCRIBE_METHOD;

        if(empty($this->_requestParams['mkey'])){
            if(!empty($email)){
                Mage::log(Mage::helper('sare')->__('Cant unsubscribe this address: %s, missing mkey!', $email), null, 'sare.log');
                return false;
            }
        }
        return $this->send();
    }

    public function updateCustomerData($customer, $customerData, $key){
        if(!$this->validateSettings()){
            return false;
        }

        $this->buildDefaultParams();
        $this->_method = self::SARE_API_UPDATE_METHOD;
        $this->_requestParams['s_mkey'] = $key;
        $this->_requestParams['s_name'] = $customer->getFirstname().' '.$customer->getLastname();
        foreach($customerData as $key=>$value){
            $this->_requestParams['s_cust_'.$key] = $value;
        }

        $this->send();
    }
}