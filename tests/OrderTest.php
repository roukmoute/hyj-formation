<?php

declare(strict_types=1);

namespace Formation;

use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_When_start_order_Then_raise_OrderStarted()
    {
        $order = new Order();
        $order->start();

        $this->assertContainsEquals(new OrderStarted(), $order->events());
    }

    public function test_Given_order_started_When_take_marchandise_Then_raise_MarchandiseReceived()
    {
        $order = new Order();
        $order->start();
        $order->takeMarchandise();
        $this->assertContainsEquals(new MarchandiseReceived(), $order->events());
    }

    public function test_Given_Order_not_started_When_take_marchandise_Then_raise_nothing()
    {
        $order = new Order();
        $order->takeMarchandise();

        $this->assertNotContainsEquals(new MarchandiseReceived(), $order->events());
    }

    public function test_Given_Order_with_marchandise_received_When_take_marchandise_Then_raise_nothing()
    {
        $order = new Order([
            new OrderStarted(),
            new MarchandiseReceived(),
        ]);
        $order->takeMarchandise();

        $this->assertCount(2, $order->events());
    }

    public function test_Given_Order_started_of_7_colis_When_take_marchandise_with_5_colis_Then_raise_MarchandisePartiallyReceived()
    {
        $order = new Order();
        $order->start(7);
        $order->takeMarchandise(5);

        $this->assertNotContainsEquals(new MarchandiseReceived(), $order->events());
        $this->assertContainsEquals(new MarchandisePartiallyReceived(7, 5), $order->events());
    }

    public function test_Given_Order_of_7_colis_with_5_colis_received_When_take_marchandise_with_2_colis_Then_raise_MarchandiseReceived()
    {
        $order = new Order();
        $order->start(7);
        $order->takeMarchandise(5);
        $order->takeMarchandise(2);

        $this->assertContainsEquals(new MarchandiseReceived(2), $order->events());
    }
}
