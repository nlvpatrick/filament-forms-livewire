<div class="m-48 p-3">
    <section class="bg-white dark:bg-gray-900 py-8 lg:py-16 antialiased">
        <div class="max-w-2xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">Discussion
                    ({{ $this->products->reports->count() }})</h2>
            </div>
            <form class="mb-6">
                <div class="mb-3">
                    {{ $this->commentForm }}
                </div>

                <x-filament::button wire:click.prevent="saveComment">
                    Post comment
                </x-filament::button>
            </form>

            <div style="{{ $this->products->reports->count() > 3 ? 'overflow-y: scroll; height:400px;' : '' }}">
                @foreach ($this->products->reports->sortByDesc('id') as $item)
                    <article
                        class="p-6 mb-3 text-base bg-white border-t border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                        <p class="text-gray-500 dark:text-gray-400">{{ $item->comment }}</p>

                        <div style="text-align:left">

                            <a class="hover:underline text-sky-600" href="javascript:void(0)"
                                wire:click.prevent="edit_comments({{ $item->id }})"
                                style="font-size: 12px;font-weight:bold;margin-right:4px;color:#0284C7">Edit</a>
                            <a
                                style="height: 6px;
                width: 6px;
                background-color: #bbb;
                border-radius: 50%;
                display: inline-block;
                margin-right:4px">
                            </a>
                            <a class="hover:underline text-red-600" href="javascript:void(0)"
                                wire:click.prevent="delete_modal({{ $item->id }})"
                                style="font-size: 12px;font-weight:bold;color:#DC2626">Delete</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- modal section --}}
    <x-filament::modal id="edit-comment" width="3xl">
        {{ $this->editCommentForm }}
        <x-slot name="footer">
            <x-filament::button wire:click.prevent="update_comment" size="sm" id="edit_button">
                Update
            </x-filament::button>
            <button> </button>
        </x-slot>
    </x-filament::modal>

    <x-filament::modal id="delete-comment" width="md" alignment="center" icon="heroicon-o-trash"
        icon-color="danger">

        <x-slot name="heading">
            Delete comment
        </x-slot>

        <x-slot name="description">
            Are you sure you would like to do this?
        </x-slot>

        <x-slot name="footerActions">
            <x-filament::button color="gray" outlined size="md" class="w-48"
                x-on:click.prevent="$dispatch('close-modal', {id: 'delete-comment'})">
                Cancel
            </x-filament::button>
            <x-filament::button size="md" color="danger" class="w-48" wire:click.prevent="delete_comment">
                Confirm
            </x-filament::button>
        </x-slot>

    </x-filament::modal>
</div>
