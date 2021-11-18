<?php

namespace Barcode\Management\Block\Adminhtml\Grid\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('barcodegrid_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Barcode Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'barcode',
            'text',
            [
                'name' => 'barcode',
                'label' => __('Barcode'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'power',
            'text',
            [
                'name' => 'power',
                'label' => __('Power'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'color',
            'text',
            [
                'name' => 'color',
                'label' => __('color'),
                'class' => 'validate-alphanum-with-spaces',
                'required' => false
            ]
        );
        $fieldset->addField(
            'sku',
            'text',
            [
                'name' => 'sku',
                'label' => __('sku'),
                'required' => true
            ]
        );


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
