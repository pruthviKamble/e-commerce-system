<?php

namespace App\Cart;

use App\Models\User;
use App\Money\Money;
use Illuminate\Support\Collection;

class Cart
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function add(array $products)
    {
        $this->user->cart()->syncWithoutDetaching(
            $this->getStorePayload($products)
        );
    }

    public function update(int $id, int $quantity)
    {
        $this->user->cart()->updateExistingPivot($id, compact('quantity'));
    }

    public function delete(int $id)
    {
        $this->user->cart()->detach($id);
    }

    public function clear()
    {
        $this->user->cart()->detach();
    }

    public function isEmpty()
    {
        return $this->user->cart->sum('pivot.quantity') === 0;
    }

    public function subtotal()
    {
        $subtotal = $this->user->cart->sum(function ($product) {
            return $product->money->amount() * $product->pivot->quantity;
        });

        return new Money($subtotal);
    }

    public function total()
    {
        return $this->subtotal();
    }

    private function getStorePayload(array $products): Collection
    {
        return collect($products)->mapWithKeys(function ($product) {
            return [
                $product['id'] => [
                    'quantity' => $product['quantity'] + $this->getCurrentQuantity($product['id'])
                ]
            ];
        });
    }

    protected function getCurrentQuantity(int $productId)
    {
        $product = $this->user->cart()->where('id', $productId)->first();

        return $product ? $product->pivot->quantity : 0;
    }
}
