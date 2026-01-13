<div class="mt-8 bg-white dark:bg-primary-900 p-6 rounded-lg shadow">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-serif font-bold text-gray-800 dark:text-gold-400">Children's Log</h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ \Carbon\Carbon::parse($date)->format('l, j F') }}
        </span>
    </div>

    @if(count($logs) > 0)
        <div class="space-y-6">
            @foreach($logs as $personId => $data)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-primary-800 border border-gray-100 dark:border-primary-700">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="sm:w-1/3">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $data['name'] }}</label>
                            <select wire:model="logs.{{ $personId }}.status"
                                class="w-full rounded-md border-gray-300 dark:border-primary-600 bg-white dark:bg-primary-700 text-gray-700 dark:text-gray-200 focus:border-gold-500 focus:ring-gold-500 text-sm">
                                <option value="with_me">Thuis (With me)</option>
                                <option value="with_other_parent">Andere ouder</option>
                                <option value="school">School / Opvang</option>
                                <option value="sick">Ziek</option>
                                <option value="holiday_with_me">Vakantie (Mij)</option>
                                <option value="holiday_other">Vakantie (Ander)</option>
                                <option value="visiting_others">Logeren</option>
                            </select>
                        </div>
                        <div class="sm:w-2/3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea wire:model="logs.{{ $personId }}.notes" rows="2"
                                class="w-full rounded-md border-gray-300 dark:border-primary-600 bg-white dark:bg-primary-700 text-gray-700 dark:text-gray-200 focus:border-gold-500 focus:ring-gold-500 text-sm placeholder-gray-400 dark:placeholder-primary-500"
                                placeholder="Any specifics?"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end items-center pt-2">
                <span class="text-sm text-green-500 mr-4" x-data="{ show: false }"
                    x-init="@this.on('children_message', () => { show = true; setTimeout(() => show = false, 2000) })"
                    x-show="show" x-transition.opacity>
                    Updated!
                </span>
                <button wire:click="save"
                    class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white rounded-md shadow transition text-sm font-medium">
                    Update Status
                </button>
            </div>
        </div>
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p>No children configured yet.</p>
            <a href="{{ route('people') }}"
                class="text-gold-600 hover:text-gold-500 text-sm underline mt-2 inline-block">Manage People</a>
        </div>
    @endif
</div>