<?php

declare(strict_types=1);

namespace Formation;

use Formation\Command\MarchandiseReceived;
use Formation\Command\OrderStarted;
use Formation\Query\OrderId;
use Formation\Query\WaitingOrders;
use PHPUnit\Framework\TestCase;

class OrderQueryTest extends TestCase
{
    public function test_When_receive_OrderCreated_Then_this_order_is_added_in_waiting_orders()
    {
        $orderId = new OrderId('foo');

        $waitingOrders = new WaitingOrders();
        $waitingOrders->handle(new OrderStarted(), $orderId);

        $this->assertContainsEquals($orderId, $waitingOrders->orderIds());
    }

    public function test_When_receive_MarchandiseReceived_Then_this_order_is_removed_of_waiting_orders()
    {
        $orderId = new OrderId('foo');

        $waitingOrders = new WaitingOrders();
        $waitingOrders->handle(new OrderStarted(), $orderId);
        $waitingOrders->handle(new MarchandiseReceived(), $orderId);

        $this->assertNotContainsEquals($orderId, $waitingOrders->orderIds());
    }

    public function test_Given_2_waiting_orders_A_and_B_When_receive_MarchandiseReceived_of_order_B_Then_I_have_only_order_A_in_waiting_orders()
    {
        $orderIdA = new OrderId('A');
        $orderIdB = new OrderId('B');

        $waitingOrders = new WaitingOrders();
        $waitingOrders->handle(new OrderStarted(), $orderIdA);
        $waitingOrders->handle(new OrderStarted(), $orderIdB);
        $waitingOrders->handle(new MarchandiseReceived(), $orderIdB);

        $this->assertContainsEquals($orderIdA, $waitingOrders->orderIds());
        $this->assertNotContainsEquals($orderIdB, $waitingOrders->orderIds());
    }
}
