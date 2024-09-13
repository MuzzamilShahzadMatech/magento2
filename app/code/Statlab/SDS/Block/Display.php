<?php
namespace Statlab\SDS\Block;

use Magento\Framework\View\Element\Template;
use Statlab\SDS\Model\ResourceModel\sdsifu\Collection;

class Display extends Template
{
	protected $_registry;
    private $collection;

    public function __construct(
        Template\Context $context,
		\Magento\Framework\Registry $registry,
        Collection $collection,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->collection = $collection;
		$this->_registry = $registry;
    }

    public function getALLpdf() {
        return $this->collection->setOrder('part','ASC');
    }
	
	public function searchSDS($s) {
        return $this->collection->addFieldToFilter('part', array('like' => '%'.$s.'%'))->setOrder('part','ASC');
    }
	
	public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }    

}