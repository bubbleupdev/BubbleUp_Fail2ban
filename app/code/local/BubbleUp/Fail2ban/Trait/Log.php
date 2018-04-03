<?php
trait BubbleUp_Fail2ban_Trait_Log
{
    public function log($msg, $data=null, $level=Zend_Log::DEBUG)
    {
        Mage::log($msg, $level, $this->getLogFilename(), true);
        
        //Mage::log($this->__getMemoryUsage(), null, $this->getLogFilename(), true);

        if ($data!==null) {
            Mage::log($data, $level, $this->getLogFilename(), true);
        }

        // Call the class's database logger if it has one.
        if ((int)$level < (int)Zend_Log::DEBUG) {
            $this->__selfLog("Appending log message to the logger because log level is ".var_export($level, 1));
            
            $this->__logToDatabase($msg);
        } else {
            $this->__selfLog("Not sending this log entry to the logger because the level is ".var_export($level, 1). "and the required level is " .var_export(Zend_Log::DEBUG, 1));
        }
    }

    public function __logToDatabase($msg)
    {
        if (!isset($this->logger)) {
            $this->__selfLog("Tried to log a message to the database, but the logger could not be accessed.");
            return;
        }
        $this->logger->appendMessage($msg);
    }

    public function __selfLog($msg)
    {
        if (!isset($_GET['logger_diagnostic'])) {
            return;
        }

        Mage::log($msg, null, 'logutil.log', true);
    }
    public function throwException($msg)
    {
        Mage::log("Throwing exception: {$msg}", Zend_Log::CRIT, $this->getLogFilename(), true);
        
        // Call the class's database logger if it has one.
        if (isset($this->logger)) {
            $this->logger->appendMessage("Throwing exception: {$msg}");
        }

        throw new Exception($msg);
    }
    public function __getMemoryUsage()
    {
        static $memoryUsageLastTime = 0;

        $currentMemoryUsage = memory_get_usage(true);
        $memoryDifference = $currentMemoryUsage - $memoryUsageLastTime;
        
        $memoryUsageLastTime = $currentMemoryUsage;

        return "[ Mem:{$this->__bytesToHuman($currentMemoryUsage)} ] [ Change: {$memoryDifference} ]";
    }

    public function __bytesToHuman($bytes)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($bytes/pow(1024, ($i=floor(log($bytes, 1024)))), 4).' '.$unit[$i];
    }
    public function getLogFilename()
    {
        if (isset($this->logFilename) && !empty($this->logFilename)) {
            return $this->logFilename;
        }

        return 'bubbleup_fail2ban.log';
    }
}
