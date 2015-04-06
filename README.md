Magento integration with SARE e-mail marketing software.
=========================================================


Description
----------------------------------------------------------
Easily integrates SARE email marketing software with Magento. Synchronize newsletter subscribers, bought items and sales figures for ultimate control over your email marketing campaigns. Developed by [creativestyle](http://www.creativestyle.de/)

This extension synchronizes your subscribers with SARE email marketing software as well as detailed consumers' details.
Using [SAREscript](http://sare.pl/en/offer/5-sarescript)  you are able to create complex email marketing campaigns, segment customers and perform highly personlized and automated campaigns. Feel free to join your email marketing campaign with SMS messages channel.

Quick integration
----------------------------------------------------------

All you need to do is input your UID and integration key to get you going. If you don't have SARE account yet, you can [request free test account here](http://sare.pl/en/configuration/1).</a></p>
Integrates SARE e-mail marketing software. Synchronize your subscribers, customers and sales figures.

Installation
----------------------------------------------------------
Since we're available on http://packages.firegento.com you can install this extension using composer (more info about using composer in Magento projects - http://magebase.com/magento-tutorials/composer-with-magento/)

1. Add to repositories section to your composer.json file:
  ```
  "repositories": [
        {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ],
  ```
2. Type in your project root:
   ```php composer.phar require creativestyle/sare dev-master```

You can always [download .zip file](https://github.com/adamkarnowka/sare-magento-integration/archive/master.zip) or close this git or visit [Magento Connect](http://www.magentocommerce.com/magento-connect/sare-integration-1.html), extension key is:   ```http://connect20.magentocommerce.com/community/Creativestyle_Sare```.

For post installation help please check user's manual file - [english](https://github.com/adamkarnowka/magento-sare-integration/blob/master/SARE-users-manal_en.pdf?raw=true), [polish](https://github.com/adamkarnowka/magento-sare-integration/blob/master/SARE-users-manal_pl.pdf?raw=true).

Help needed? Please contact <emailmarketing@creativestyle.de>


Screenshots
----------------------------------------------------------
Main configuration panel:
![Alt text](https://raw.githubusercontent.com/adamkarnowka/sare-magento-extension/master/screenshots/1.png "Screenshot")

Advanced features to export:
![Alt text](https://raw.githubusercontent.com/adamkarnowka/sare-magento-extension/master/screenshots/2.png "Screenshot")

Batch processing screenshot:
![Alt text](https://raw.githubusercontent.com/adamkarnowka/sare-magento-extension/master/screenshots/4.png "Screenshot")

SMTP Integration screenshot:
![Alt text](https://raw.githubusercontent.com/adamkarnowka/sare-magento-extension/master/screenshots/3.png "Screenshot")
