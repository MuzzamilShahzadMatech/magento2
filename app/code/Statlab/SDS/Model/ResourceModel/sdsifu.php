<?php
namespace Statlab\SDS\Model\ResourceModel;


class sdsifu extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('SDS_AND_IFU', 'id');
	}
	
}