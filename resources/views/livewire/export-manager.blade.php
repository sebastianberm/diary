<div class="max-w-4xl mx-auto py-12">
    <div class="bg-white dark:bg-primary-900 shadow rounded-lg p-6">
        <h2 class="text-2xl font-serif font-bold text-gray-800 dark:text-gold-400 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            Export Diary to PDF
        </h2>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="export" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" wire:model="startDate"
                        class="w-full rounded-md border-gray-300 dark:border-primary-600 dark:bg-primary-700 text-gray-900 dark:text-gray-100 focus:border-gold-500 focus:ring-gold-500">
                    @error('startDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" wire:model="endDate"
                        class="w-full rounded-md border-gray-300 dark:border-primary-600 dark:bg-primary-700 text-gray-900 dark:text-gray-100 focus:border-gold-500 focus:ring-gold-500">
                    @error('endDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Options -->
            <div class="bg-gray-50 dark:bg-primary-800 p-4 rounded-md">
                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Options</h4>

                <div class="flex items-center">
                    <input type="checkbox" id="frontPage" wire:model="includeFrontPage"
                        class="rounded border-gray-300 dark:border-primary-600 text-gold-600 shadow-sm focus:border-gold-300 focus:ring focus:ring-gold-200 focus:ring-opacity-50">
                    <label for="frontPage" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                        Include Front Page (Title & Date Range)
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-primary-700">
                <div wire:loading wire:target="export"
                    class="mr-4 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Generating...
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-2 bg-gold-600 hover:bg-gold-700 text-white rounded-md shadow font-medium transition disabled:opacity-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export PDF
                </button>
            </div>

            <p class="text-xs text-gray-500 mt-2 text-right">
                * Exports longer than 31 days will be processed in the background.
            </p>
        </form>
    </div>
</div>