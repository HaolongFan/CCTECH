<?php

//http://cctech:8888/index.php/orderconfirm/ajax

class CCTECH_OrderConfirm_AjaxController extends Mage_Core_Controller_Front_Action
{

   public function indexAction()
   {      
      $result = Array('success' => true, 'message' =>  NULL, 'data' => NULL);

    $params = $this->getRequest()->getParams('productids');
    $this->getResponse()->setHeader('Content-type', 'application/json');
     
    $productids = explode("-", $params['productids']);
    
    $result['data'] = $productids;
    
     $cart = Mage::getSingleton('checkout/cart');
    /* We want to add only the product/products for this user and do so programmatically, so lets clear cart before we start adding the products into it */
    //$cart->truncate();
    //$cart->save();
    //$cart->getItems()->clear()->save();
    try {
    /* Add product with custom oprion? =>  some-custom-option-id-here: value to be read from database or assigned manually, hardcoded? Just example*/
    
      foreach($productids as $productid)
      {
          $_product = Mage::getModel('catalog/product');
          $_product->load($productid);
          $cart->addProduct($_product);
      }
      
      
      //$cart->addProductsByIds($productids);
      
      $cart->save();
    }
    catch (Exception $ex) {
       $result['success'] = false;
       $result['message'] = 'cart:'.$ex;
       echo json_encode($result); exit;
    }
   
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
        $result['success'] = false;
         $result['message'] = 'checkout: '.$ex;
         echo json_encode($result); exit;
    }
    
    /* Clear the cart */
    $cart->truncate();
    $cart->save();
    $cart->getItems()->clear()->save();
        
    echo json_encode($result);
   }
}

?>