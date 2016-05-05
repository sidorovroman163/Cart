<?php
namespace Meling\Cart\Providers;

/**
 * Class Session
 * @package Meling\Cart\Providers
 */
class Session extends Provider
{
    public function addCertificate(
        $certificateId,
        $quantity = 1,
        $price = null,
        $shopId = null,
        $deliveryId = null,
        $shopTariffId = null,
        $addressId = null,
        $pvz = null
    ) {
        $certificates = $this->session()->get('certificates', array());
        if($keys = array_keys($certificates)) {
            $id = max($keys) + 1;
        } else {
            $id = 1;
        }
        $certificate = $this->orm()->query('certificate')->in($certificateId)->findOne();
        if($certificate) {
            $certificates[$id] = array(
                'id'            => $id,
                'certificateId' => $certificate->id(),
                'quantity'      => $quantity,
                'price'         => $price,
                'shopId'        => $shopId,
                'deliveryId'    => $deliveryId,
                'shopTariffId'  => $shopTariffId,
                'addressId'     => $addressId,
                'pvz'           => $pvz,
            );
            $this->session()->set('certificates', $certificates);
            $this->certificates[$id] = $this->buildProduct($id, $certificate, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz);
        }
    }

    public function addOption(
        $optionId,
        $quantity = 1,
        $price = null,
        $shopId = null,
        $deliveryId = null,
        $shopTariffId = null,
        $addressId = null,
        $pvz = null
    ) {
        $options = $this->session()->get('options', array());
        if($keys = array_keys($options)) {
            $id = max($keys) + 1;
        } else {
            $id = 1;
        }
        $option = $this->orm()->query('option')->in($optionId)->findOne();
        if($option) {
            $options[$id] = array(
                'id'           => $id,
                'optionId'     => $option->id(),
                'quantity'     => $quantity,
                'price'        => $price,
                'shopId'       => $shopId,
                'deliveryId'   => $deliveryId,
                'shopTariffId' => $shopTariffId,
                'addressId'    => $addressId,
                'pvz'          => $pvz,
            );
            $this->session()->set('options', $options);
            $this->options[$id] = $this->buildProduct($id, $option, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz);
        }
    }

    public function address()
    {
        return null;
    }

    public function addresses()
    {
        return array();
    }

    /**
     * @return \Meling\Cart\Products\Product[]
     */
    public function certificates()
    {
        if($this->certificates === null) {
            $this->certificates = array();
            foreach($this->session()->get('certificates', array()) as $id => $certificate) {
                if(!empty($certificate['certificateId'])) {
                    $entity = $this->orm()->query('certificate')->in($certificate['certificateId'])->findOne();
                    if($entity) {
                        $quantity                = empty($certificate['quantity']) ? 1 : (int)$certificate['quantity'];
                        $price                   = empty($certificate['price']) ? $entity->getField('price') : (int)$certificate['price'];
                        $shopId                  = empty($certificate['shopId']) ? null : $certificate['shopId'];
                        $shopTariffId            = empty($certificate['shopTariffId']) ? null : $certificate['shopTariffId'];
                        $addressId               = empty($certificate['addressId']) ? null : $certificate['addressId'];
                        $pvz                     = empty($certificate['pvz']) ? null : $certificate['pvz'];
                        $this->certificates[$id] = $this->buildProduct(
                            $id, $entity, $quantity, $price, $shopId,
                            $shopTariffId, $addressId, $pvz
                        );
                    }
                }
            }
        }

        return $this->certificates;
    }

    public function clear()
    {
        $this->session()->remove('options');
        $this->session()->remove('certificates');
    }

    public function customerCards()
    {
        return array();
    }

    public function editCertificate($id, $certificateId, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz)
    {
        $this->removeCertificate($id);
        $this->addCertificate($certificateId, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz);
    }

    public function editOption($id, $optionId, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz)
    {
        $this->removeOption($id);
        $this->addOption($optionId, $quantity, $price, $shopId, $deliveryId, $shopTariffId, $addressId, $pvz);
    }

    public function email()
    {
        return null;
    }

    public function firstname()
    {
        return null;
    }

    public function id()
    {
        return null;
    }

    public function lastname()
    {
        return null;
    }

    public function middlename()
    {
        return null;
    }

    /**
     * @return \Meling\Cart\Products\Product[]
     */
    public function options()
    {
        if($this->options === null) {
            $this->options = array();
            foreach($this->session()->get('options', array()) as $id => $option) {
                if(!empty($option['optionId'])) {
                    $entity = $this->orm()->query('option')->in($option['optionId'])->findOne();
                    if($entity) {
                        $quantity           = empty($option['quantity']) ? 1 : (int)$option['quantity'];
                        $price              = empty($option['price']) ? $entity->getField('price') : (int)$option['price'];
                        $shopId             = empty($option['shopId']) ? null : $option['shopId'];
                        $shopTariffId       = empty($option['shopTariffId']) ? null : $option['shopTariffId'];
                        $addressId          = empty($option['addressId']) ? null : $option['addressId'];
                        $pvz                = empty($option['pvz']) ? null : $option['pvz'];
                        $this->options[$id] = $this->buildProduct($id, $entity, $quantity, $price, $shopId, $shopTariffId, $addressId, $pvz);
                    }
                }
            }
        }

        return $this->options;
    }

    public function phone()
    {
        return null;
    }

    public function removeCertificate($id)
    {
        $certificates = $this->session()->get('certificates', array());
        unset($certificates[$id]);
        $this->session()->set('certificates', $certificates);
    }

    public function removeOption($id)
    {
        $options = $this->session()->get('options', array());
        unset($options[$id]);
        $this->session()->set('options', $options);
    }


}
