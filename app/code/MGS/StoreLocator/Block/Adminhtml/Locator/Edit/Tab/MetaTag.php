<?php

namespace MGS\StoreLocator\Block\Adminhtml\Locator\Edit\Tab;

/**
 * Store locator edit form address tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class MetaTag extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_storeFactory;
    protected $_countryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('locator');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('locator_');

        $addressFieldset = $form->addFieldset('meta_tag_fieldset', ['legend' => __('Store Meta Tags')]);

        $addressFieldset->addField(
            'store_meta_title',
            'text',
            [
                'name' => 'store_meta_title',
                'label' => __('Store Meta Title'),
                'id' => 'store_meta_title',
                'title' => __('Store Meta Title'),
                'required' => false
            ]
        );

        $addressFieldset->addField(
            'store_meta_keyword',
            'text',
            [
                'name' => 'store_meta_keyword',
                'label' => __('Store Meta Keyword'),
                'id' => 'store_meta_keyword',
                'title' => __('Store Meta Keyword'),
                'required' => false
            ]
        );
    
        $addressFieldset->addField(
            'store_meta_description',
            'text',
            [
                'name' => 'store_meta_description',
                'label' => __('Store Meta Description'),
                'id' => 'store_meta_description',
                'title' => __('Store Meta Description'),
                'required' => false
            ]
        );

        $mapFieldset = $form->addFieldset('form_map', array('legend' => ''));
        $data = $model->getData();
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
