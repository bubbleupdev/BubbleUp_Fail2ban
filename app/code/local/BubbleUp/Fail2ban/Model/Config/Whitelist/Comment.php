<?php

class BubbleUp_Fail2ban_Model_Config_Whitelist_Comment
{
    public function getCommentText()
    {
        $userIp = Mage::helper('core/http')->getRemoteAddr();

        return 'These comma-separated IPs will bypass brute-force protection.<br/><strong>Your IP is:'.$userIp."</strong>";
    }
}
