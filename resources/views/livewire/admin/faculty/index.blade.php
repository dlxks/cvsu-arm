<?php
use App\Models\User;
use App\Models\FacultyProfile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $branch = '';
    public $department = '';
    public $file;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'branch' => 'required|string',
        'department' => 'required|string',
    ];

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => "{$validated['first_name']} {$validated['last_name']}",
                'email'    => $validated['email'],
                'password' => Hash::make('password'),
            ]);

            $user->assignRole('faculty');

            FacultyProfile::create([
                'user_id'    => $user->id,
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'branch'     => $validated['branch'],
                'department' => $validated['department'],
            ]);
        });

        Flux::modal('add-faculty-modal')->close();
        $this->dispatch('pg:eventRefresh-facultyProfileTable');
        $this->reset();
        Flux::toast('Faculty member added successfully.');
    }

    public function import()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            public function model(array $row)
            {
                return DB::transaction(function () use ($row) {
                    $user = User::firstOrCreate(
                        ['email' => $row['email']],
                        [
                            'name' => "{$row['first_name']} {$row['last_name']}",
                            'password' => Hash::make('password')
                        ]
                    );

                    $user->assignRole('faculty');

                    return FacultyProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'first_name' => $row['first_name'],
                            'last_name'  => $row['last_name'],
                            'email'      => $row['email'],
                            'branch'     => $row['branch'],
                            'department' => $row['department'],
                        ]
                    );
                });
            }
        }, $this->file);

        Flux::modal('import-faculty-modal')->close();
        $this->dispatch('pg:eventRefresh-facultyProfileTable');
        $this->reset('file');
        Flux::toast('Import completed successfully.');
    }
};
?>

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-lg font-black">Faculty Management</h1>

        <div>
            <flux:modal.trigger name="import-faculty-modal">
                <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Faculty</flux:button>
            </flux:modal.trigger>

            <flux:modal.trigger name="add-faculty-modal">
                <flux:button variant="primary" size="sm" icon="user-plus">Add Faculty</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Import Modal --}}
    <flux:modal name="import-faculty-modal" class="md:w-96">
        <form wire:submit="import" class="space-y-4">
            <div>
                <flux:heading size="lg">Import Faculty</flux:heading>
                <flux:subheading>Upload an Excel or CSV file to bulk add faculty members.</flux:subheading>
            </div>

            {{-- Progress Logic for both Uploading and Processing --}}
            <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress" class="space-y-3">
                <flux:input type="file" wire:model="file" size="sm" accept=".csv, .xlsx, .xls" />

                {{-- 1. Progress Bar for File Upload --}}
                <div x-show="uploading">
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-medium text-blue-700 dark:text-blue-400">Uploading File...</span>
                        <span class="text-xs font-medium text-blue-700 dark:text-blue-400"
                            x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            x-bind:style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                {{-- 2. Progress Indicator for Server-Side Import --}}
                <div wire:loading wire:target="import" class="w-full">
                    <div class="flex justify-between mb-1">
                        <span class="text-xs font-medium text-amber-700 dark:text-amber-400">Processing
                            records...</span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-500 h-2 rounded-full animate-progress-indeterminate"></div>
                    </div>
                </div>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled"
                    wire:target="import">
                    <span wire:loading.remove wire:target="import">Start Import</span>
                    <span wire:loading wire:target="import" class="flex items-center gap-2">
                        <flux:icon.arrow-path class="animate-spin w-4 h-4" />
                        Please wait...
                    </span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Manual Add Modal --}}
    <flux:modal name="add-faculty-modal" class="md:w-120">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Faculty</flux:heading>
                <flux:subheading>Enter details for manual creation.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="first_name" label="First Name" size="sm" />
                <flux:input wire:model="last_name" label="Last Name" size="sm" />
            </div>

            <flux:input wire:model="email" type="email" label="Email Address" size="sm" />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="branch" label="Branch" size="sm">
                    <flux:select.option value="Main">Main</flux:select.option>
                    <flux:select.option value="Extension">Extension</flux:select.option>
                </flux:select>
                <flux:input wire:model="department" label="Department" size="sm" />
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" size="sm" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Create Account</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Table Section with Loading --}}
    <div class="relative min-h-100">
        <div wire:loading wire:target="import, save"
            class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800">
            <flux:icon.arrow-path class="animate-spin w-10 h-10 text-primary mb-2" />
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Updating Faculty Table...</p>
        </div>
        <livewire:admin.faculty-profile-table lazy />
    </div>
</div>