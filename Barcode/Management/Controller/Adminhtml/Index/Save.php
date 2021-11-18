<?php

namespace Barcode\Management\Controller\Adminhtml\Index;

class Save extends \Magento\Backend\App\Action {

    var $gridFactory;
    protected $_resource;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $resource, \Barcode\Management\Model\GridFactory $gridFactory, \Barcode\Management\Model\GridDataFactory $gridData
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->_gridDataFactory = $gridData;
        $this->_resource = $resource;
    }

    public function execute() {
        $data = $this->getRequest()->getPostValue();
        $querymodel = $this->_gridDataFactory->create();
        if (!$data) {
            $this->_redirect('barcode_management/index/addrow');
            return;
        }
        try {
            $rowData = $this->gridFactory->create();
            if (isset($data['id'])) {
                $id = $data['id'];
                $result = $querymodel->getSelectById($id);
                $fetch_barcode = $result[0]['barcode'];
                if ($fetch_barcode != $data['barcode']) {
                    $barcode = $data['barcode'];
                    $total_rows = $querymodel->getUniqueBarcode($barcode, $id);
                    if ($total_rows >= 1) {
                        $this->messageManager->addError(__('Barcode must be unique'));
                    } else {
                        $rowData->setData($data);
                        if (isset($data['id'])) {
                            $rowData->setEntityId($data['id']);
                        }
                        $rowData->save();
                        $this->messageManager->addSuccess(__('Barcode data has been successfully saved.'));
                    }
                } else {
                    $rowData->setData($data);
                    if (isset($data['id'])) {
                        $rowData->setEntityId($data['id']);
                    }
                    $rowData->save();
                    $this->messageManager->addSuccess(__('Barcode data has been successfully saved.'));
                }
            } else {
                $barcode = $data['barcode'];
                $unique_barcode = $querymodel->getSelectByBarcode($barcode);
                if ($unique_barcode >= 1) {
                    $this->messageManager->addError(__('Barcode must be unique'));
                } else {
                    $rowData->setData($data);
                    if (isset($data['id'])) {
                        $rowData->setEntityId($data['id']);
                    }
                    $rowData->save();
                    $this->messageManager->addSuccess(__('Barcode data has been successfully saved.'));
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('barcode_management/index/index');
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Barcode_Management::save');
    }

}
