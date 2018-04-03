<?php

class BubbleUp_Fail2ban_Helper_Response
{
    public function respondWithHttpError()
    {
        $redirectUrl = Mage::helper("adminhtml")->getUrl("/");
        
        Mage::app()->getResponse()
            ->clearHeaders()
            ->setHttpResponseCode(401)
            ->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('HTTP/1.0', '401 Unauthorized')
            ->setBody("<meta http-equiv='refresh' content='0; url={$redirectUrl}'>Please wait...")
            ->sendResponse()
        ;
        exit;
    }
}
