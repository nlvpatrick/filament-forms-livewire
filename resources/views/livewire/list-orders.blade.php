<div class="m-48 p-3">
    <div class="mb-3 flex flex-row-reverse space-x-1 space-x-reverse ...">
        <x-filament::button href="/orders/create" tag="a">
            Create Order
        </x-filament::button>
    </div>

    <div class="grid grid-cols-4 gap-4">
        @foreach ($this->orders as $order)
            <a href="/orders/edit/{{ $order->id }}" target="_blank">
                <div class="p-6 rounded-md bg-white shadow dark:bg-gray-800">
                    <div class="flex flex-row justify-between">
                        <p style="font-size: 12px">{{ $order->or_number }}</p>

                        <p class="font-semibold"style="font-size: 12px">
                            {{ ucfirst($order->status) }}
                        </p>
                    </div>

                    <p class="font-semibold ..." style="font-size: 12px">{{ $order->customer }}</p>
                    <p class="font-semibold ..." style="font-size: 12px">Total ordered items :
                        {{ count($order->orderItems) }}
                    </p>
                    <hr class="h-px mt-2 mb-2 bg-gray-200 border-0 dark:bg-gray-700">
                    <div>
                        <span style="font-size: 12px">Information</span>
                        <p class="font-semibold ..." style="font-size: 12px">{{ $order->city }}</p>
                        <p class="font-semibold ..." style="font-size: 12px">{{ $order->state }}</p>
                        <p class="font-semibold ..." style="font-size: 12px">{{ $order->phone }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
