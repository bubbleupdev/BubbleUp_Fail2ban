<?php

class BubbleUp_Fail2ban_Model_Observer
{
    use BubbleUp_Fail2ban_Trait_Log;

    public function beforeAdminAuthenticate($observer)
    {
        $username = $observer->getUsername();

        $user = Mage::getModel('bubbleup_fail2ban/user');
        $user->username = $username;

        if ($user->isAllowedToLogIn()) {
            $this->log("Allowing login for user:{$username}");
            return;
        }

        $userIsWhitelisted = Mage::helper('bubbleup_fail2ban/whitelist')->isCurrentIpWhitelisted();
        if ($userIsWhitelisted) {
            Mage::getSingleton('core/session')->addWarning('Your account is locked due to too many failed login attempts, but your IP is in the whitelist, so you were allowed in anyway.');
            return;
        }

        $this->log("Not allowing login for user:{$username}");
        $this->onLoginBlocked($user);
    }

    public function onLoginFailure($observer)
    {
        $username = $observer->getData('user_name');

        $user = Mage::getModel('bubbleup_fail2ban/user');
        $user->username = $username;

        $remainingAttempts = $user->getRemainingAttempts() - 1; // This login just failed, so they are actually one less attempt than what is reflected in the DB.

        if ($remainingAttempts > 0) {
            Mage::getSingleton('core/session')->addNotice("You have {$remainingAttempts} more tries before your account will be locked.");
        } else {
            Mage::getSingleton('core/session')->addError("You have attempted to login too many times. Your account is now locked.");
        }

        // If the user doesn't exist, we must add our own log entry about the failure. FireGento_AdminMonitoring only adds this for existing accounts.
        if (!$user->existsInDatabase()) {
            Mage::helper('bubbleup_fail2ban/history')->recordLoginFailure($user, 'This account does not exist, so the login failed.');
        }
    }

    public function onLoginBlocked($user)
    {
        Mage::helper('bubbleup_fail2ban/history')->recordLoginFailure($user);

        Mage::getSingleton('core/session')->addError('Your account is locked due to too many failed login attempts.');

        Mage::helper('bubbleup_fail2ban/response')->respondWithHttpError();
    }
}
