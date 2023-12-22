<?php

namespace App\Livewire;

use App\Models\Products;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class EditProduct extends Component implements HasForms
{
    use InteractsWithForms;

    public $record;

    public ?array $data = [];

    public function render()
    {
        return view('livewire.edit-product');
    }

    public function mount(Products $products): void
    {
        $this->record = $products;

        $this->form->fill($products->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('quantity')
                            ->type('number'),
                        TextInput::make('description')
                            ->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('categories', 'category_name')
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('category_name'),
                            ]),
                    ])->columns(2),
            ])
            ->statePath('data')
            ->model(Products::class);
    }

    public function save(): void
    {
        Products::find($this->record->id)->update($this->form->getState());

        redirect('products');

        Notification::make()
            ->title('Updated successfully')
            ->success()
            ->send();
    }
}
