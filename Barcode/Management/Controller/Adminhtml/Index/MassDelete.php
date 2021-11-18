<?php

namespace Barcode\Management\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Barcode\Management\Model\ResourceModel\Grid\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $_filter;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try{

            $logCollection = $this->_filter->getCollection($this->_collectionFactory->create());
            foreach ($logCollection as $item) {
                $item->delete();
            }
            $this->messageManager->addSuccess(__('Barcode Data Deleted Successfully.'));
        }catch(Exception $e){
            $this->messageManager->addError($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Barcode_Management::row_data_delete');
    }
}
