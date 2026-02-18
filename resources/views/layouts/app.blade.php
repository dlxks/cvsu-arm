<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800  max-w-7xl mx-auto">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <flux:sidebar sticky collapsible="mobile"
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

            {{-- Sidebar Header --}}
            <flux:sidebar.header>
                <a href="{{ route('dashboard.resolve') }}" wire:navigate
                    class="flex items-center px-2 gap-2 text-sm font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                    <img src="{{ asset('images/CvSU-Logo.png') }}" alt="CvSU Icon" class="w-6">
                    CvSU - ARM
                </a>

                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            {{-- Navigation --}}
            <flux:sidebar.nav>
                <flux:sidebar.group heading="Main" class="grid">
                    <flux:sidebar.item icon="home" class="mb-2" :href="route('dashboard.resolve')"
                        :current="request()->routeIs('admin.dashboard', 'faculty.dashboard')" wire:navigate>
                        Dashboard
                    </flux:sidebar.item>

                    <flux:separator />

                    {{-- Teaching --}}
                    <flux:sidebar.group heading="Teaching" class="grid mb-4">
                        <flux:sidebar.item icon="clipboard-document-list" href="#">
                            Schedules & Subjects
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="check-badge" href="#">Grades</flux:sidebar.item>
                        <flux:sidebar.item icon="academic-cap" href="#">Teaching History</flux:sidebar.item>
                    </flux:sidebar.group>

                    <flux:separator />

                    {{-- Research --}}
                    <flux:sidebar.group heading="Research & Extension" class="grid mb-4">
                        <flux:sidebar.item icon="book-open" href="#">Researches</flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" href="#">Extensions</flux:sidebar.item>
                    </flux:sidebar.group>

                    @role('admin')
                    <flux:separator />

                    <flux:sidebar.group class="grid">

                        <flux:sidebar.group expandable heading="Branch Management" class="grid mb-4">
                            <flux:sidebar.item icon="building-library" href="#">
                                Branches / Colleges
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="briefcase" href="#">
                                Departments
                            </flux:sidebar.item>
                        </flux:sidebar.group>

                        <flux:separator />

                        <flux:sidebar.group expandable heading="System" class="grid mb-4">
                            <flux:sidebar.item icon="identification" :href="route('admin.faculty')"
                                :current="request()->routeIs('admin.faculty')" wire:navigate>
                                Faculty List
                            </flux:sidebar.item>

                            <flux:sidebar.item icon="users" href="#">
                                Users
                            </flux:sidebar.item>
                        </flux:sidebar.group>

                    </flux:sidebar.group>
                    @endrole
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />

        </flux:sidebar>

        {{-- Content Area --}}
        <div class="flex-1 flex flex-col">

            {{-- Mobile Header --}}
            <flux:header>
                <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

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
                                Settings
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer">
                                Log Out
                            </flux:menu.item>
                        </form>

                    </flux:menu>
                </flux:dropdown>
            </flux:header>

            {{-- Main Content --}}
            <flux:main class="flex-1 p-6 w-full">
                {{ $slot }}
            </flux:main>

        </div>

    </div>

    @fluxScripts
</body>

</html>