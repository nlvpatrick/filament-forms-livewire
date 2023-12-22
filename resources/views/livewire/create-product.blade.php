<div class="m-48 p-3">

    {{ $this->form }}

    <div style="text-align:right; margin-bottom: 4px; margin-top:12px">

        <x-filament::button wire:click.prevent="create">
            Save
        </x-filament::button>

        <x-filament::button color='gray' href="/products" tag="a">
            Cancel
        </x-filament::button>
    </div>

    <x-filament-actions::modals />
</div>
