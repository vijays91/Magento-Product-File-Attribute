<?php

class LWM_FileAttribute_Block_Element_File extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data) {
        parent::__construct($data);
        $this->setType('file');
    }

    public function getElementHtml() {        
        $html = '';
        if ((string)$this->getValue()) {
            
            $url = $this->_getUrl();
            
            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = Mage::getBaseUrl('media') . $url;
            }

            $val = $this->getValue();
            $val = trim($this->getValue(), "/");
            $val = trim($val);
            
            $html = '<a href="' . $url . '"'
                . ' onclick="popWin(\'' . $url . '\',\'preview\',\'top:0,left:0,width=820,height=600,resizable=yes,scrollbars=yes\'); return false;">'
                . '<img src="' . Mage::getDesign()->getSkinUrl('images/fam_page_white.gif') . '" id="' . $this->getHtmlId() . '_image" title="' . $val . '"'
                . ' alt="' . $this->getValue() . '" height="16" width="16" class="small-image-preview v-middle" />'
                . '</a> ';
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }

    protected function _getDeleteCheckbox() {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('lwm_fileattribute')->__('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    protected function _getHiddenInput() {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    protected function _getUrl() {
        return 'catalog_sheet/file_save' . $this->getValue();
    }

    public function getName() {
        return $this->getData('name');
    }
}