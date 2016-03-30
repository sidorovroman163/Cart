<?php
namespace Meling\Cart;

class Orders
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var \Meling\Cart\Orders\Order[]
     */
    private $orders;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Customer $customer
     * @param Provider $provider
     */
    public function __construct(Customer $customer, Provider $provider)
    {
        $this->customer = $customer;
        $this->provider = $provider;
    }

    public function asArray()
    {
        $this->requireOrders();

        return $this->orders;
    }

    public function defaultOrder()
    {
        return $this->buildOrder();
    }

    public function get($id)
    {
        $this->requireOrders();
        if(array_key_exists($id, $this->orders)) {
            return $this->orders[$id];
        }
        throw new \Exception('Order ' . $id . ' does not exist');
    }

    private function buildCustomProvider(array $options = array(), array $certificates = array())
    {
        return new \Meling\Cart\Provider\Custom($options, $certificates);
    }

    private function buildOrder($id = null, $customer = null, $provider = null)
    {
        if($customer === null) {
            $customer = $this->customer;
        }
        if($provider === null) {
            $provider = $this->provider;
        }

        return new Orders\Order($id, $customer, $this->buildProducts($provider));
    }

    private function buildProducts($provider)
    {
        return new \Meling\Cart\Products($provider);
    }

    private function requireOrders()
    {
        if($this->orders !== null) {
            return;
        }
        $orders            = array();
        $objectsShop       = array();
        $objectsDeliveries = array();
        foreach($this->provider->objects()->asArray() as $object) {
            if($object->shopId()) {
                if($object->shopTariffId()) {
                    $objectsDeliveries[$object->deliveryId() . $object->shopTariffId() . $object->addressId()] = array(
                        'options'      => array(),
                        'certificates' => array(),
                    );
                    if($object instanceof \Meling\Cart\Objects\Option) {
                        $objectsDeliveries[$object->deliveryId() . $object->shopTariffId() . $object->addressId()]['options'][] = $object;
                    } elseif($object instanceof \Meling\Cart\Objects\Certificate) {
                        $objectsDeliveries[$object->deliveryId() . $object->shopTariffId() . $object->addressId()]['certificates'][] = $object;
                    }
                } else {
                    $objectsShop[$object->shopId()] = array(
                        'options'      => array(),
                        'certificates' => array(),
                    );
                    if($object instanceof \Meling\Cart\Objects\Option) {
                        $objectsShop[$object->shopId()]['options'][] = $object;
                    } elseif($object instanceof \Meling\Cart\Objects\Certificate) {
                        $objectsShop[$object->shopId()]['certificates'][] = $object;
                    }
                }
            }
        }
        foreach($objectsShop as $objects) {
            $provider = $this->buildCustomProvider($objects['options'], $objects['certificates']);
            $orders[] = $this->buildOrder(null, null, $provider);
        }
        $this->orders = $orders;
    }

}