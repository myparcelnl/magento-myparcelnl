<?php
class MyParcel_MyParcelBE_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{

    /**
     * Get consignment id's from all shipments in the order list
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        /**
         * @var Mage_Sales_Model_Order_Shipment $shipment
         * @var MyParcel_MyParcelBE_Model_Shipment $myParcelShipment
         */
        $shipmentIds = array();
        $consignmentIds = array();
        $myParcelShipments = array();

        if ($this->getCollection()->count() == 0) {
            return $this;
        }

        foreach($this->getCollection() as $shipment)
        {
            $shipmentIds[] = $shipment->getId();
        }

        $collection = Mage::getResourceModel('myparcel_be/shipment_collection');
        $collection->getSelect();
        $collection->addFieldToFilter('shipment_id', array('in' => array($shipmentIds)));

        foreach ($collection as $myParcelShipment){
            if($myParcelShipment->hasConsignmentId()){
                $consignmentId = $myParcelShipment->getConsignmentId();
                $consignmentIds[] = $consignmentId;
                $myParcelShipments[$consignmentId] = $myParcelShipment;
            }
        }


        $apiInfo    = Mage::getModel('myparcel_be/api_myParcel');
        $responseShipments = $apiInfo->getConsignmentsInfoData($consignmentIds);

        if($responseShipments){
            foreach($responseShipments as $responseShipment){
                $myParcelShipment = $myParcelShipments[$responseShipment->id];
                $myParcelShipment->updateStatus($responseShipment);
            }
        }

        return $this;
    }



}
