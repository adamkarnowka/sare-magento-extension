<?php
class Creativestyle_Sare_Model_Customer extends Mage_Core_Model_Abstract{

    public $_attributes = array('customer_id', 'store_id', 'website_id', 'last_logged_in', 'registered_on', 'firstname', 'lastname', 'last_order_at', 'last_order_value', 'total_orders_count', 'total_orders_value', 'average_sale','city', 'postcode', 'customer_group_id','company', 'country_id','last_bought_products','last_order_at_unix','telephone','fax','dob','gender','wishlist_items', 'unsubscription_link','data_last_updated_at', 'name');
    public $labels = array();

    public function __construct(){
        $this->labels = array(
            'customer_id'=>array('label'=>Mage::helper('sare')->__('Magento internal customer_id (entity_id)'), 'description'=>Mage::helper('sare')->__('')),
            'store_id'=>array('label'=>Mage::helper('sare')->__('Store ID in which customer registered in'), 'description'=>Mage::helper('sare')->__('')),
            'registered_on'=>array('label'=>Mage::helper('sare')->__('Register datetime'), 'description'=>Mage::helper('sare')->__('Holds date and time when customer has registered.')),
            'last_logged_in'=>array('label'=>Mage::helper('sare')->__('Last login date'), 'description'=>Mage::helper('sare')->__('Holds data when customer was logged in webshop.')),
            'unsubscription_link'=>array('label'=>Mage::helper('sare')->__('Unsubscribe URL'), 'description'=>Mage::helper('sare')->__('Contains unsubscribe URL, this field is required.')),
            'firstname'=>array('label'=>Mage::helper('sare')->__('Customer\'s firstname'), 'description'=>Mage::helper('sare')->__('Customer\'s firstname.')),
            'lastname'=>array('label'=>Mage::helper('sare')->__('Customer\'s lastname'), 'description'=>Mage::helper('sare')->__('Customer\'s lastname.')),
            'dob'=>array('label'=>Mage::helper('sare')->__('Date of birth'), 'description'=>Mage::helper('sare')->__('')),
            'gender'=>array('label'=>Mage::helper('sare')->__('Customer\'s gender'), 'description'=>Mage::helper('sare')->__('')),
            'company'=>array('label'=>Mage::helper('sare')->__('Customer\'s company'), 'description'=>Mage::helper('sare')->__('')),
            'postcode'=>array('label'=>Mage::helper('sare')->__('Customer\'s postcode (billing address)'), 'description'=>Mage::helper('sare')->__('')),
            'city'=>array('label'=>Mage::helper('sare')->__('Customer\'s city (billing address)'), 'description'=>Mage::helper('sare')->__('')),
            'telephone'=>array('label'=>Mage::helper('sare')->__('Customer\'s telephone number (billing address)'), 'description'=>Mage::helper('sare')->__('')),
            'fax'=>array('label'=>Mage::helper('sare')->__('Customer\'s fax number (billing address)'), 'description'=>Mage::helper('sare')->__('')),
            'country_id'=>array('label'=>Mage::helper('sare')->__('Customer\'s country id (f.e: DE, PL)'), 'description'=>Mage::helper('sare')->__('')),
            'customer_group_id'=>array('label'=>Mage::helper('sare')->__('Customer\'s group id'), 'description'=>Mage::helper('sare')->__('')),
            'last_order_at'=>array('label'=>Mage::helper('sare')->__('Last order datetime'), 'description'=>Mage::helper('sare')->__('Customer\'s lastname.')),
            'last_order_value'=>array('label'=>Mage::helper('sare')->__('Last order value (grand total)'), 'description'=>Mage::helper('sare')->__('Customer\'s last order value (grand total, incl. shipment, tax, dicount, etc)')),
            'total_orders_count'=>array('label'=>Mage::helper('sare')->__('Total orders count'), 'description'=>Mage::helper('sare')->__('')),
            'total_orders_value'=>array('label'=>Mage::helper('sare')->__('Total orders value'), 'description'=>Mage::helper('sare')->__('')),
            'average_sale'=>array('label'=>Mage::helper('sare')->__('Customer\'s average order value'), 'description'=>Mage::helper('sare')->__('')),
            'last_bought_products'=>array('label'=>Mage::helper('sare')->__('List of last bought items (sku1, sku2, ...)'), 'description'=>Mage::helper('sare')->__('')),
            'wishlist_items'=>array('label'=>Mage::helper('sare')->__('Products put in customer\'s wishlist (sku1, sku2, etc)'), 'description'=>Mage::helper('sare')->__('')),
            'data_last_updated_at'=>array('label'=>Mage::helper('sare')->__('Timestamp of last datachange'), 'description'=>Mage::helper('sare')->__('')),
        );
    }

