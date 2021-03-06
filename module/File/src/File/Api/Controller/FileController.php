<?php
namespace File\Api\Controller;

use File\Form,
    Eva\Api,
    Eva\Mvc\Controller\RestfulModuleController,
    Zend\View\Model\JsonModel;

class FileController extends RestfulModuleController
{
    public function restIndexFile()
    {
        $request = $this->getRequest();

        $query = $request->getQuery();

        $form = Api::_()->getForm('File\Form\FileSearchForm');
        $selectQuery = $form->fieldsMap($query, true);

        $fileModel = Api::_()->getModel('File\Model\File');
        $files = $fileModel->setItemListParams($selectQuery)->getFiles();
        $paginator = $fileModel->getPaginator();
        $this->layout('layout/adminblank');

        return array(
            'form' => $form,
            'files' => $files,
            'query' => $query,
            'paginator' => $paginator,
        );
    }

    public function restPostFile()
    {
        $request = $this->getRequest();
        $postData = $request->getPost();
        $form = new Form\UploadForm();
        $form->init()
             ->setData($postData)
             ->enableFilters()
             ->enableFileTransfer();

        $fileModel = Api::_()->getModel('File\Model\File');
        $response = array();
        if ($form->isValid() && $form->getFileTransfer()->isUploaded()) {
            if($form->getFileTransfer()->receive()){
                $files = $form->getFileTransfer()->getFileInfo();
                $fileModel->setUploadFiles($files);
                $fileModel->setConfigKey('default')->createFiles();
                $lastFileId = $fileModel->getLastFileId();
                if($lastFileId) {
                    $fileinfo = $fileModel->setItemParams($lastFileId)->getFile();
                    $file = array(
                        'id' => $fileinfo['id'],
                        'name' => $fileinfo['originalName'],
                        'size' => (int)$fileinfo['fileSize'],
                        'url' => $fileinfo['Url'],
                        'thumbnail_url' => $fileinfo['Thumb'],
                    );
                    $response = array(
                        $file
                    );
                }
            }
        } else {
        }

        return new JsonModel($response);
    }

}
