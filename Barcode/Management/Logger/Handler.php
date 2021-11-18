<?php

namespace Barcode\Management\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    public $loggerType = Logger::INFO;

    public $fileName = '/var/log/exception.log';
}
