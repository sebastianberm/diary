<div class="max-w-4xl mx-auto space-y-6">
    <!-- Date Navigation -->
    <div class="flex items-center justify-between bg-white dark:bg-primary-900 p-4 rounded-lg shadow">
        <button wire:click="$set('date', '{{ \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d') }}')"
            class="p-2 text-gray-600 hover:text-gold-600 dark:text-gray-400 dark:hover:text-gold-400 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <div class="text-center">
            <h2 class="text-xl font-serif font-bold text-gray-800 dark:text-gold-400">
                {{ \Carbon\Carbon::parse($date)->format('l, j F Y') }}
            </h2>
            <input type="date" wire:model.live="date"
                class="mt-1 block mx-auto text-sm border-none bg-transparent text-gray-500 dark:text-gray-400 focus:ring-0 cursor-pointer">
        </div>

        <button wire:click="$set('date', '{{ \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d') }}')"
            class="p-2 text-gray-600 hover:text-gold-600 dark:text-gray-400 dark:hover:text-gold-400 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Mood Selector -->
    <div class="bg-white dark:bg-primary-900 p-6 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
            {{ __('messages.how_was_day') }}
        </h3>
        <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
            @foreach($moods as $mood)
                <button wire:click="$set('mood_id', {{ $mood->id }})"
                    class="group flex flex-col items-center p-3 rounded-lg transition-all duration-200 
                                    {{ $mood_id == $mood->id ? 'bg-gold-50 ring-2 ring-gold-500 dark:bg-primary-800' : 'hover:bg-gray-50 dark:hover:bg-primary-800' }}">
                    <span
                        class="text-3xl mb-1 transform group-hover:scale-110 transition-transform">{{ $mood->icon }}</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 font-medium">{{ __($mood->name) }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Editor -->
    <div class="bg-white dark:bg-primary-900 p-6 rounded-lg shadow min-h-[600px] flex flex-col">
        <livewire:rich-editor wire:model="content" />

        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-primary-800 mt-4">
            <span class="text-sm text-gray-400 italic" wire:loading
                wire:target="save">{{ __('messages.saving') }}</span>
            <span class="text-sm text-green-500" x-data="{ show: false }"
                x-init="@this.on('message', () => { show = true; setTimeout(() => show = false, 2000) })" x-show="show"
                x-transition.opacity>
                {{ __('messages.saved_successfully') }}
            </span>
            <button wire:click="save"
                class="px-6 py-2 bg-gold-600 hover:bg-gold-700 text-white rounded-md shadow transition font-medium">
                {{ __('messages.save_entry') }}
            </button>
        </div>

        <!-- Context / Interactions -->
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-primary-800 flex items-center justify-between">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs font-bold text-gray-400 uppercase mr-2">{{ __('messages.mentioned') }}</span>
                @forelse($detectedPeople as $person)
                    <span
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ $person->name }}
                        <button wire:click="removeInteraction({{ $person->id }})"
                            class="ml-1 text-indigo-400 hover:text-indigo-600 dark:hover:text-white">
                            &times;
                        </button>
                    </span>
                @empty
                    <span class="text-xs text-gray-400 italic">{{ __('messages.no_one_detected') }}</span>
                @endforelse
            </div>

            <button wire:click="scan" wire:loading.attr="disabled"
                class="text-xs flex items-center text-gray-500 hover:text-gold-600 hover:bg-gray-50 dark:hover:bg-primary-800 px-2 py-1 rounded transition">
                <svg wire:loading.class="animate-spin" class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
                {{ __('messages.scan_context') }}
            </button>
        </div>
    </div>