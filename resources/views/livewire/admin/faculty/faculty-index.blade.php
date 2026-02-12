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
        // Flux::toast('Faculty member added successfully.');
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
        // Flux::toast('Import completed successfully.');
    }
};
?>

<div class="space-y-4">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-lg font-black">Faculty Management</h1>

        <div>
            {{-- Import Faculty Date --}}
            <flux:modal.trigger name="import-faculty-modal">
                <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Faculty
                </flux:button>
            </flux:modal.trigger>

            {{-- Add Faculty Manually --}}
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
                <flux:subheading>Upload an Excel or CSV file to bulk add faculty members.
                    <span class="text-red-500 italic">*Only Excel or CSV files are allowed.</span>
                </flux:subheading>
            </div>

            <flux:input type="file" wire:model="file" size="sm" accept=".csv, .xlsx, .xls" />

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" size="sm">Start Import</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Manual Add Modal --}}
    <flux:modal name="add-faculty-modal" class="md:w-120">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Faculty</flux:heading>
                <flux:subheading>Enter the details for the new faculty account.</flux:subheading>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="first_name" label="First Name" size="sm" placeholder="John" />
                <flux:input wire:model="last_name" label="Last Name" size="sm" placeholder="Doe" />
            </div>

            <flux:input wire:model="email" type="email" label="Email Address" size="sm"
                placeholder="john@example.com" />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="branch" label="Branch" size="sm">
                    <flux:select.option value="Main">Main</flux:select.option>
                    <flux:select.option value="Extension">Extension</flux:select.option>
                </flux:select>

                <flux:input wire:model="department" label="Department" placeholder="IT Department" />
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary" size="sm">Create Account</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Faculty Data Table --}}
    <livewire:admin.faculty-profile-table />



</div>