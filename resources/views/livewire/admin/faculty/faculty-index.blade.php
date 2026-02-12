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

}; ?>

<div class="space-y-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-lg font-black">Faculty Management</h1>
    </div>

    {{-- Faculty Data Table --}}
    <livewire:admin.faculty-profile-table />

</div>