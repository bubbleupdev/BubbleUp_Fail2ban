# BubbleUp_Fail2ban

This module prevents brute-force password cracking attempts on the admin panel. With the default configuration, it will lock a user's account after 5 failed login attempts within any 60 minute period.

## Installation and Setup Instructions
Pre-setup: If you do not already have [FireGento_AdminMonitoring](https://github.com/firegento/firegento-adminmonitoring) installed, you'll need to install it first.

1. Download [the zip file](https://github.com/bubbleupdev/BubbleUp_Fail2ban/archive/master.zip), or `git clone` the repo
2. Copy all of the contents from the `BubbleUp_Fail2ban` directory into your Magento Root directory. This can be done with drag-and-drop. You do not need to copy the `LICENSE` and `README.md` files.
3. Clear caches if enabled.

To make sure it's working, attempt to log into the admin panel with an incorrect username or password. If the module is working, it will say that you only have 4 attempts left.

## Whitelisting
To allow some users to bypass the brute-force protection mechanism, you can ask them for their IP address and add it in the whitelist field under `System=>Configuration=>Advanced=>Admin=>"BubbleUp Fail2Ban : Brute force protection for admin logins"`. You can add as many IPs as you want, separated by commas.

## Configurability
By default, the module limits each user to `5` login attempts within any `60` minute time window. These limits are completely configurable in the admin panel. 

## FAQ

#### Can this module be used stand-alone, without also installing [FireGento_AdminMonitoring](https://github.com/firegento/firegento-adminmonitoring)?
Not currently. If you aren't already using the AdminMonitoring module, we highly recommend installing it. [PCI-DSS requirement 10](https://www.pcicomplianceguide.org/security-logging-and-monitoring-pci-dss-requirement-10-why-all-the-fuss/) states:
> Logging mechanisms and the ability to track user activities are critical in preventing, detecting and minimizing the impact of a data compromise.

#### I'm a developer and want this module to be decoupled from the [FireGento_AdminMonitoring](https://github.com/firegento/firegento-adminmonitoring) extension.
Great! This module is only very loosely coupled to the AdminMonitoring module. All you'll need to do is [override these two classes using the `<rewrite>` directive](http://inchoo.net/magento/overriding-magento-blocks-models-helpers-and-controllers/), and implement your own functions for [BubbleUp_Fail2ban_Model_History::getFailedLoginCollection($username, $startTime)](https://github.com/bubbleupdev/BubbleUp_Fail2ban/blob/master/app/code/local/BubbleUp/Fail2ban/Model/History.php) and optionally, [BubbleUp_Fail2ban_Model_History_Login::logLockout($userId, $username, $message)](https://github.com/bubbleupdev/BubbleUp_Fail2ban/blob/master/app/code/local/BubbleUp/Fail2ban/Model/History/Login.php).
