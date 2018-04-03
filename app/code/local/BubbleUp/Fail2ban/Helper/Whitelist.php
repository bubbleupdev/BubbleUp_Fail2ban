<?php

class BubbleUp_Fail2ban_Helper_Whitelist
{
    public function isCurrentIpWhitelisted()
    {
        $currentIp = Mage::helper('core/http')->getRemoteAddr();

        return $this->isIpInWhitelist($currentIp);
    }

    public function isIpInWhitelist($ipAddress)
    {
        $whitelist = Mage::helper('bubbleup_fail2ban/config')->getWhitelistedIps();

        return in_array($ipAddress, $whitelist);
    }
}
