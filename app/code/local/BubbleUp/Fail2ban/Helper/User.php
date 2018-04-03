<?php

class BubbleUp_Fail2ban_Helper_User
{
    public function getAdminUserIdByUsername($username)
    {
        $collection = Mage::getModel('admin/user')->getCollection();

        $collection->addFieldToFilter('username', $username);

        $user = $collection->getFirstItem();

        if (!$user->getId()) {
            throw new Exception("Unable to locate admin user with username:{$username}");
        }

        return $user->getId();
    }
}
