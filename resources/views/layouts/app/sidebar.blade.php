<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        {{-- Sidebar Header --}}
        <flux:sidebar.header>
            <a href="{{ route('dashboard.resolve') }}" wire:navigate
                class="flex items-center px-2 text-sm font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                CvSU - ARM
            </a>

            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        {{-- Main Navigation --}}
        <flux:sidebar.nav>
            <flux:sidebar.group heading="Main" class="grid">
                {{-- Dashboard Link --}}
                <flux:sidebar.item icon="home" :href="route('dashboard.resolve')" :current="request()->routeIs(
                        'admin.dashboard',
                        'faculty.dashboard'
                    )" wire:navigate>
                    Dashboard
                </flux:sidebar.item>

                <flux:separator />

                {{-- Teaching --}}
                <flux:sidebar.group heading="Teaching" class="grid">
                    <flux:sidebar.item icon="clipboard-document-list" href="#">
                        Schedules &amp; Subjects
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="check-badge" href="#">Grades</flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" href="#">Teaching History</flux:sidebar.item>
                </flux:sidebar.group>

                <flux:separator />

                {{-- Research & Extensions --}}
                <flux:sidebar.group heading="Research & Extension" class="grid">
                    <flux:sidebar.item icon="book-open" href="#">Researches</flux:sidebar.item>
                    <flux:sidebar.item icon="puzzle-piece" href="#">Extensions</flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.group>

            <flux:separator />

            {{-- User Management --}}
            <flux:sidebar.group expandable heading="System" class="grid">
                <flux:sidebar.item icon="identification" href="#">Faculty List</flux:sidebar.item>
                <flux:sidebar.item icon="users" href="#">Users</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        {{-- User --}}
        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    {{-- Mobile Header --}}
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 leading-tight">
                                <flux:heading class="truncate">
                                    {{ auth()->user()->name }}
                                </flux:heading>
                                <flux:text class="truncate">
                                    {{ auth()->user()->email }}
                                </flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <x-theme-switcher initialTheme="light" />

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>