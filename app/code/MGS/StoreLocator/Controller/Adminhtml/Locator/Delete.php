<?php

namespace MGS\StoreLocator\Controller\Adminhtml\Locator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;

class Delete extends \Magento\Backend\App\Action {
    public function execute() {
        $locatorId = $this->getRequest()->getParam('id', false);
        if ($locatorId) {
            try {
                $locator = $this->_objectManager->create('MGS\StoreLocator\Model\Store')->load($locatorId);
                
                $url = "find-a-builder/index/view/id/".$locatorId;
                $filterData = [
			UrlRewriteService::TARGET_PATH => $url
		 ];
		 $rewriteFinder = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlFinderInterface')->findOneByData($filterData);
		 if($rewriteFinder){
                	$this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite')->load($rewriteFinder->getUrlRewriteId())->delete();
                }
                $locator->delete();
               
                $this->messageManager->addSuccess(__('A Store locator has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

}
