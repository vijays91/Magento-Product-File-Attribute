<?php

class Mage_Core_Model_File_Uploader extends Varien_File_Uploader
{
    protected $_skipDbProcessing = false;
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }
        $helper = Mage::helper('core/file_storage');
        if ($helper->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }
        $dbHelper = Mage::helper('core/file_storage_database');
        $this->_result['file'] = $dbHelper->saveUploadedFile($result);

        return $this;
    }

    public function skipDbProcessing($flag = null)
    {
        if (is_null($flag)) {
            return $this->_skipDbProcessing;
        }
        $this->_skipDbProcessing = (bool)$flag;
        return $this;
    }

    public function checkAllowedExtension($extension)
    {
        $validator = Mage::getSingleton('core/file_validator_notProtectedExtension');
        if (!$validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /*- -*/    
    public function save($destinationFolder, $newFileName = null, $file_type = null)
    {
        $this->_validateFile();
        if ($this->_allowCreateFolders) {
            $this->_createDestinationFolder($destinationFolder);
        }
        if (!is_writable($destinationFolder)) {
            throw new Exception('Destination folder is not writable or does not exists.');
        }
        $this->_result = false;
        $destinationFile = $destinationFolder;
        $fileName = isset($newFileName) ? $newFileName : $this->_file['name'];
        $fileName = self::getCorrectFileName($fileName);
        if ($this->_enableFilesDispersion) {
            $fileName = $this->correctFileNameCase($fileName);
            $this->setAllowCreateFolders(true);
            $this->_dispretionPath = self::getDispretionPath($fileName);
            /*- -*/
            if($file_type == "file_type") { // hide the product name first 2 letter folder creation.
                $this->_dispretionPath = "";                
            }
            /*- -*/
            $destinationFile.= $this->_dispretionPath;
            $this->_createDestinationFolder($destinationFile);
        }
        if($this->_allowRenameFiles) {
            $fileName = self::getNewFileName(self::_addDirSeparator($destinationFile) . $fileName);
        }
        $destinationFile = self::_addDirSeparator($destinationFile) . $fileName;
        $this->_result = $this->_moveFile($this->_file['tmp_name'], $destinationFile);
        if ($this->_result) {
            chmod($destinationFile, 0666);
            if ($this->_enableFilesDispersion) {
                $fileName = str_replace(DIRECTORY_SEPARATOR, '/',
                    self::_addDirSeparator($this->_dispretionPath)) . $fileName;
            }
            $this->_uploadedFileName = $fileName;
            $this->_uploadedFileDir = $destinationFolder;
            $this->_result = $this->_file;
            $this->_result['path'] = $destinationFolder;
            $this->_result['file'] = $fileName;
            $this->_afterSave($this->_result);
        }
        return $this->_result;
    }
    /*- -*/

    /*- -*/
    private function _createDestinationFolder($destinationFolder) {
        if (!$destinationFolder) {
            return $this;
        }

        if (substr($destinationFolder, -1) == DIRECTORY_SEPARATOR) {
            $destinationFolder = substr($destinationFolder, 0, -1);
        }

        if (!(@is_dir($destinationFolder) || @mkdir($destinationFolder, 0777, true))) {
            throw new Exception("Unable to create directory '{$destinationFolder}'.");
        }
        return $this;
    }
    /*- -*/
}
