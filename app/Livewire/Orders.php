<?php

namespace App\Livewire;

use App\Models\OrderItems;
use App\Models\Orders as ModelsOrders;
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

class Orders extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $customerDetailsData = [];

    public ?array $customerItemsData = [];

    public function render()
    {
        return view('livewire.orders');
    }

    public function mount(): void
    {
        $this->customerDetailsForm->fill();

        $this->customerItemsForms->fill();
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
                        ->default('OR-'.random_int(100000, 999999))
                        ->disabled()
                        ->dehydrated()
                        ->required()
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
                        ->default('new')
                        ->disabled()
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
                                                    $initialQuantity = Products::where('id', $get('products_id'))->first()->quantity;
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

    public function create(): void
    {
        $this->customerDetailsForm->getState();

        $this->customerItemsForms->getState();

        try {
            DB::beginTransaction();

            $orders = ModelsOrders::create($this->customerDetailsForm->getState());

            foreach ($this->customerItemsForms->getState()['items'] as $key => $value) {
                OrderItems::create([
                    'orders_id' => $orders->id,
                    'products_id' => $value['products_id'],
                    'quantity' => $value['quantity'],
                ]);
            }

            redirect('orders/create');

            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}
