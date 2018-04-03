<?php

class BubbleUp_Fail2ban_Helper_Time
{
    public $defaultFormat = 'g:i:s A T \o\n D M jS, Y';
    public $defaultTimezone = 'America/Chicago';

    public function mysqlTimeToStoreTime($mysqlTimestamp)
    {
        $dateTime = $this->mysqlTimeToDateTimeObject($mysqlTimestamp);

        return $this->formatDateAsLocalTime($dateTime);
    }

    public function formatDateAsLocalTime($dateTimeObject)
    {
        $dateTime = clone $dateTimeObject;

        $dateTime = $this->convertDateTimeToLocal($dateTime);

        return $dateTime->format($this->defaultFormat);
    }

    public function convertDateTimeToLocal($dateTimeObject)
    {
        $storeTimezone = $this->getDefaultTimezone();
        
        $dateTimeObject->setTimezone(new DateTimeZone($storeTimezone));

        return $dateTimeObject;
    }

    public function mysqlTimeToDateTimeObject($mysqlTimestamp)
    {
        $dateTime = DateTime::createFromFormat("Y-m-d H:i:s", $mysqlTimestamp, new DateTimeZone('UTC'));

        if ($dateTime instanceof DateTime) {
            return $dateTime;
        }

        throw new Exception("Failed to convert mysql timestamp {$mysqlTimestamp} into a DateTime object. Errors:".print_r(DateTime::getLastErrors(), true));
    }

    public function getDefaultTimezone()
    {
        return Mage::getStoreConfig('general/locale/timezone') ?: $this->defaultTimezone;
    }
}
