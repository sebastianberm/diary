<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:diary-entry-manager />
            <livewire:immich-gallery />
            <livewire:children-log-manager />
        </div>
    </div>
</x-app-layout>