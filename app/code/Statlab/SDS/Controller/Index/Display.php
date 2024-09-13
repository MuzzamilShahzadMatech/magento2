<?php
namespace Statlab\SDS\Controller\Index;

class Display extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_sdsifuFactory;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Statlab\SDS\Model\sdsifuFactory $sdsifuFactory)
	{
		$this->_pageFactory = $pageFactory;
		$this->_sdsifuFactory = $sdsifuFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		return $this->_pageFactory->create();
	}
}