    /**
     * Populates customer data and returns it in associative array
     * @param $customer Customer object for which data is populated
     * @return array Consumer's data array
     */
    public function populateCustomerData($customer){
        $data = array();
        foreach($this->_attributes as $attribute){
            if(is_object($customer)&&$customer->getId()){
                if($value = call_user_func(array($this, $attribute), $customer)){
                    $data[$attribute] = $value;
                } else {
                    // Well, nothing :)
                }
            } else {
                $data[$attribute] = call_user_func('Creativestyle_Sare_Model_Customer::'.$attribute, Mage::getModel('customer/customer'));
            }
        }
        // Zend_Debug::dump($data);die();
        return $data;
    }

    /**
     * Returns customer's unsubsubcription URL
     * @param Mage_Customer_Model_Customer $customer
     * @return string $unsubscroptionLink
     */
    static function unsubscription_link($customer){
        return Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail())->getUnsubscriptionLink();
    }

    /**
    * Returns last login date. If not logged in yet- retuns actual time
    * @param $customer
    * @return string
    */

    static function last_logged_in($customer){
         $version = Mage::getVersionInfo();

         $logCustomer = Mage::getModel('log/customer')->load($customer->getId(), 'customer_id');
         $lastVisited = $logCustomer->getLoginAtTimestamp();
         if(empty($lastVisited)){
             // Not logged in yet
             $lastVisited =  Mage::getModel('core/date')->date('Y-m-d H:i:s');
         } else {
             if($version['minor']<6){
                 $lastVisited =  Mage::getModel('core/date')->date('Y-m-d H:i:s', $lastVisited);
             } else {
                 $lastVisited = date('Y-m-d H:i:s', $lastVisited);
             }
         }
        return $lastVisited;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return datetime Datetime of last user login (Y-m-d H:i:s)
     */
    static function data_last_updated_at($customer){
        $now = Mage::getModel('core/date')->timestamp(time());
        return date('Y-m-d H:i:s', $now);
    }

    /**
     * Return customer's ID (entity_id)
     * @param Mage_Customer_Model_Customer $customer
     * @return int customer_id
     */
    static function customer_id($customer){
        return $customer->getId();
    }

    /**
     * Returns customer's firstname
     * @param Mage_Customer_Model_Customer $customer
     * @return string firstname
     */
    static function firstname($customer){
        return $customer->getFirstname();
    }

    /**
     * Returns customer's lastname
     * @param Mage_Customer_Model_Customer $customer
     * @return string lastname
     */
    static function lastname($customer){
        return $customer->getLastname();
    }

    /**
     * Returns customer's fullname
     * @param Mage_Customer_Model_Customer $customer
     * @return string lastname
     */
    static function name($customer){
        return $customer->getFirstname().' '.$customer->getLastname();
    }

    /**
     * Returns customer's  date-of-birth
     * @param Mage_Customer_Model_Customer $customer
     * @return string firstname
     */
     static function dob($customer){
        return $customer->getDob();
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return mixed Customer's gender or empty if nothing specified
     */
    static function gender($customer){
         if($customer->getGender()){
            return $customer->getGender();
         } else {
             return "";
         }
    }

    /**
     * Returns collction of orders for specified customer
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Sales_Order_Collection $orders
     */
    static function getAllOrders($customer){
        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', array('eq'=>$customer->getId()))->setOrder('created_at');
        return $orders;
    }

    /**
     * Returns last placed order or null (in case there are no order for this customer yet)
     * @param Mage_Customer_Model_Customer $customer
     * @return mixed datetime or null
     */
    static function getLastOrder($customer){
        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', array('eq'=>$customer->getId()))->setOrder('created_at');
        $order = $orders->getFirstItem();
        if($order->getId()){
            return $order;
        }
        return false;
    }

    /**
     * Returns datetime of last order for specified customer
     * @param Mage_Customer_Model_Customer $customer
     * @return datetime
     */
     static function last_order_at($customer){
        $order = self::getLastOrder($customer);
        if($order&&$order->getId()){
            $sTime = Mage::app()
                ->getLocale()
                ->date(strtotime($order->getCreatedAtStoreDate()), null, null, false)
                ->toString('YYYY-MM-dd H:m:s');

            return $sTime;
        }
        return '';
     }

    /**
     * Fetches list of SKUs for products placed in customer's wishlist.
     * @param Mage_Customer_Model_Customer$customer
     * @return string skus (separated by ,)
     */
    static function wishlist_items($customer){
        $wishList = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer);
        $wishListItemCollection = $wishList->getItemCollection();
        $arrProductSkus = array();
        if (count($wishListItemCollection)) {
            foreach ($wishListItemCollection as $item) {
                $product = $item->getProduct();
                $arrProductSkus[] = $product->getSku();
            }
        }
        return implode(',', $arrProductSkus);
     }

    /**
     * Returns last order's datetima as timestamp
     * @param $customer
     * @return int|string
     */
     static function last_order_at_unix($customer){
        $lastOrderAr = self::last_order_at($customer);
        if($lastOrderAr!=''){
            return strtotime($lastOrderAr);
        }
        return '';
     }

     /**
     * Returns last order value (grand total)
     * @param $customer
     * @return float order value (grand total)
     */
     static function last_order_value($customer){
        $order = self::getLastOrder($customer);
        if($order&&$order->getId()){
            return (float)$order->getGrandTotal();
        }
        return '';
     }

    /**
     * Returns customer registration datetime
     * @param Mage_Customer_Model_Customer $customer
     * @return mixed
     */
     static function registered_on($customer){
         if(!$customer->getCreatedAt()){
             return "";
         }


         $sTime = Mage::app()
            ->getLocale()
            ->date(strtotime($customer->getCreatedAt()), null, null, false)
            ->toString('YYYY-MM-dd H:m:s');
        return $sTime;
     }

    /**
     * Returns customer city (using default billing address)
     * @param Mage_Customer_Model_Customer $customer
     * @return string
     */
     static function city($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getCity();
        }
        return '';
     }

    /**
     * Returns storeID where customer has registered in
     * @param $customer
     * @return mixed
     */
    static function store_id($customer){
        return $customer->getStoreId();
    }

    /**
     * Returns websiteID where customer has registered in
     * @param $customer
     * @return mixed
     */
    static function website_id($customer){
        return $customer->getWebsiteId();
    }

    static function company($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getCompany();
        }
        return '';
    }

    /**
     * Returns customer's telephone number
     * @param $customer
     * @return string
     */

    static function telephone($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getTelephone();
        }
        return '';
     }

    /***
     * Returns customer's fax number
     * @param $customer
     * @return string
     */
    static function fax($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getFax();
        }
        return '';
    }

    /**
     * Returns customer's group ID
     * @param $customer
     * @return mixed
     */
    static function customer_group_id($customer){
        return $customer->getGroupId();
    }

    /**
     * Returns list of last bought products as comma separated list of SKUs
     * @param $customer
     * @return string
     */
    static function last_bought_products($customer){
        $orders = self::getAllOrders($customer);
        $items = array();
        foreach($orders as $order){
            foreach($order->getAllItems() as $item){
                $items[] = $item->getSku();
            }
        }

        return implode(',', $items);
    }

    /**
     * Returns customer's postcody (billing address)
     * @param $customer
     * @return string
     */
    static function postcode($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getPostcode();
        }
        return '';
    }

    /**
     * @param $customer
     * @return string
     */
    static function country_id($customer){
        if(is_object($customer->getDefaultBillingAddress())){
            return $customer->getDefaultBillingAddress()->getCountryId();
        }
        return '';
    }

     static function total_orders_count($customer){
        $orders = self::getAllOrders($customer);
        return $orders->getSize();
    }

     static function total_orders_value($customer){
        $orders = self::getAllOrders($customer);
        $value = 0;
        foreach($orders as $order){
            $value += $order->getGrandTotal();
        }
        return $value;
    }

     static function average_sale($customer){
        $orders = self::getAllOrders($customer);
        $value = 0;
        $count = 0;
        foreach($orders as $order){
            $value += $order->getGrandTotal();
            $count ++;
        }
        if($count>0){
            return round($value/$count,2);
        }
        return 0;
    }
}