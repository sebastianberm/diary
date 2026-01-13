<div class="p-6 bg-white dark:bg-primary-900 rounded-lg shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-serif font-bold text-gray-800 dark:text-gold-400">People & Contacts</h2>
        <button wire:click="$dispatch('open-modal', 'person-modal')"
            class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white rounded-md shadow transition">
            Add Person
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-sm text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-primary-800">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Type</th>
                    <th class="py-3 px-4">Is Own Child</th>
                    <th class="py-3 px-4">Keywords</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-primary-800">
                @foreach($people as $person)
                    <tr class="hover:bg-gray-50 dark:hover:bg-primary-800/50 transition">
                        <td class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200">{{ $person->name }}</td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst($person->type) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                            @if($person->is_own_child)
                                <span class="text-green-600 dark:text-green-400">Yes</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500 dark:text-gray-500">
                            {{ implode(', ', $person->keywords ?? []) }}
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button wire:click="edit({{ $person->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <button wire:click="delete({{ $person->id }})"
                                wire:confirm="Are you sure you want to delete {{ $person->name }}?"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <x-modal name="person-modal" focusable>
        <div class="p-6 bg-white dark:bg-primary-900">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $editingId ? 'Edit Person' : 'Add Person' }}
            </h2>

            <form wire:submit="save" class="mt-6 space-y-4">
                <div>
                    <x-input-label for="name" value="Name" class="dark:text-gray-300" />
                    <x-text-input wire:model="name" id="name" type="text"
                        class="mt-1 block w-full dark:bg-primary-800 dark:text-gray-100 dark:border-primary-700"
                        placeholder="e.g. Sebastian" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="type" value="Type" class="dark:text-gray-300" />
                        <select wire:model="type" id="type"
                            class="mt-1 block w-full border-gray-300 dark:border-primary-700 bg-white dark:bg-primary-800 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="general">General</option>
                            <option value="family">Family</option>
                            <option value="friend">Friend</option>
                            <option value="colleague">Colleague</option>
                            <option value="other">Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="flex items-center pt-6">
                        <label for="is_own_child" class="inline-flex items-center">
                            <input wire:model="is_own_child" id="is_own_child" type="checkbox"
                                class="rounded border-gray-300 dark:border-primary-700 get-blue-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-primary-800"
                                name="is_own_child">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Is my own child</span>
                        </label>
                    </div>
                </div>

                <div>
                    <x-input-label for="keywords" value="Keywords / Aliases (comma separated)"
                        class="dark:text-gray-300" />
                    <x-text-input wire:model="keywords" id="keywords" type="text"
                        class="mt-1 block w-full dark:bg-primary-800 dark:text-gray-100 dark:border-primary-700"
                        placeholder="e.g. Bas, Bassie" />
                    <x-input-error :messages="$errors->get('keywords')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')"
                        class="dark:bg-primary-800 dark:text-gray-300 dark:hover:bg-primary-700">
                        Cancel
                    </x-secondary-button>

                    <x-primary-button
                        class="ms-3 bg-gold-600 hover:bg-gold-700 dark:bg-gold-600 dark:hover:bg-gold-500 text-white">
                        Save
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>