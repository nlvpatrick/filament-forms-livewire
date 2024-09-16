<?php

namespace App\Livewire;

use App\Models\Products;
use App\Models\Reports as ModelsReports;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class Reports extends Component implements HasForms
{
    use InteractsWithForms;

    public Products $products;

    public ?array $commentData = [];

    public ?array $editCommentData = [];

    public $comment_id;

    public function render()
    {
        return view('livewire.reports');
    }

    public function commentForm(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('comment')
                    ->required()
                    ->markAsRequired(false)
                    ->autosize()
                    ->placeholder('Write a comment...'),
            ])
            ->statePath('commentData');
    }

    public function editCommentForm(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('comment')
                    ->required()
                    ->markAsRequired(false)
                    ->autosize()
                    ->placeholder('Write a comment...'),
            ])
            ->statePath('editCommentData');
    }

    protected function getForms(): array
    {
        return [
            'commentForm',
            'editCommentForm',
        ];
    }

    public function saveComment(): void
    {
        if ($this->commentForm->getState()['comment'] != '') {
            ModelsReports::create([
                'products_id' => $this->products->id,
                'comment' => $this->commentForm->getState()['comment'],
            ]);

            $this->commentForm->fill();
        }
    }

    public function edit_comments($id)
    {
        $this->comment_id = $id;

        $comment = ModelsReports::find($id);

        $this->editCommentForm->fill([
            'comment' => $comment->comment,
        ]);

        $this->dispatch('open-modal', id: 'edit-comment');
    }

    public function update_comment()
    {
        if ($this->editCommentForm->getState()['comment'] != '') {
            $comment = ModelsReports::find($this->comment_id);

            $comment->comment = $this->editCommentForm->getState()['comment'];

            $comment->update();

            Notification::make()
                ->title('Comment Updated')
                ->success()
                ->send();

            $this->dispatch('close-modal', id: 'edit-comment');
        }
    }

    public function delete_modal($id)
    {
        $this->comment_id = $id;

        $this->dispatch('open-modal', id: 'delete-comment');
    }

    public function delete_comment()
    {
        $comment = ModelsReports::find($this->comment_id)->delete();

        Notification::make()
            ->title('Comment Deleted')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'delete-comment');
    }
}
