@extends('layouts.master')

@section('content')
<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16">Update Documents</h5>
    </div>
    <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
        <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
            <a href="{{ route('driver.dashboard') }}" class="text-slate-400 dark:text-zink-200">Dashboard</a>
        </li>
        <li class="text-slate-700 dark:text-zink-100">
            Update Documents
        </li>
    </ul>
</div>

<div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">
    <div class="xl:col-span-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-4 text-15 text-slate-500 dark:text-zink-200">Please upload new versions of any documents you need to update.</h6>
                
                @if (session('info'))
                    <div class="p-3 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                        {{ session('info') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('driver.documents.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <!-- License -->
                        <div>
                            <label for="license_file" class="inline-block mb-2 text-base font-medium text-slate-800 dark:text-zink-100">Driver's License</label>
                            @if($profile && $profile->license_file_path)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($profile->license_file_path) }}" target="_blank" class="text-sm font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                        <i data-lucide="file-text" class="size-4"></i> View Current License
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="license_file" id="license_file" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="mt-1 text-sm text-slate-500 dark:text-zink-200">Upload new file to replace current.</p>
                        </div>

                        <!-- Registration -->
                        <div>
                            <label for="registration_file" class="inline-block mb-2 text-base font-medium text-slate-800 dark:text-zink-100">Vehicle Registration</label>
                            @if($profile && $profile->vehicle_registration_file_path)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($profile->vehicle_registration_file_path) }}" target="_blank" class="text-sm font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                        <i data-lucide="file-text" class="size-4"></i> View Current Registration
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="registration_file" id="registration_file" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="mt-1 text-sm text-slate-500 dark:text-zink-200">Upload new file to replace current.</p>
                        </div>

                        <!-- Gov ID -->
                        <div>
                            <label for="gov_id_file" class="inline-block mb-2 text-base font-medium text-slate-800 dark:text-zink-100">Government ID</label>
                            @if($profile && $profile->gov_id_file_path)
                                <div class="mb-2">
                                    <a href="{{ Storage::url($profile->gov_id_file_path) }}" target="_blank" class="text-sm font-medium text-custom-500 hover:text-custom-600 flex items-center gap-1">
                                        <i data-lucide="file-text" class="size-4"></i> View Current ID
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="gov_id_file" id="gov_id_file" class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200" accept=".pdf,.jpg,.jpeg,.png">
                            <p class="mt-1 text-sm text-slate-500 dark:text-zink-200">Upload new file to replace current.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-5">
                        <a href="{{ route('driver.dashboard') }}" class="text-red-500 bg-white btn hover:text-red-500 hover:bg-red-100 focus:text-red-500 focus:bg-red-100 active:text-red-500 active:bg-red-100 dark:bg-zink-700 dark:hover:bg-red-500/10 dark:focus:bg-red-500/10 dark:active:bg-red-500/10">Cancel</a>
                        <button type="submit" class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Update & Resubmit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
