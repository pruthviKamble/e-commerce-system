<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Models\User;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /** @test */
    function it_has_default_status_of_pending()
    {
        $order = factory(Order::class)->create();

        $this->assertEquals(Order::PENDING, $order->status);
    }

    /** @test */
    function it_belongs_to_a_user()
    {
        $order = factory(Order::class)->create();

        $this->assertInstanceOf(User::class, $order->user);
    }

    /** @test */
    function it_belongs_to_an_address()
    {
        $order = factory(Order::class)->create();

        $this->assertInstanceOf(Address::class, $order->address);
    }

    /** @test */
    function it_belongs_to_a_shipping_method()
    {
        $order = factory(Order::class)->create();

        $this->assertInstanceOf(ShippingMethod::class, $order->shippingMethod);
    }

    /** @test */
    function it_has_many_products()
    {
        $order = factory(Order::class)->create();

        $order->products()->attach(
            $productVariation = factory(ProductVariation::class)->create(), [
                'quantity' => 1
            ]
        );

        $this->assertInstanceOf(ProductVariation::class, $order->products->first());
        $this->assertTrue($order->products->first()->is($productVariation));
    }

    /** @test */
    function it_has_a_quantity_attached_to_the_products()
    {
        $order = factory(Order::class)->create();

        $order->products()->attach(
            $productVariation = factory(ProductVariation::class)->create(), [
                'quantity' => 2
            ]
        );

        $this->assertEquals(2, $order->products->first()->pivot->quantity);
    }
}
