<?php

class BubbleUp_Fail2ban_Model_User
{
    use BubbleUp_Fail2ban_Trait_Log;

    public $username;
    protected $_failedLoginsCollection;

    public function getSampleSizeStartTime()
    {
        $minutesBack = Mage::helper('bubbleup_fail2ban/config')->getLoginFailureTimeframeMinutes();

        $currentTime = time();

        return $currentTime - ($minutesBack * 60);
    }

    public function existsInDatabase()
    {
        try {
            $userId = $this->lookupEntityId();
        } catch (Exception $e) {
            return false;
        }

        return !empty($userId) && is_numeric($userId);
    }

    public function lookupEntityId()
    {
        return Mage::helper('bubbleup_fail2ban/user')->getAdminUserIdByUsername($this->username);
    }

    public function isAllowedToLogIn()
    {
        $attemptsRemaining = $this->getRemainingAttempts();

        if ($attemptsRemaining < 1) {
            return false;
        }

        return true;
    }

    public function getNumberOfRecentFailures()
    {
        return $this->getFailedLogins()->getSize();
    }

    public function getFailedLogins()
    {
        if (!isset($this->_failedLoginsCollection)) {
            $this->_failedLoginsCollection = Mage::getModel('bubbleup_fail2ban/history')->getFailedLoginCollection($this->username, $this->getSampleSizeStartTime());
        }

        return $this->_failedLoginsCollection;
    }

    public function getFirstFailureInTimeframe()
    {
        return $this->getFailedLogins()->getFirstItem();
    }

    public function getNextUnlockTime()
    {
        $firstFailure = $this->getFirstFailureInTimeframe();
        $firstFailureTime = Mage::helper('bubbleup_fail2ban/time')->mysqlTimeToDateTimeObject($firstFailure->getCreatedAt());

        $minutesToAdd = Mage::helper('bubbleup_fail2ban/config')->getLoginFailureTimeframeMinutes();

        $nextUnlockTime = clone $firstFailureTime;
        $nextUnlockTime->modify("+{$minutesToAdd} minutes");

        return $nextUnlockTime;
    }
    public function getRemainingAttempts()
    {
        $numberOfRecentFailures = $this->getNumberOfRecentFailures();
        $failureLimit = Mage::helper('bubbleup_fail2ban/config')->getFailedLoginLimit();

        return $failureLimit - $numberOfRecentFailures;
    }
}
