<?php

namespace App\Livewire;

use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditOrders extends Component implements HasForms
{
    use InteractsWithForms;

    public Orders $orders;

    public ?array $customerDetailsData = [];

    public ?array $customerItemsData = [];

    public function render()
    {
        return view('livewire.edit-orders');
    }

    public function mount(): void
    {
        $this->customerDetailsForm->fill($this->orders->toArray());

        $this->customerItemsForms->fill([
            'items' => $this->orders->orderItems->toArray(),
        ]);
    }

    protected function getForms(): array
    {
        return [
            'customerDetailsForm',
            'customerItemsForms',
        ];
    }

    public static function customerDetailsForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('or_number')
                        ->label('OR Number')
                        ->disabled()
                        ->dehydrated()
                        ->maxLength(32),

                    TextInput::make('customer')
                        ->dehydrated()
                        ->required(),

                    TextInput::make('phone')
                        ->numeric()
                        ->dehydrated()
                        ->required(),

                ])
                ->columns(2),

            Section::make()
                ->schema([
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'proccessing' => 'Processing',
                            'shipped' => 'Shiped',
                            'delivered' => 'Delivered',
                        ])
                        ->dehydrated()
                        ->required(),

                    TextInput::make('city')
                        ->dehydrated()
                        ->required(),

                    TextInput::make('state')
                        ->label('State / Province')
                        ->dehydrated()
                        ->required(),

                    TextInput::make('zip')
                        ->label('Zip / Postal Code')
                        ->numeric()
                        ->dehydrated()
                        ->required(),

                    RichEditor::make('notes')
                        ->columnSpan(2),
                ])
                ->columns(3),

        ])
            ->statePath('customerDetailsData');
    }

    public function customerItemsForms(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Section::make()
                        ->schema(
                            [
                                Repeater::make('items')
                                    ->schema([
                                        Select::make('products_id')
                                            ->label('Products')
                                            ->searchable()
                                            ->preload()
                                            ->options(Products::all()->pluck('name', 'id'))
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->live()
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->rules([
                                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                    $initialQuantity = ! is_null($get('products_id')) ? Products::where('id', $get('products_id'))->first()->quantity : null;
                                                    $quantitySold = $value;
                                                    $quantityLeft = $initialQuantity - $quantitySold;
                                                    $isLessThanFive = $quantityLeft < 5;

                                                    if ($isLessThanFive) {
                                                        $fail('Insufficient Quantity.');
                                                    }
                                                },
                                            ])
                                            ->required(),
                                    ])
                                    ->extraItemActions([
                                        Action::make('openProduct')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->tooltip('Open product')
                                            ->url(function (array $arguments, Repeater $component) {
                                                $itemData = $component->getRawItemState($arguments['item']);

                                                $product = Products::find($itemData['products_id']);

                                                if (! $product) {
                                                    return null;
                                                }

                                                return route('products.edit', $product->id);
                                            }, shouldOpenInNewTab: true)
                                            ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['products_id'])),
                                    ])
                                    ->columns(2),
                            ]
                        ),
                ]
            )->statePath('customerItemsData');
    }

    public function update(): void
    {

        $details = $this->customerDetailsForm->getState();

        $items = $this->customerItemsForms->getState();

        $orderItemsArray = $this->orders->orderItems->pluck('id')->toArray();

        try {
            DB::beginTransaction();

            Orders::find($this->orders->id)->update($details);

            $updatedItemsArray = [];

            foreach ($items['items'] as $key => $value) {
                if (isset($value['id'])) {
                    $updatedItemsArray[] = $value['id'];
                }
            }

            $removedItems = array_diff($orderItemsArray, $updatedItemsArray);

            if (! empty($removedItems)) {
                foreach ($removedItems as $removedItem) {
                    OrderItems::destroy($removedItem);
                }
            }

            if (empty($removedItems)) {

                foreach ($items['items'] as $value) {
                    OrderItems::updateOrCreate(
                        [
                            'orders_id' => isset($value['orders_id']) ? $value['orders_id'] : $this->orders->id,
                            'products_id' => $value['products_id'],
                        ],
                        [
                            'quantity' => $value['quantity'],
                        ]
                    );
                }
            }

            Notification::make()
                ->title('Update successfully')
                ->success()
                ->send();

            redirect('orders/edit/'.$this->orders->id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}
