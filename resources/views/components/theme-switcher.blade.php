@props([
'showLabel' => true,
'initialTheme' => 'system'
])

<flux:menu.item x-data="{
        theme: '{{ $initialTheme }}',
        label: 'System',

        init() {
            this.theme = localStorage.getItem('theme') || '{{ $initialTheme }}';
            this.applyTheme();

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.theme === 'system') this.applyTheme();
            });
        },

        toggleTheme() {
            if (this.theme === 'system') this.theme = 'dark';
            else if (this.theme === 'dark') this.theme = 'light';
            else this.theme = 'system';

            localStorage.setItem('theme', this.theme);
            this.applyTheme();
        },

        applyTheme() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = this.theme === 'dark' || (this.theme === 'system' && prefersDark);
            
            document.documentElement.classList.toggle('dark', isDark);

            // Update Label
            if (this.theme === 'dark') this.label = 'Dark Mode';
            else if (this.theme === 'light') this.label = 'Light Mode';
            else this.label = 'System Theme';
        }
    }" x-init="init()" @click.stop="toggleTheme()" class="group cursor-pointer">
    <div class="flex items-center justify-center">
        <div x-show="theme === 'light'" x-cloak>
            <flux:icon.sun
                class="size-5 text-zinc-400 dark:text-zinc-300 group-hover:text-zinc-800 dark:group-hover:text-white" />
        </div>
        <div x-show="theme === 'dark'" x-cloak>
            <flux:icon.moon
                class="size-5 text-zinc-400 dark:text-zinc-300 group-hover:text-zinc-800 dark:group-hover:text-white" />
        </div>
        <div x-show=" theme==='system'" x-cloak>
            <flux:icon.computer-desktop
                class="size-5 text-zinc-400 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-white" />
        </div>
    </div>

    @if($showLabel)
    <span x-text="label" class="ml-2"></span>
    @endif
</flux:menu.item>