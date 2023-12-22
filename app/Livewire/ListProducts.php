<?php

namespace App\Livewire;

use App\Models\Categories;
use App\Models\Products;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-products');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Products::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description'),
                TextColumn::make('quantity'),
                TextColumn::make('category_id')
                    ->label('Category')
                    ->formatStateUsing(function ($state) {
                        return Categories::where('id', $state)->pluck('category_name')->first();
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->action(function ($record) {
                        redirect('products/edit/'.$record->id);
                    }),
                DeleteAction::make()
                    ->record(function ($record) {
                        return $record;
                    }),
            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                Action::make('add_product')
                    ->action(function () {
                        redirect('products/create');
                    }),
            ]);
    }
}
