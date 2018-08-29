![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module for X-Cart

## Support

This plugin supports X-Cart **4.4.0** to **4.7.x**

## Preparation

The usage of this module requires that you have obtained CardGate securitycredentials.  
Please visit [**My CardGate**](https://my.cardgate.com/) and retrieve your Site Id and Hash key, or contact your accountmanager.

## Installation

1. Download and unzip the most recent [**source code**](https://github.com/cardgate/x-cart/releases) file on your desktop.

2. Upload the **install_cardgateplus, include** and **payment** folders to the **root** folder of your webshop.

3. Open **http://mywebshop.com/install_cardgateplus** in your browser and install the plug-in.  
   (Replace **http://mywebshop.com** with the URL of your webshop)
   
4. For security reasons now delete the **install_cardgateplus** folder on your webshop.

## Configuration

1. Login to the **admin** section of your webshop.

2. Click on the **Settings** tab and select **Payment methods**.

3. Click on the **Configure** link of the **payment method** you wish to activate.

4. Enter the **site ID** and the **hash key**, which you can find at **Sites** on [**My CardGate**](https://my.cardgate.com/).

5. Select the **currency** you wish to use.

6. Enter the default **gateway language**.

7. If you wish to use the log option, check if the log directory on your website has **write privileges**.  
   For example the directory **http://mywebshop.com/payment/cardgateplus/logs** .

8. Click on the **Update** button and save the settings.

9. Again click on the **Settings** tab and click on **Payment methods**.

10. Select the payment method you have configured and change (if desired) the name of the payment method and the special instructions.  
    Now click on **Apply Changes**.

11. Repeat **step 3 to 10** for **every payment method** you wish to **activate**.

12. Go to [**My CardGate**](https://my.cardgate.com/), choose **Sites** and select the appropriate site.

13. Go to **Connection to the website** and enter the **Callback URL**  
    The Callback URL is displayed in every payment module and can be copied (and pasted) from there.

14. When you use the **same** site ID and hash key for your payment methods, you can use  
    **any** Callback URL of a CardGate payment method, as long as it is **active**.  
    This Callback will also correctly process the Callbacks to other CardGate payment methods.

15. For security reasons now delete the **install_cardgateplus** folder on your webshop.

16. When you are **finished testing** make sure that you switch **all activated payment methods** from  
    **Test mode** to **Active mode** and safe it (**Save**).

## Requirements

No further requirements.
