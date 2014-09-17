<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Product\Model\Product;
use Product\Form\ProductForm;

class ProductController extends AbstractActionController {

    protected $productTable;

    private function getProductTable() {
        if (!$this->productTable) {
            $sm = $this->getServiceLocator();
            $this->productTable = $sm->get('Product\Model\ProductTable');
        }
        return $this->productTable;
    }

    public function indexAction() {
        return new ViewModel(array(
            'products' => $this->getProductTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $form = new ProductForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = new Product();
            $form->setInputFilter($product->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $product->exchangeArray($form->getData());
                $this->getProductTable()->saveProduct($product);
                return $this->redirect()->toRoute('product');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('product', array(
                        'action' => 'add'
            ));
        }
        try {
            $product = $this->getProductTable()->getProduct($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('product', array(
                        'action' => 'index'
            ));
        }
        $form = new ProductForm();
        $form->bind($product);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($product->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getProductTable()->saveProduct($product);
                return $this->redirect()->toRoute('product');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('product');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getProductTable()->deleteProduct($id);
            }
            return $this->redirect()->toRoute('product');
        }
        return array(
            'id' => $id,
            'product' => $this->getProductTable()->getProduct($id)
        );
    }

}
