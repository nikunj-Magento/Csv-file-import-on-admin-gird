<?php

namespace Barcode\Management\Controller\Adminhtml\Add;

class Index extends \Magento\Backend\App\Action {

    public function execute() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

}

?>