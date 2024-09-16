<?php

namespace App\Observers;

use App\Models\OrderItems;
use App\Models\Products;

class OrderItemsObserver
{
    /**
     * Handle the OrderItems "created" event.
     */
    public function created(OrderItems $orderItems): void
    {
        $products = Products::find($orderItems->products_id);

        $products->quantity -= $orderItems->quantity;

        $products->save();
    }

    /**
     * Handle the OrderItems "updated" event.
     */
    public function updated(OrderItems $orderItems): void
    {
        $products = Products::find($orderItems->products_id);

        $newQuantity = $products->original_quantity - $orderItems->quantity;

        $products->quantity = $newQuantity;

        $products->save();
    }

    /**
     * Handle the OrderItems "deleted" event.
     */
    public function deleted(OrderItems $orderItems): void
    {
        $products = Products::find($orderItems->products_id);

        $products->quantity += $orderItems->quantity;

        $products->save();
    }

    /**
     * Handle the OrderItems "restored" event.
     */
    public function restored(OrderItems $orderItems): void
    {
        //
    }

    /**
     * Handle the OrderItems "force deleted" event.
     */
    public function forceDeleted(OrderItems $orderItems): void
    {
        //
    }
}
