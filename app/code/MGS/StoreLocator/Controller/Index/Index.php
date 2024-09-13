<?php

namespace MGS\StoreLocator\Controller\Index;

use MGS\StoreLocator\Model\StoreFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_storeFactory;
    
    public function __construct(Context $context, StoreFactory $storeFactory) {
        parent::__construct($context);
        $this->_storeFactory = $storeFactory;
    }

    public function execute() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Find Home Builders Near You | Wausau Homes'));
        $this->_view->getPage()->getConfig()->setDescription(__('Find a home builder for your custom dream home. Choose one of Wausau Homes design studios located throughout the Midwest & build your way, on time, at a firm price.'));
        $this->_view->renderLayout();
    }

}
