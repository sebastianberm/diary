<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Modelable;

class RichEditor extends Component
{
    #[Modelable]
    public $content;

    public $people = [];
    public $scanDelay = 1000;

    public function mount()
    {
        // Pass names/keywords to JS
        $this->people = \App\Models\Person::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'keywords' => $p->keywords ?? []
            ];
        })->toArray();

        $this->scanDelay = (int) \App\Services\SystemConfig::get('scan_delay', 1000);
    }

    public function render()
    {
        return view('livewire.rich-editor');
    }
}
