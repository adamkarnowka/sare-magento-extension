<?php
/**
 * Author: Adam Karnowka <adam.karnowka@creativestyle.pl>
 * Date: 23.06.13 Time: 11:33
 * For questions, issues - please visit: http://www.creativestyle.de/email-marketing.html
 */

class Creativestyle_Sare_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Sends notification mail to addresses defined in backend.
     *
     * @param string $title
     * @param string $description
     * @param array $modelData
     * @param string $apiUrl
     */

    public function sendProblemNotification($title, $description, $modelData, $apiUrl){

        if(!Mage::getStoreConfig('sare/settings/send_problem_mails')){
            return false;
        }

        Mage::getDesign()->setArea('frontend');
        $dynamicContent = array(
            '%TITLE%'=> Mage::helper('sare')->__($title),
            '%MESSAGE%'=> Mage::helper('sare')->__($description),
            '%REQUEST%'=> $modelData,
            '%FOOTER%'=>Mage::helper('sare')->__('You receive these emails, because your address was entered in Magento configuration panel. <br/>You can disable them in Magento backend - Newsletter / SARE integration / Settings. <br/><br/>Have a nice day!<br/>SARE team.'),
            '%API_URL%'=>trim($apiUrl)
        );

        $htmlBlock = Mage::app()->getLayout()->createBlock('core/template')->setTemplate('sare/problemnotification.phtml')->toHtml();
        foreach($dynamicContent as $placeholder=>$text){
            if(is_array($text)){
                $text = ltrim(print_r($text, true));
            }
            $htmlBlock = str_replace($placeholder, $text, $htmlBlock);
        }

        $email = Mage::getModel('core/email_template');
        $email->setSenderEmail(Mage::getStoreConfig('sare/settings/notification_senderemail'));
        $email->setSenderName(Mage::getStoreConfig('sare/settings/notification_sendername'));
        $email->setTemplateSubject(Mage::helper('sare')->__('SARE integration problem notification'));
        $email->setTemplateText(str_replace("                                    ","",$htmlBlock));

        $receivers = explode(",", Mage::getStoreConfig('sare/settings/exceptions_sent_to'));
        foreach($receivers as $receiver){
            $name = Mage::getStoreConfig('trans_email/'.$receiver.'/name');
            $emailAddress = Mage::getStoreConfig('trans_email/'.$receiver.'/email');
            $email->send($emailAddress, $name);
        }
    }
}