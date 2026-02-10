<?php
use App\Models\User;
use App\Models\FacultyProfile;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isModalOpen = false;
    public $file; 

    // Unified Form fields
    public $editingId = null;
    public $first_name, $last_name, $email, $department, $branch;
    public $academic_rank, $contactno, $address, $sex, $birthday;

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Note: Targeting the 'users' table for email uniqueness as that's the primary auth record
            'email' => 'required|email|unique:users,email,' . $this->editingId,
            'department' => 'required',
        ];
    }

    public function saveFaculty()
    {
        $this->validate();

        DB::transaction(function () {
            $userData = [
                'name' => "{$this->first_name} {$this->last_name}",
                'email' => $this->email,
            ];

            if ($this->editingId) {
                $user = User::findOrFail($this->editingId);
                $user->update($userData);
                $user->profile()->update($this->getProfileData());
            } else {
                $user = User::create(array_merge($userData, [
                    'password' => Hash::make('password123'),
                ]));
                $user->assignRole('faculty');
                $user->profile()->create($this->getProfileData());
            }
        });

        $this->resetForm();
        $this->isModalOpen = false;
    }

    private function getProfileData() 
    {
        return [
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'email'         => $this->email,
            'department'    => $this->department,
            'branch'        => $this->branch,
            'academic_rank' => $this->academic_rank,
            'contactno'     => $this->contactno,
            'address'       => $this->address,
            'sex'           => $this->sex,
            'birthday'      => $this->birthday,
            'updated_by'    => auth()->id(),
        ];
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        $this->editingId = $user->id;
        $this->first_name = $user->profile->first_name ?? '';
        $this->last_name = $user->profile->last_name ?? '';
        $this->email = $user->email;
        $this->department = $user->profile->department ?? '';
        $this->branch = $user->profile->branch ?? '';
        // Map other fields as necessary...
        
        $this->isModalOpen = true;
    }

    public function importExcel()
    {
        $this->validate(['file' => 'required|mimes:xlsx,csv|max:10240']);
        
        $rows = Excel::toCollection(collect(), $this->file)->first()->skip(1);

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $user = User::updateOrCreate(
                    ['email' => $row[6]],
                    ['name' => "{$row[0]} {$row[2]}", 'password' => Hash::make('password123')]
                );

                if ($user->wasRecentlyCreated) {
                    $user->assignRole('faculty');
                }

                $user->profile()->updateOrCreate(['user_id' => $user->id], [
                    'first_name'    => $row[0],
                    'middle_name'   => $row[1],
                    'last_name'     => $row[2],
                    'branch'        => $row[3],
                    'department'    => $row[4],
                    'academic_rank' => $row[5],
                    'email'         => $row[6],
                    'contactno'     => $row[7],
                    'address'       => $row[8],
                    'sex'           => $row[9],
                    'birthday'      => $row[10],
                ]);
            }
        });

        $this->reset('file');
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'first_name', 'last_name', 'email', 'department', 'branch', 'academic_rank', 'contactno', 'address', 'sex', 'birthday']);
    }

    public function deleteFaculty($id) 
    {
        User::findOrFail($id)->delete();
    }

    public function with() 
    {
        return [
            'members' => User::role('faculty')
                ->with('profile')
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->paginate(10)
        ];
    }
}; ?>

<div class="space-y-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <flux:heading size="lg" level="1">Faculty Management</flux:heading>

        {{-- <div class="flex flex-wrap gap-2 items-center">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search faculty..."
                icon="magnifying-glass" />
            <div class="flex items-center border rounded-lg p-1 bg-white">
                <input type="file" wire:model="file"
                    class="text-xs file:mr-4 file:py-1 file:px-2 file:border-0 file:text-xs file:bg-gray-100">
                <flux:button wire:click="importExcel" variant="ghost" size="sm" loading>Import</flux:button>
            </div>
            <flux:button wire:click="$set('isModalOpen', true)" variant="primary" icon="plus">Add Faculty</flux:button>
        </div> --}}
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($members as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        <div class="font-medium">{{ $user->name }}</div>
                        <div class="text-xs ">{{ $user->email }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ $user->profile->department ?? 'Unassigned' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-1">
                            <flux:button wire:click="edit({{ $user->id }})" icon="pencil" variant="ghost" size="sm" />
                            <flux:button wire:click="deleteFaculty({{ $user->id }})"
                                wire:confirm="Are you sure you want to delete this faculty member?" icon="trash"
                                variant="ghost" color="red" size="sm" />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @if($members->hasPages())
        <div class="mt-4">
            {{ $members->links() }}
        </div>
        @endif
    </flux:card>

    <flux:modal wire:model="isModalOpen" class="md:w-1/2" @modal-close="resetForm">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Faculty' : 'Add New Faculty' }}</flux:heading>

            <form wire:submit="saveFaculty" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="first_name" label="First Name" required />
                <flux:input wire:model="last_name" label="Last Name" required />
                <flux:input wire:model="email" label="Email Address" class="md:col-span-2" required />
                <flux:input wire:model="department" label="Department" required />
                <flux:input wire:model="branch" label="Branch" />

                <div class="md:col-span-2 flex justify-end gap-2 pt-4">
                    <flux:button x-on:click="$dispatch('modal-close')" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update Faculty' : 'Save Faculty' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>