<?php

class LWM_FileAttribute_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function productAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params) {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $params['product'];
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $params['attribute']);
        if ($attribute && ($attribute->getFrontendInput() == 'lwm_file') && ($attributeValue = $product->getData($params['attribute']))) {
            $outputHtml = sprintf('<a href="%s" download>%s</a>', $this->escapeUrl(Mage::getBaseUrl('media') . 'catalog_sheet/file_save' . $attributeValue), Mage::helper('lwm_fileattribute')->__('Download'));
        }
        
       /*-
       	Try this in frontend
			<div class="std">
				<?php echo $_helper->productAttribute($_product, nl2br($_product->getCatalogSheet()), 'catalog_sheet') ?>
			</div>
       -*/
        return $outputHtml;
    }
}