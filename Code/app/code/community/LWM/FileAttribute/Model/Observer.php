<?php

class LWM_FileAttribute_Model_Observer
{
    public function addFileAttributeType(Varien_Event_Observer $observer) {
        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'lwm_file',
            'label' => Mage::helper('lwm_fileattribute')->__('File Upload'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',
                '_default_value',

                'is_searchable',
                'is_visible_in_advanced_search',
                'is_filterable',
                'is_filterable_in_search',
                'is_comparable',
                'is_used_for_promo_rules',
                'position',
                'used_in_product_listing',
                'used_for_sort_by',
            )
        );
        $response->setTypes($types);
        return $this;
    }

    public function assignBackendModelToAttribute(Varien_Event_Observer $observer) {
        $backendModel = 'lwm_fileattribute/attribute_backend_file';
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'lwm_file') {
            $object->setBackendModel($backendModel);
            $object->setBackendType('varchar');
        }
        return $this;
    }

    public function updateExcludedFieldList(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $attributes = Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('lwm_file');
        $list = array_merge($list, array_values($attributes));
        $block->setFormExcludedFieldList($list);
        return $this;
    }

    public function updateElementTypes(Varien_Event_Observer $observer) {
        $response = $observer->getEvent()->getResponse();

        $types = $response->getTypes();
        $types['lwm_file'] = Mage::getConfig()->getBlockClassName('lwm_fileattribute/element_file');

        $response->setTypes($types);

        return $this;
    }

    /*- Frontend -*/
    public function changeFileAttributeOutput(Varien_Event_Observer $observer) {
        $outputHelper = $observer->getHelper();
        $helper = Mage::helper('lwm_fileattribute');
        $outputHelper->addHandler('productAttribute', $helper);
    }
}