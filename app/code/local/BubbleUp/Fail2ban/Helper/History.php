<?php

class BubbleUp_Fail2ban_Helper_History
{
    public $_time;

    public function __construct()
    {
        $this->_time = Mage::helper('bubbleup_fail2ban/time');
    }

    public function recordLoginFailure($user, $defaultMessage="Login blocked due to too many attempts.")
    {
        $messages = [$defaultMessage];

        $numRecentFailures = $user->getNumberOfRecentFailures();

        if ($numRecentFailures) {
            $firstFailure = $user->getFirstFailureInTimeframe()->getCreatedAt();
            $firstFailureLocalized = $this->_time->mysqlTimeToStoreTime($firstFailure);

            $nextUnlock = $user->getNextUnlockTime();
            $nextUnlockLocalized = $this->_time->formatDateAsLocalTime($nextUnlock);

            $messages[] = "Recent failures: {$numRecentFailures} since {$firstFailureLocalized}. Account will unlock at {$nextUnlockLocalized}.";
        }
        
        try {
            $userId = $user->lookupEntityId();
        } catch (Exception $e) {
            $userId = 0;
            $messages[] = "Unable to look up admin user ID. Exception was:{$e->getMessage()}";
        }


        Mage::getModel('bubbleup_fail2ban/history_login')->logLockout($userId, $user->username, implode("\n", $messages));
    }
}
