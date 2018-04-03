<?php

class BubbleUp_Fail2ban_Model_History
{
    use BubbleUp_Fail2ban_Trait_Log;

    public function getFailedLoginCollection($username=null, $timeframe='1 day ago UTC')
    {
        $collection = Mage::getModel('firegento_adminmonitoring/history')->getCollection();

        $collection
            ->addFieldToFilter('action', FireGento_AdminMonitoring_Helper_Data::ACTION_LOGIN)
            ->addFieldToFilter('status', FireGento_AdminMonitoring_Helper_Data::STATUS_FAILURE)
        ;

        if ($timeframe !== null) {
            if (is_numeric($timeframe)) {
                $epochSeconds = intval($timeframe);
            } else {
                $epochSeconds = strtotime($timeframe);
            }

            $timestamp = date("Y-m-d H:i:s", $epochSeconds);
            $collection->addFieldToFilter('created_at', ['gt' => $timestamp]);
        }

        if ($username !== null) {
            $collection->addFieldToFilter('user_name', $username);
        }

        return $collection;
    }
}
