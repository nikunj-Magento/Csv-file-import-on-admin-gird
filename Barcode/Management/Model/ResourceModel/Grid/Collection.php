<?php

namespace Barcode\Management\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            'Barcode\Management\Model\Grid',
            'Barcode\Management\Model\ResourceModel\Grid'
        );
    }
}
