<?php

/* originally from icingaweb2-module-businessprocess | (c) Icinga GmbH | GPLv2 */

namespace Icinga\Module\Oidc\Forms;

use Icinga\Web\Form;

class FileUploadForm extends Form
{
    protected $path;

    public function setUploadPath($path)
    {
        $this->path= $path;
        return $this;
    }

    public function createElements(array $formData)
        {

        $this->setAttrib('enctype', 'multipart/form-data');

        $this->setAttrib('class', $this->getAttrib('class')." "."fileupload");


        $this->addElement('file', 'uploaded_file', array(
            'label'       => $this->translate('File'),
            'destination' => $this->getTempDir(),
            'required'    => true,

        ))->ad;

        /** @var \Zend_Form_Element_File $el */
        $el = $this->getElement('uploaded_file');
        $el->setValueDisabled(true);

        $this->setSubmitLabel(
            $this->translate('Next')
        );
    }

    protected function getTempDir()
    {
        return sys_get_temp_dir();
    }

    protected function processUploadedSource()
    {
        /** @var \Zend_Form_Element_File $el */
        $el = $this->getElement('uploaded_file');
        $mime= $el->getMimeType();
            if(is_array($mime)){
                throw new \Exception('Only one file is allowed!');
            }
            if(! is_string($mime)){
                throw new \Exception('Don\' do this again!');
            }
            if(strpos($mime,'image') === false){
                throw new \Exception('Only Images are allowed!');
            }
            if ($el) {
                $arr = explode(DIRECTORY_SEPARATOR,$el->getFileName());
                $newfile=array_pop($arr);
                $newfile = $this->path.DIRECTORY_SEPARATOR.$newfile;
                if(file_exists($newfile)){
                    unlink($newfile);
                }
                // TODO: race condition, try to do this without unlinking here

                $el->addFilter('Rename', $newfile);
                if ($el->receive()) {
                    $this->setRedirectUrl('oidc/file');

                } else {
                    foreach ($el->file->getMessages() as $error) {
                        $this->addError($error);
                    }
                }
            }



        return $this;
    }

    public function onSuccess()
    {
        $this->processUploadedSource();

        parent::onSuccess();
    }
}
