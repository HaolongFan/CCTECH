<?php

//http://cctech:8888/index.php/orderconfirm/ajax

class CCTECH_OrderConfirm_AjaxController extends Mage_Core_Controller_Front_Action
{

   public function indexAction()
   {
      $data = json_encode("345");
      
      $products = $this->getRequest()->getParam('products');
      $this->getResponse()->setHeader('Content-type', 'application/json');
      
      $customer = Mage::getSingleton('customer/session')->getCustomer();
      
     $product = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToFilter('sku', 'ZE344565767')
    ->addAttributeToSelect('*')
    ->getFirstItem();
    
    $product->load($product->getId());
    
    $productids = Array($product->getId());
    
     $cart = Mage::getSingleton('checkout/cart');
    /* We want to add only the product/products for this user and do so programmatically, so lets clear cart before we start adding the products into it */
    $cart->truncate();
    $cart->save();
    $cart->getItems()->clear()->save();
    try {
    /* Add product with custom oprion? =>  some-custom-option-id-here: value to be read from database or assigned manually, hardcoded? Just example*/
      $cart->addProductsByIds($productids);
      $cart->save();
    }
    catch (Exception $ex) {
       echo $ex->getMessage();
    }
   //unset($product);
   
    $storeId = Mage::app()->getStore()->getId();
    $checkout = Mage::getSingleton('checkout/type_onepage');
    $checkout->initCheckout();
    $checkout->saveCheckoutMethod('register');
    $checkout->saveShippingMethod('flatrate_flatrate');
    $checkout->savePayment(array('method'=>'checkmo'));
    try {
        $checkout->saveOrder();
    }
    catch (Exception $ex) {
        echo $ex->getMessage();
    }
    
    /* Clear the cart */
    $cart->truncate();
    $cart->save();
    $cart->getItems()->clear()->save();
    
    $data = Array('customer' => $customer->getName(), 
    'product' => $product->getName(),
    'product ids' => $productids,
    'cart' => $cart->getItems(),
    'checkout' => get_class($checkout)
    ); 

    $this->getResponse()->setBody(json_encode($data));
   
   }
}

?>
