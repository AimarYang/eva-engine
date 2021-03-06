<?php

namespace Blog\Model;

use Eva\Api,
    Eva\Mvc\Model\AbstractModel;

class Category extends AbstractModel
{
    protected $itemTableName = 'Blog\DbTable\Categories';

    protected $events = array(
    );

    
    protected $treeConfig = array(
        'adapter'   => 'BinaryTreeDb',
        'direction' => false,
        'options'   => array(
           'dbTable' => 'Blog\DbTable\Categories',
        ),
    );

    public function createCategory()
    {
        $item = $this->setItemAttrMap(array(
            'urlName' => array('urlName', 'getUrlName'),
            'createTime' => array('createTime', 'getCreateTime'),
        ))->getItemArray();

        $tree = new \Core\Tree\Tree($this->treeConfig['adapter'], $this->treeConfig['direction'], $this->treeConfig['options']);

        $itemId = $tree->insertNode($item);

        if($itemId){
            $item['id'] = $itemId;
            $this->item = $item;
        }
        
        if($itemId && $this->getSubItem('FileConnect')){
            $subData = $this->getSubItem('FileConnect');
            $subTable = Api::_()->getDbTable('File\DbTable\FilesConnections');
            $subItem = $this->getItemClass($subData, array(
                'connect_id' => array('connect_id', 'getConnectId'),
                'connectType' => array('connectType', 'getConnectType')
            ), 'File\Model\FileConnect\Item');
            $subData = $subItem->toArray();
            $subTable->where(array('connect_id' => $itemId, 'connectType' => 'category'))->remove();
            if($subData['connect_id'] && $subData['file_id']) {
                $subTable->where(array('connect_id' => $itemId, 'connectType' => 'category'))->create($subData);
            }
        }

        return $itemId;
    }

    public function saveCategory()
    {
        $item = $this->setItemAttrMap(array(
            'urlName' => array('urlName', 'getUrlName'),
        ))->getItemArray();
        
        $tree = new \Core\Tree\Tree($this->treeConfig['adapter'], $this->treeConfig['direction'], $this->treeConfig['options']);
        $itemId = $tree->updateNode($item);

        if($itemId){
            $item['id'] = $itemId;
            $this->item = $item;
        }

        if($itemId && $this->getSubItem('FileConnect')){
            $subData = $this->getSubItem('FileConnect');
            $subTable = Api::_()->getDbTable('File\DbTable\FilesConnections');
            $subItem = $this->getItemClass($subData, array(
                'connect_id' => array('connect_id', 'getConnectId'),
                'connectType' => array('connectType', 'getConnectType')
            ), 'File\Model\FileConnect\Item');
            $subData = $subItem->toArray();
            $subTable->where(array('connect_id' => $itemId, 'connectType' => 'category'))->remove();
            if($subData['connect_id'] && $subData['file_id']) {
                $subTable->where(array('connect_id' => $itemId, 'connectType' => 'category'))->create($subData);
            }
        }

        return $itemId;
    }

    public function deleteCategory()
    {
        $item = $this->getItemArray();

        $tree = new \Core\Tree\Tree($this->treeConfig['adapter'], $this->treeConfig['direction'], $this->treeConfig['options']);
        $itemId = $tree->deleteNode($item);

        if($itemId){
            $item['id'] = $itemId;
            $this->item = $item;
        }
        $subTable = Api::_()->getDbTable('File\DbTable\FilesConnections');
        $subTable->where(array('connect_id' => $itemId, 'connectType' => 'category'))->remove();

        return $itemId;
    }

    public function getCategory()
    {
        $params = $this->getItemParams();

        if(!$params || !(is_numeric($params) || is_string($params))){
            throw new \Core\Model\Exception\InvalidArgumentException(sprintf(
                '%s params %s not correct',
                __METHOD__,
                $params
            ));
        }

        $itemTable = $this->getItemTable();

        if(is_numeric($params)){
            $this->item = $category = $itemTable->where(array('id' => $params))->find('one');
        } else {
            $this->item = $category = $itemTable->where(array('urlName' => $params))->find('one');
        }

        if($category) {
            $this->item = $category = $this->setItemAttrMap(array(
                'FileConnect' => array(
                    'connect_id' => null,
                ),
                'File' => array(
                    'connect_id' => null,
                ),
            ))->getItemArray();
        }

        return $this->item = $category;
    }

    public function getCategories()
    {
        $defaultParams = array(
            'enableCount' => true,
            'page' => 1,
            'order' => 'iddesc',
        );
        $params = $this->getItemListParams();
        $params = new \Zend\Stdlib\Parameters(array_merge($defaultParams, $params));

        $tree = new \Core\Tree\Tree($this->treeConfig['adapter'], $this->treeConfig['direction'], $this->treeConfig['options']);
        $categories = $tree->getTree(); 

        $res = array();

        if ($categories) {
            foreach ($categories as $cateArray) {
                $category = $this->setItemParams($cateArray['id'])->getCategory(); 
                $category['level'] = $cateArray['level'];
                $res[] = $category;
            }    
        }

        return $this->itemList = $res;
    }
}
