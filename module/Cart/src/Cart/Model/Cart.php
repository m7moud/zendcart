<?php

namespace Cart\Model;
use Zend\Session\Container;

class Cart {

    protected $_items = array();
    protected $_sessionNamespace = NULL;

    public function __construct() {
        if (isset($this->getSession()->items)) {
            $this->_items = $this->getSession()->items;
        }
    }

    public function addItem($product, $qty) {
        if (0 > $qty) {
            return false;
        }
        if (0 == $qty) {
            $this->removeItem($product);
            return false;
        }
        $item = array(
            'product' => $product,
            'qty' => $qty
        );
        $this->_items[$product->id] = $item;
        $this->persist();
        return $item;
    }

    public function removeItem($product) {
        if (is_int($product)) {
            unset($this->_items[$product]);
        }
        if ($product) {
            unset($this->_items[$product->id]);
        }
        $this->persist();
    }

    public function getItems() {
        return $this->_items;
    }

    protected function setSession() {
        $this->_sessionNamespace = new Container(__NAMESPACE__);
    }

    protected function getSession() {
        if (NULL === $this->_sessionNamespace) {
            $this->setSession();
        }
        return $this->_sessionNamespace;
    }

    protected function persist() {
        $this->getSession()->items = $this->_items;
    }

}
