<?php

namespace App\Livewire;

use App\Exceptions\DuplicateVoteException;
use App\Exceptions\VoteNotFoundException;
use App\Livewire\Traits\WithAuthRedirects;
use App\Models\Idea;

use Livewire\Attributes\On;

use Livewire\Component;

class IdeaShow extends Component
{


    use WithAuthRedirects;

    public $idea;
    public $votesCount;
    public $hasVoted;

    protected $listeners = [
        'statusWasUpdated',
        'statusWasUpdatedError',
        'ideaWasUpdated',
        'ideaWasMarkedAsSpam',
        'ideaWasMarkedAsNotSpam',
        'commentWasAdded',
        'commentWasDeleted',
    ];

    public function mount(Idea $idea, $votesCount)
    {
        $this->idea = $idea;
        $this->votesCount = $votesCount;
        $this->hasVoted = $idea->isVotedByUser(auth()->user());
    }

    #[On('statusWasUpdated')]
    public function statusWasUpdated()
    {
        $this->idea->refresh();
    }

    #[On('statusWasUpdatedError')]
    public function statusWasUpdatedError()
    {
        $this->idea->refresh();
    }

    #[On('ideaWasUpdated')]
    public function ideaWasUpdated()
    {
        $this->idea->refresh();
    }

    #[On('ideaWasMarkedAsSpam')]
    public function ideaWasMarkedAsSpam()
    {
        $this->idea->refresh();
    }

    #[On('ideaWasMarkedAsNotSpam')]
    public function ideaWasMarkedAsNotSpam()
    {
        $this->idea->refresh();
    }

    #[On('commentWasAdded')]
    public function commentWasAdded()
    {
        $this->idea->refresh();
    }

    #[On('commentWasDeleted')]
    public function commentWasDeleted()
    {
        $this->idea->refresh();
    }

    public function vote()
    {
        if (auth()->guest()) {
            return $this->redirectToLogin();
        }

        if ($this->hasVoted) {
            try {
                $this->idea->removeVote(auth()->user());
            } catch (VoteNotFoundException $e) {
                // do nothing
            }
            $this->votesCount--;
            $this->hasVoted = false;
        } else {
            try {
                $this->idea->vote(auth()->user());
            } catch (DuplicateVoteException $e) {
                // do nothing
            }
            $this->votesCount++;
            $this->hasVoted = true;
        }
    }


    public function render()
    {
        return view('livewire.idea-show');
    }
}
