<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white dark:bg-primary-900 shadow sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">System Configuration</h3>

        @if (session()->has('message'))
            <div class="mb-4 text-green-600 dark:text-green-400 font-medium">{{ session('message') }}</div>
        @endif

        <div class="space-y-6">
            @foreach($settings->groupBy('group') as $group => $groupSettings)
                <div class="border-b border-gray-200 dark:border-primary-700 pb-4 last:border-0">
                    <h4 class="text-md font-bold text-gold-600 uppercase tracking-wider mb-3">{{ $group }}</h4>

                    <div class="grid gap-4">
                        @foreach($groupSettings as $index => $setting)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $setting->description ?? $setting->key }}
                                    </label>
                                    <span class="text-xs text-gray-500 font-mono">{{ $setting->key }}</span>
                                </div>

                                <div class="md:col-span-2">
                                    @if(isset($envValues[$setting->key]) && $envValues[$setting->key] !== null)
                                        <div
                                            class="flex items-center text-sm text-gray-500 bg-gray-50 dark:bg-primary-800 p-2 rounded border border-gray-200 dark:border-primary-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                            <span class="truncate flex-1">
                                                {{ $setting->type === 'password' ? '********' : $envValues[$setting->key] }}
                                            </span>
                                            <span
                                                class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">ENV</span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Managed by environment variable.</p>
                                    @else
                                            <div class="flex items-center">
                                                <input type="checkbox"
                                                    wire:model="settings.{{ $loop->parent->index * 100 + $index }}.value"
                                                    class="rounded border-gray-300 dark:border-primary-600 text-gold-600 shadow-sm focus:border-gold-300 focus:ring focus:ring-gold-200 focus:ring-opacity-50"
                                                    value="1" {{ $setting->value ? 'checked' : '' }}>
                                                <span
                                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $setting->value ? 'Enabled' : 'Disabled' }}</span>
                                            </div>
                                        @elseif($setting->type === 'number')
                                            <input type="number"
                                                wire:model="settings.{{ $loop->parent->index * 100 + $index }}.value"
                                                class="w-full rounded-md border-gray-300 dark:border-primary-600 bg-white dark:bg-primary-700 text-gray-700 dark:text-gray-200 focus:border-gold-500 focus:ring-gold-500 shadow-sm">
                                        @else
                                            <input type="{{ $setting->type === 'password' ? 'password' : 'text' }}"
                                                wire:model="settings.{{ $loop->parent->index * 100 + $index }}.value"
                                                class="w-full rounded-md border-gray-300 dark:border-primary-600 bg-white dark:bg-primary-700 text-gray-700 dark:text-gray-200 focus:border-gold-500 focus:ring-gold-500 shadow-sm">
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between items-center">
            <div>
                @if($testResult)
                    <span class="text-sm font-medium {{ $testResult['ok'] ? 'text-green-500' : 'text-red-500' }}">
                        {{ $testResult['msg'] }}
                    </span>
                @endif
            </div>
            <div class="space-x-2">
                <button wire:click="testImmich"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                    Test Immich
                </button>
                <button wire:click="save"
                    class="inline-flex items-center px-4 py-2 bg-gold-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gold-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition duration-150 ease-in-out">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>