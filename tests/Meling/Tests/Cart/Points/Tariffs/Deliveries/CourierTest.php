<?php
namespace Meling\Tests\Cart\Points\Tariffs\Deliveries;

class CourierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Meling\Cart\Points\Tariffs\Deliveries\Courier
     */
    protected $deliveryCourier;

    protected $name;

    protected $fullName;

    protected $cost;

    public function setUp()
    {
        $orm = new \Meling\Tests\ORM();
        /** @var \Meling\Tests\ORMWrappers\Entities\Delivery $delivery */
        $delivery   = $orm->query('delivery')->where('alias', 'courier')->findOne();
        $this->name = $delivery->getField('name');
        /** @var \Meling\Tests\ORMWrappers\Entities\ShopTariff $defaultTariff */
        $defaultTariff  = $delivery->shopTariffs()->getByOffset(0);
        $this->cost     = $defaultTariff->getField('cost');
        $this->fullName = $this->name . ' (' . $defaultTariff->getField('name') . ')';
        $orm->disconnect();
        $this->deliveryCourier = new \Meling\Cart\Points\Tariffs\Deliveries\Courier($delivery, $delivery->shopTariffs(), $defaultTariff);
    }

    public function testAttributeDefaultTariff()
    {
        $this->assertAttributeInstanceOf('\Meling\Tests\ORMWrappers\Entities\ShopTariff', 'defaultTariff', $this->deliveryCourier);
    }

    public function testAttributeDelivery()
    {
        $this->assertAttributeInstanceOf('\Meling\Tests\ORMWrappers\Entities\Delivery', 'delivery', $this->deliveryCourier);
    }

    public function testMethodCalculate()
    {
        $this->assertEquals($this->cost, $this->deliveryCourier->calculate());
    }

    public function testMethodDefaultTariff()
    {
        $this->assertInstanceOf('\Meling\Tests\ORMWrappers\Entities\ShopTariff', $this->deliveryCourier->defaultTariff());
    }

    public function testMethodFullName()
    {
        $this->assertEquals($this->fullName, $this->deliveryCourier->fullName());
    }

    public function testMethodName()
    {
        $this->assertEquals($this->name, $this->deliveryCourier->name());
    }

}
