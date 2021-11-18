<?php

namespace Barcode\Management\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddtoCartAfter implements ObserverInterface {

    private $connection;
    private $attributeSet;
    private $checkout;
    private $serialize;
    private $logger;
    private $configurable;
    private $resourceProduct;
    private $messageManager;
    CONST ATTRIBUTE_SET_NAME = 'Contact Lens';

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Checkout\Model\Cart $checkout,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Psr\Log\LoggerInterface $logger,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable  $configurable,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\Framework\Message\ManagerInterface $messageManager

    ) {
        $this->connection = $resource->getConnection();
        $this->attributeSet = $attributeSet;
        $this->checkout = $checkout;
        $this->serialize = $serialize;
        $this->logger = $logger;
        $this->configurable = $configurable;
        $this->resourceProduct = $resourceProduct;
        $this->messageManager = $messageManager;

    }

    public function execute(Observer $observer)
    {
        
       
        $product = $observer->getEvent()->getProduct();
        
        if($product) {

           //$product = $observer->getEvent()->getProduct();
           $ProductAttributeSetName =  $this->getProductAttributeSetNameById($product);
            try {
                if(self::ATTRIBUTE_SET_NAME==$ProductAttributeSetName){
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/barcode.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $item = $this->checkout->getQuote()->getItemByProduct($product);
                    $logger->info(print_r($item->getProduct()->getTypeId(),true));
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $optionsArray = [];

                    if (isset($options['options']) && !empty($options['options'])) {
                        
                        foreach ($options['options'] as $option) {
                            
                            if($value = $this->getProductOptionNameById($option['option_value'],$product)) {

                                /*$val = $this->checkPower($value,$optionsArray);*/
                                
                                if($item->getProduct()->getTypeId()=='simple'){

                                    if($newBarcodesArray = $this->getBarcodeBy($item->getProduct()->getSku(),$value)) {
                                       
                                        if(isset($newBarcodesArray['barcode']) &&  !empty($newBarcodesArray['barcode'])){
                                            $newBarcodes = $newBarcodesArray['barcode'];
                                            $buyRequest = $item->getOptionByCode('info_buyRequest');
                                            $buyRequestArr = $this->serialize->unserialize($buyRequest->getValue());

                                            if(isset($buyRequestArr['barcode']) && !empty($buyRequestArr['barcode'])){

                                                $oldBarcodes  = $buyRequestArr['barcode'];
                                                $oldBarcodesArray = explode(',', $oldBarcodes);
                                                $newBarcodesArray = explode(',', $newBarcodes);
                                                $barcodesArray = array_unique(array_merge($oldBarcodesArray,$newBarcodesArray));
                                                $barcodes = implode($barcodesArray, ',');
                                                $buyRequestArr['barcode'] = $barcodes;

                                            } else {
                                               
                                                $buyRequestArr['barcode'] = $newBarcodes;
                                            }
                                            
                                            $buyRequest->setValue($this->serialize->serialize($buyRequestArr));
                                            $buyRequest->save();
                                        }
                                        
                                    }
                                }

                                if($item->getProduct()->getTypeId() =='configurable') {
                                    
                                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/barcode1.log');
                                    $logger = new \Zend\Log\Logger();
                                    $logger->addWriter($writer);
                                    

                                    
                                    if($value=='Without Power') { 
                                        $optionsArray[] = 'Without Power';
                                    }

                                    if($value=='With Power') { 
                                        $optionsArray[] = 'With Power';

                                    }
                                    
                                    if(in_array('With Power', $optionsArray) ) { 

                                        if($value != 'With Power'){
                                            
                                            $optionsArray['With Power'] = $value;
                                        }
                                    }

                                    if($value=='I need two different powers') { 
                                        unset($optionsArray);
                                        $optionsArray[]= $value;
                                    }

                                    if(in_array('I need two different powers', $optionsArray) ) { 


                                        if($value != 'I need two different powers') {
                                            $optionsArray['I need two different powers'][] = $value;
                                        }
                                    }

                                    
                                }    
                            }
                        }
                    }

                    if($item->getProduct()->getTypeId() =='configurable') {

                        $childSku = $item->getSku();
                        //$logger->info('sku '. $childSku);
                        $childId = $this->resourceProduct->getIdBySku($childSku);
                        //$logger->info('childId '. $childId);
                        $colorAttr = $product->getResource()->getAttribute('color');
                        $attrId = $colorAttr->getAttributeId();
                        $attributes = $item->getOptionByCode('attributes');
                        $attributesValue = $this->serialize->unserialize($attributes->getValue());
                         
                        if ($attributesValue) {

                            if (isset($attributesValue[$attrId]) && !empty($attributesValue[$attrId])) {
                               $valueId = $attributesValue[$attrId];
                               $optionColor = $colorAttr->getSource()->getOptionText($valueId);
                               if ($childId) {
                                    $parentIds = $this->configurable->getParentIdsByChild($childId);
                                   
                                    if ($parentIds) {
                                        $skus = $this->resourceProduct->getProductsSku($parentIds);
                                        if($skus) {
                                            $sku = $skus[0]['sku'];
                                        }
                                       
                                    }

                                }
                                if(!$sku) {
                                    $this->messageManager->addNotice('Something went during add to cart');
                                    $this->logger->critical($e->getMessage());
                                }
                               
                                
                                if( isset($optionsArray[0]) && $optionsArray )  {
                                    if($optionsArray[0]=='I need two different powers' || $optionsArray[0]=='With Power') {
                                        unset($optionsArray[0]);
                                    }
                                }

                                if($optionsArray) {
                                    /* With 2 Powers */
                                    
                                    if(isset($optionsArray['I need two different powers']) && !empty($optionsArray['I need two different powers'])) {
                                        $optionsAr = $optionsArray['I need two different powers'];
                                        
                                        $i = 0;
                                        foreach ($optionsAr as $key => $val) {

                                            if($newBarcodesArray = $this->getBarcodeBy($sku,$val,$optionColor)) {
                                                
                                                if(isset($newBarcodesArray['barcode']) &&  !empty($newBarcodesArray['barcode'])){
                                                    $newBarcodes = $newBarcodesArray['barcode'];
                                                    $buyRequest = $item->getOptionByCode('info_buyRequest');
                                                    $buyRequestArr = $this->serialize->unserialize($buyRequest->getValue());

                                                    if(isset($buyRequestArr['barcode']) && !empty($buyRequestArr['barcode'])){

                                                        $oldBarcodes  = $buyRequestArr['barcode'];
                                                        $oldBarcodesArray = explode(',', $oldBarcodes);
                                                        $newBarcodesArray = explode(',', $newBarcodes);
                                                        $barcodesArray = array_unique(array_merge($oldBarcodesArray,$newBarcodesArray));
                                                        $barcodes = implode($barcodesArray, ',');
                                                        $buyRequestArr['barcode'] = $barcodes;

                                                    } else {
                                                       
                                                        $buyRequestArr['barcode'] = $newBarcodes;
                                                    }
                                                    
                                                    $buyRequest->setValue($this->serialize->serialize($buyRequestArr));
                                                    $buyRequest->save();
                                                }
                                            }
                                            $i++;
                                        }
                                    } else {
                                        /* Without Power */
                                        if($newBarcodesArray = $this->getBarcodeBy($sku,$val=null,$optionColor)) {
                       
                                            if(isset($newBarcodesArray['barcode']) &&  !empty($newBarcodesArray['barcode'])){
                                                $newBarcodes = $newBarcodesArray['barcode'];
                                                $buyRequest = $item->getOptionByCode('info_buyRequest');
                                                $buyRequestArr = $this->serialize->unserialize($buyRequest->getValue());

                                                if(isset($buyRequestArr['barcode']) && !empty($buyRequestArr['barcode'])){

                                                    $oldBarcodes  = $buyRequestArr['barcode'];
                                                    $oldBarcodesArray = explode(',', $oldBarcodes);
                                                    $newBarcodesArray = explode(',', $newBarcodes);
                                                    $barcodesArray = array_unique(array_merge($oldBarcodesArray,$newBarcodesArray));
                                                    $barcodes = implode($barcodesArray, ',');
                                                    $buyRequestArr['barcode'] = $barcodes;

                                                } else {
                                                   
                                                    $buyRequestArr['barcode'] = $newBarcodes;
                                                }
                                                
                                                $buyRequest->setValue($this->serialize->serialize($buyRequestArr));
                                                $buyRequest->save();
                                            }
                                        }
                                    }
                                }
                            }
                          
                        }   
                    }
                }
               
            
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
           
           
        
    }

   
    /*private function checkPower($value,$optionsArray) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/barcode1.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        


        if($value=='Without Power') { 
            $optionsArray = [];
            return;
        }

        if($value=='With Power') { 
            $optionsArray[] = 'With Power';

        }
        $logger->info(print_r($value,true));
        if(in_array('With Power', $optionsArray) ) { 

            if($value != 'With Power'){
                $optionsArray[] = $value;
            }
        }
        if($value=='I need two different powers') { 
            $optionsArray[] = 'I need two different powers';
        }
        $logger->info(print_r($optionsArray,true)); 
       return $optionsArray;

    }*/
    private function getBarcodeBy($sku,$power=null,$color = null) {

        $tablename = $this->getTableNameDB();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/getBarcodeBy.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
    
        

        /* for simple item */
        if($power && !$color){

            return  $this->connection->fetchRow("SELECT * FROM $tablename where sku='$sku' and power='$power'");
        }
        /* Without Power(Config item) */
        if(!$power && $color){

            return  $this->connection->fetchRow("SELECT * FROM $tablename where sku='$sku' and color='$color'");
            $logger->info(print_r("SELECT * FROM $tablename where sku='$sku' and color='$color' and power='$power' ",true));
        }

        if($power && $color){

             $data =   $this->connection->fetchRow("SELECT * FROM $tablename where sku='$sku' and color='$color' and power='$power' ");
            
           
             return $data;
        }
        
       
    }
    private function getTableNameDB() {
        $tablename = $this->connection->getTableName('admin_barcode');
        return $tablename;
    }
    private function getProductOptionNameById($_valueId,$product)
    {
       
       foreach ($product->getOptions() as $options) {
             foreach ($options->getValues() as $valueId=>$value) {
                   if($_valueId == $valueId) {
                        return $value['title'];
                   }
            }
          
       }
      return false;
    }

    private function getProductAttributeSetNameById($product)
    {
        return $this->attributeSet->get($product->getAttributeSetId())->getAttributeSetName();
    }


    

}
