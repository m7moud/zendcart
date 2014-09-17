<?php

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Cart\Model\Cart;

class CartController extends AbstractActionController {

    protected $_cart;

    public function __construct() {
        $this->_cart = new Cart;
    }

    public function indexAction() {
        return new ViewModel(array(
            'cart' => $this->_cart
        ));
    }

    public function addAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $product = $this->getProductTable()->getProduct($id);
        $this->_cart->addItem($product, 1);
        return $this->redirect()->toRoute('cart', array('action' => 'index'));
    }

    private function getProductTable() {
        return $this->getServiceLocator()->get('Product\Model\ProductTable');
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        $product = $this->getProductTable()->getProduct($id);
        $this->_cart->removeItem($product);
        return $this->redirect()->toRoute('cart', array('action' => 'index'));
    }

}
