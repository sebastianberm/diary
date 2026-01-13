<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\DiaryEntry;
use App\Models\Mood;
use Carbon\Carbon;
use Livewire\Attributes\Rule;

class DiaryEntryManager extends Component
{
    protected $listeners = ['request-scan' => 'scan'];

    public $date;

    #[Rule('required')]
    public $content;

    #[Rule('nullable|exists:moods,id')]
    public $mood_id;

    public $moods;
    public $interactions = []; // Array of person_ids
    public $detectedPeople = []; // Models for UI display

    public function mount($date = null)
    {
        $this->date = $date ?? Carbon::today()->format('Y-m-d');
        $this->moods = Mood::all();
        $this->loadEntry();
    }

    public function updatedDate()
    {
        $this->loadEntry();
        $this->dispatch('date-changed', $this->date);
    }

    public function loadEntry()
    {
        $entry = DiaryEntry::with('interactions.person')->where('user_id', auth()->id())
            ->where('date', $this->date)
            ->first();

        if ($entry) {
            $this->content = $entry->content;
            $this->mood_id = $entry->mood_id;
            $this->interactions = $entry->interactions->pluck('person_id')->toArray();
        } else {
            $this->content = '';
            $this->mood_id = null;
            $this->interactions = [];
        }

        $this->loadDetectedPeople();
    }

    public function loadDetectedPeople()
    {
        if (!empty($this->interactions)) {
            $this->detectedPeople = \App\Models\Person::whereIn('id', $this->interactions)->get();
        } else {
            $this->detectedPeople = [];
        }
    }

    public function scan()
    {
        $service = new \App\Services\ContextService();
        $detectedIds = $service->detectPeople($this->content);

        // Merge with existing (don't remove manual ones, but maybe we want a toggle? For now just add)
        $this->interactions = array_unique(array_merge($this->interactions, $detectedIds));

        $this->loadDetectedPeople();

        if (count($detectedIds) > 0) {
            session()->flash('message', 'Detected ' . count($detectedIds) . ' people!');
        } else {
            session()->flash('message', 'No new mentions found.');
        }
    }

    public function removeInteraction($personId)
    {
        $this->interactions = array_diff($this->interactions, [$personId]);
        $this->loadDetectedPeople();
    }

    public function save()
    {
        $this->validate();

        $entry = DiaryEntry::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'date' => $this->date,
            ],
            [
                'content' => $this->content,
                'mood_id' => $this->mood_id,
                'metadata' => [],
            ]
        );

        // Sync Interactions
        // We delete all and re-insert for simplicity, or sync if ManyToMany (but we have custom pivot model)
        // Let's use delete insert for the Pivot Table "entry_interactions"
        \App\Models\EntryInteraction::where('entry_id', $entry->id)->delete();

        foreach ($this->interactions as $personId) {
            \App\Models\EntryInteraction::create([
                'entry_id' => $entry->id,
                'person_id' => $personId,
                'source' => 'scan', // Default source
            ]);
        }

        session()->flash('message', 'Dagboek opgeslagen!');
    }

    public function render()
    {
        return view('livewire.diary-entry-manager');
    }
}
