<?php

class BubbleUp_Fail2ban_Helper_Config
{
    public function getFailedLoginLimit()
    {
        return intval(Mage::getStoreConfig('admin/bubbleup_fail2ban/max_failed_logins')) ?: 5;
    }

    public function getLoginFailureTimeframeMinutes()
    {
        return intval(Mage::getStoreConfig('admin/bubbleup_fail2ban/lockout_minutes')) ?: 60;
    }

    public function getWhitelistedIps()
    {
        $commaSeparatedIps = Mage::getStoreConfig('admin/bubbleup_fail2ban/ip_whitelist');

        $ipArray = explode(',', $commaSeparatedIps);
        $ipArray = array_map('trim', $ipArray);

        return $ipArray;
    }
}
