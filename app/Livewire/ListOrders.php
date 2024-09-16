<?php

namespace App\Livewire;

use App\Models\Orders;
use Livewire\Component;

class ListOrders extends Component
{
    public $orders;

    public function render()
    {
        return view('livewire.list-orders');
    }

    public function mount(): void
    {
        $this->orders = Orders::with('orderItems')->get();
    }
}
