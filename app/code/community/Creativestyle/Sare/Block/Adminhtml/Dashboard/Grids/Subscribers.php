<?php

class Creativestyle_Sare_Block_Adminhtml_Dashboard_Grids_Subscribers extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('subscribers');
        $this->setDefaultSort('subscriber_id', 'desc');
        $this->setDefaultLimit(10);
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        /* @var $collection Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
        $collection
            ->showCustomerInfo(true)
            ->addSubscriberTypeField()
            ->showStoreInfo();

        if($this->getRequest()->getParam('queue', false)) {
            $collection->useQueue(Mage::getModel('newsletter/queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('subscriber_id', array(
            'header'    => Mage::helper('newsletter')->__('ID'),
            'index'     => 'subscriber_id',
            'sortable'  => false
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('newsletter')->__('Email'),
            'index'     => 'subscriber_email',
            'sortable'  => false
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('newsletter')->__('Type'),
            'index'     => 'type',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => array(
                1  => Mage::helper('newsletter')->__('Guest'),
                2  => Mage::helper('newsletter')->__('Customer')
            )
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('newsletter')->__('Customer First Name'),
            'index'     => 'customer_firstname',
            'default'   =>    '----'
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('newsletter')->__('Customer Last Name'),
            'index'     => 'customer_lastname',
            'default'   =>    '----'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('newsletter')->__('Status'),
            'index'     => 'subscriber_status',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => Mage::helper('newsletter')->__('Not Activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED => Mage::helper('newsletter')->__('Unconfirmed'),
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Convert OptionsValue array to Options array
     *
     * @param array $optionsArray
     * @return array
     */
    protected function _getOptions($optionsArray)
    {
        $options = array();
        foreach ($optionsArray as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Retrieve Website Options array
     *
     * @return array
     */
    protected function _getWebsiteOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getWebsiteOptionHash();
    }

    /**
     * Retrieve Store Group Options array
     *
     * @return array
     */
    protected function _getStoreGroupOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getStoreGroupOptionHash();
    }

    /**
     * Retrieve Store Options array
     *
     * @return array
     */
    protected function _getStoreOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getStoreOptionHash();
    }
}