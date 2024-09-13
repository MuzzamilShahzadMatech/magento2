<?php
namespace Statlab\SDS\Model\ResourceModel\sdsifu;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'SDS_AND_IFU_collection';
	protected $_eventObject = 'sdsifu_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Statlab\SDS\Model\sdsifu', 'Statlab\SDS\Model\ResourceModel\sdsifu');
	}

}