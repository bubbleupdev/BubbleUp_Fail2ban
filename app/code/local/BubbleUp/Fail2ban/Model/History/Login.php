<?php
/**
 * Extends the default FireGento admin login reporting mechanism. We have to extend because the function we want to call is protected.
 *
 */
class BubbleUp_Fail2ban_Model_History_Login extends FireGento_AdminMonitoring_Model_Observer_Log
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function logLockout($userId, $username, $message)
    {
        /* @var $history FireGento_AdminMonitoring_Model_History */
        $history = Mage::getModel('firegento_adminmonitoring/history');
        $history->setForcedLogging(true);
        $history->setData(array(
            'object_id'   => $userId,
            'object_type' => get_class(Mage::getModel('admin/user')),
            'user_agent'  => $this->getUserAgent(),
            'ip'          => $this->getRemoteAddr(),
            'user_id'     => $userId,
            'user_name'   => $username,
            'action'      => FireGento_AdminMonitoring_Helper_Data::ACTION_LOGIN,
            'created_at'  => now(),
        ));

        // Add some error information when login failed
        $history->setData('status', FireGento_AdminMonitoring_Helper_Data::STATUS_FAILURE);
        $history->setData('history_message', $message);

        $history->save();
    }
}
