<div class="mt-8 bg-white dark:bg-primary-900 p-6 rounded-lg shadow">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-serif font-bold text-gray-800 dark:text-gold-400 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                </path>
            </svg>
            Daily Photos
        </h3>

        <div class="flex items-center gap-4">
            @if($isConnected)
                <button wire:click="refresh" wire:loading.attr="disabled"
                    class="text-xs flex items-center text-gray-500 hover:text-gold-600 transition">
                    <svg wire:loading.class="animate-spin" class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Refresh
                </button>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Found: {{ count($assets) }}
                </span>
            @else
                <a href="{{ route('settings') }}" class="text-sm text-red-500 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    Configure Immich
                </a>
            @endif
        </div>
    </div>

    @if($isConnected)
        @if(count($assets) > 0)
            <div
                class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4 max-h-[400px] overflow-y-auto p-1 custom-scrollbar">
                @foreach($assets as $asset)
                    <div wire:click="toggleAsset('{{ $asset['id'] }}')"
                        class="relative group cursor-pointer aspect-square rounded-lg overflow-hidden border-2 transition-all duration-200 
                                                             {{ isset($selectedAssets[$asset['id']]) ? 'border-gold-500 ring-2 ring-gold-200 dark:ring-primary-700' : 'border-transparent hover:border-gray-300 dark:hover:border-primary-600' }}">

                        <div class="relative w-full h-full">
                            <img src="{{ $asset['thumbUrl'] }}"
                                class="w-full h-full object-cover transition-transform group-hover:scale-105" loading="lazy"
                                onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'flex items-center justify-center w-full h-full bg-gray-100 dark:bg-gray-800 text-gray-400 text-xs text-center p-1\'><span>⚠️<br>Not Found</span></div>';">
                        </div>

                        @if(isset($selectedAssets[$asset['id']]))
                            <div class="absolute inset-0 bg-gold-900/20 flex items-center justify-center">
                                <div class="bg-gold-500 rounded-full p-1 text-white shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Captions for selected photos -->
            @if(count($selectedAssets) > 0)
                <div class="mt-6 border-t border-gray-100 dark:border-primary-700 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Photos</h4>
                    <div class="space-y-3">
                        @foreach($selectedAssets as $id => $data)
                            <div class="flex gap-3 items-start p-2 bg-gray-50 dark:bg-primary-800 rounded">
                                <div class="w-12 h-12 flex-shrink-0 bg-gray-200 rounded overflow-hidden">
                                    <svg class="w-full h-full text-gray-400 p-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <input type="text" wire:model="selectedAssets.{{ $id }}.caption"
                                        class="w-full text-sm border-0 border-b border-gray-200 dark:border-primary-600 bg-transparent focus:ring-0 focus:border-gold-500 placeholder-gray-400"
                                        placeholder="Add a caption...">
                                </div>
                                <button wire:click="toggleAsset('{{ $id }}')" class="text-gray-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end items-center mt-4">
                        <span class="text-sm text-green-500 mr-4" x-data="{ show: false }"
                            x-init="@this.on('photo_message', () => { show = true; setTimeout(() => show = false, 2000) })"
                            x-show="show" x-transition.opacity>
                            Selection Saved!
                        </span>
                        <button wire:click="save"
                            class="px-4 py-2 bg-gold-600 hover:bg-gold-700 text-white rounded-md shadow transition text-sm font-medium">
                            Save Photos & Download
                        </button>
                    </div>
                </div>
            @endif

        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-primary-800/50 rounded-lg">
                <p>No photos found for this date.</p>
                <button wire:click="refresh" class="text-sm text-gold-600 hover:underline mt-2">Try Refreshing</button>
            </div>
        @endif
    @endif
</div>