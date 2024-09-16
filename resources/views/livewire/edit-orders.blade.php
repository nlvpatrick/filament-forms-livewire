<div class="m-48 p-3">
    {{ $this->customerDetailsForm }}
    <br>
    {{ $this->customerItemsForms }}
    <div style="text-align:right; margin-bottom: 4px; margin-top:12px">

        <x-filament::button wire:click.prevent="update">
            Update
        </x-filament::button>

        <x-filament::button color='gray' href="/orders" tag="a">
            Cancel
        </x-filament::button>
    </div>
</div>
