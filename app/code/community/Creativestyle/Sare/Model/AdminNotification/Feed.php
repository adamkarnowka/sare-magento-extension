<?php

class Creativestyle_Sare_Model_AdminNotification_Feed extends Mage_AdminNotification_Model_Feed {

    /**
     * Returns feed URL
     * @return string URL where feed is located under
     */

    public function getFeedUrl() {
        $url = Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://www.creativestyle.de/email-marketing/feed.rss';
        return $url;
    }


    /**
     * Performs feed update
     */
    public function observe() {
        $this->checkUpdate();
    }
}


