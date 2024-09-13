<?php
 
namespace Statlab\SDS\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	protected $_sdsifuFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Statlab\SDS\Model\sdsifuFactory $sdsifuFactory
		)
	{
		$this->_pageFactory = $pageFactory;
		$this->_sdsifuFactory = $sdsifuFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$sdsifu = $this->_sdsifuFactory->create();
		$collection = $sdsifu->getCollection();
		foreach($collection as $item){
			echo "<pre>";
			print_r($item->getData());
			echo "</pre>";
		}
		exit();
		return $this->_pageFactory->create();
	}
}