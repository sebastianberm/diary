<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use Livewire\Attributes\Rule;

class PeopleManager extends Component
{
    public $people;
    public $editingId = null;

    #[Rule('required|min:2')]
    public $name = '';

    #[Rule('required')]
    public $type = 'general';

    #[Rule('boolean')]
    public $is_own_child = false;

    public $keywords = ''; // Comma separated

    public function mount()
    {
        $this->loadPeople();
    }

    public function loadPeople()
    {
        $this->people = Person::all();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'is_own_child' => $this->is_own_child,
            'keywords' => array_map('trim', explode(',', $this->keywords)),
        ];

        if ($this->editingId) {
            Person::find($this->editingId)->update($data);
        } else {
            Person::create($data);
        }

        $this->reset(['name', 'type', 'is_own_child', 'keywords', 'editingId']);
        $this->loadPeople();
        $this->dispatch('close-modal', 'person-modal');
    }

    public function edit($id)
    {
        $person = Person::find($id);
        $this->editingId = $person->id;
        $this->name = $person->name;
        $this->type = $person->type;
        $this->is_own_child = $person->is_own_child;
        $this->keywords = implode(', ', $person->keywords ?? []);
        $this->dispatch('open-modal', 'person-modal');
    }

    public function delete($id)
    {
        Person::find($id)->delete();
        $this->loadPeople();
    }

    public function render()
    {
        return view('livewire.people-manager');
    }
}
