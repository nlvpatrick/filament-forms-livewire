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
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateProduct extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function render(): View
    {
        return view('livewire.create-product');
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

    public function create(): void
    {

        Products::create($this->form->getState());

        redirect('products');

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
