<nav x-data="{ open: false }" class="bg-white dark:bg-primary-900 border-b border-gray-100 dark:border-primary-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="/logo.png" class="block h-10 w-auto" alt="Logo" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('messages.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('people')" :active="request()->routeIs('people')">
                        {{ __('messages.diary') }}
                    </x-nav-link>
                    <x-nav-link :href="route('settings')" :active="request()->routeIs('settings')">
                        {{ __('messages.settings') }}
                    </x-nav-link>
                    <x-nav-link :href="route('export')" :active="request()->routeIs('export')">
                        {{ __('messages.export') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme Toggle -->
                <div x-data="{
                    theme: localStorage.theme || 'system',
                    toggle() {
                        if (this.theme === 'system') this.theme = 'dark';
                        else if (this.theme === 'dark') this.theme = 'light';
                        else this.theme = 'system';

                        if (this.theme === 'system') {
                            localStorage.removeItem('theme');
                            if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');
                            else document.documentElement.classList.remove('dark');
                        } else {
                            localStorage.theme = this.theme;
                            if (this.theme === 'dark') document.documentElement.classList.add('dark');
                            else document.documentElement.classList.remove('dark');
                        }
                    },
                    get icon() {
                        if (this.theme === 'system') return '💻'; 
                        if (this.theme === 'dark') return '🌙'; 
                        return '☀️'; 
                    }
                }" class="mr-4">
                    <button @click="toggle()"
                        class="text-gray-500 hover:text-gold-500 dark:text-gray-400 dark:hover:text-gold-400 focus:outline-none transition"
                        :title="theme">
                        <span x-text="icon" class="text-lg"></span>
                    </button>
                </div>

                <!-- Language Switcher -->
                <div class="mr-4 flex items-center space-x-2">
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="text-sm {{ app()->getLocale() == 'en' ? 'font-bold text-gold-600' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'nl') }}"
                        class="text-sm {{ app()->getLocale() == 'nl' ? 'font-bold text-gold-600' : 'text-gray-500 hover:text-gray-700' }}">NL</a>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-primary-900 hover:text-gray-700 dark:hover:text-gold-400 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('messages.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('messages.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('people')" :active="request()->routeIs('people')">
                {{ __('People') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('export')" :active="request()->routeIs('export')">
                {{ __('Export') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('messages.profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('messages.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>