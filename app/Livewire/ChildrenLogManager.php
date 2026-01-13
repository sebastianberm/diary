<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\ChildrenLog;
use App\Models\Person;
use Carbon\Carbon;

use Livewire\Attributes\On;

class ChildrenLogManager extends Component
{
    public $date;
    public $logs = []; // [person_id => ['status' => ..., 'notes' => ...]]

    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
        $this->loadLogs();
    }

    #[On('date-changed')]
    public function updateDate($date)
    {
        $this->date = $date;
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $children = Person::where('is_own_child', true)->get();
        $this->logs = [];

        foreach ($children as $child) {
            $log = ChildrenLog::where('person_id', $child->id)
                ->where('date', $this->date)
                ->first();

            $this->logs[$child->id] = [
                'name' => $child->name,
                'status' => $log ? $log->status : 'with_me',
                'notes' => $log ? $log->notes : '',
            ];
        }
    }

    public function save()
    {
        foreach ($this->logs as $personId => $data) {
            ChildrenLog::updateOrCreate(
                [
                    'person_id' => $personId,
                    'date' => $this->date,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                ]
            );
        }

        session()->flash('children_message', 'Status updated!');
    }

    public function render()
    {
        return view('livewire.children-log-manager');
    }
}
