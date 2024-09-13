<?php

namespace MGS\StoreLocator\Controller\Index;

use MGS\StoreLocator\Model\StoreFactory;
use Magento\Framework\App\Action\Context;

class BuilderState extends \Magento\Framework\App\Action\Action
{

    protected $_storeFactory;
    protected $_context;

    public function __construct(Context $context, StoreFactory $storeFactory)
    {
        parent::__construct($context);
        $this->_storeFactory = $storeFactory;
        $this->_context = $context->getUrl();
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams('state');
        if (empty($data) || empty($data['state'])) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl($this->_context->getUrl('find-a-builder'));
            return $redirect;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $title = ucfirst($data['state']) . ' Home Builders - Custom New Homes in ' . ucfirst($data['state']) . ' | Wausau Homes';
        $this->_view->getPage()->getConfig()->getTitle()->set(__($title));
        $this->_view->renderLayout();
    }
}
