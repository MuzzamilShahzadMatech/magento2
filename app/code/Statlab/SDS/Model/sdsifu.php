<?php 
namespace Statlab\SDS\Model;
class sdsifu extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'SDS_AND_IFU';

	protected $_cacheTag = 'SDS_AND_IFU';

	protected $_eventPrefix = 'SDS_AND_IFU';

	protected function _construct()
	{
		$this->_init('Statlab\SDS\Model\ResourceModel\sdsifu');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}