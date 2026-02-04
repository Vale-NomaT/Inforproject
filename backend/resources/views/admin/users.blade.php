@extends('layouts.master')

@section('title', 'Users Management')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="flex flex-col gap-4 mb-5 md:flex-row md:items-center md:justify-between">
            <div>
                <h5 class="text-16">Users List</h5>
                <p class="text-slate-500 dark:text-zink-200">View and manage all users on SafeRide Kids.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-sm">
                    <span class="text-slate-600 dark:text-zink-200">
                        Filter by type:
                    </span>
                    <select
                        name="type"
                        class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 rounded-md py-1.5 px-3"
                        onchange="this.form.submit()"
                    >
                        <option value="">All</option>
                        <option value="parent" @if ($filterType === 'parent') selected @endif>Parents</option>
                        <option value="driver" @if ($filterType === 'driver') selected @endif>Drivers</option>
                        <option value="admin" @if ($filterType === 'admin') selected @endif>Admins</option>
                    </select>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="px-4 py-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-500/20 dark:text-green-400" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($users->isEmpty())
            <div class="text-center py-10">
                <div class="avatar-md mx-auto mb-4">
                    <div class="avatar-title bg-slate-100 text-slate-500 rounded-full text-2xl dark:bg-zink-600 dark:text-zink-200">
                        <i data-lucide="users"></i>
                    </div>
                </div>
                <h5 class="text-16 mb-2">No users found</h5>
                <p class="text-slate-500 dark:text-zink-200">Try adjusting your filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap table-auto">
                    <thead class="bg-slate-100 dark:bg-zink-600 text-slate-500 dark:text-zink-200">
                        <tr>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-left">Name</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-left">Email</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-left">Type</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-left">Status</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-left">Reason</th>
                            <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-zink-500">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-slate-500 dark:text-zink-200">
                                    <h6 class="text-15 mb-0 text-slate-700 dark:text-zink-100">{{ $user->name }}</h6>
                                </td>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-slate-500 dark:text-zink-200">
                                    {{ $user->email }}
                                </td>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-slate-500 dark:text-zink-200">
                                    <span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border border-transparent bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-200">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-slate-500 dark:text-zink-200">
                                    @if ($user->status === 'active')
                                        <span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border border-transparent bg-green-100 text-green-500 dark:bg-green-500/20 dark:text-green-500">Active</span>
                                    @elseif ($user->status === 'pending')
                                        <span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border border-transparent bg-yellow-100 text-yellow-500 dark:bg-yellow-500/20 dark:text-yellow-500">Pending</span>
                                    @elseif ($user->status === 'suspended')
                                        <span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border border-transparent bg-red-100 text-red-500 dark:bg-red-500/20 dark:text-red-500">Suspended</span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-block text-xs font-medium rounded border border-transparent bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-200">{{ $user->status }}</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-slate-500 dark:text-zink-200">
                                    @if ($user->status_reason)
                                        <span class="text-xs">{{ $user->status_reason }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-zink-300">-</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-2.5 border-b border-slate-200 dark:border-zink-500 text-right">
                                    @if ($user->status !== 'suspended')
                                        <form method="POST" action="{{ route('admin.users.suspend', ['user' => $user->id]) }}" class="flex items-center justify-end gap-2">
                                            @csrf
                                            <input
                                                type="text"
                                                name="reason"
                                                placeholder="Reason"
                                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 rounded-md py-1 px-2 text-xs w-32"
                                            >
                                            <button
                                                type="submit"
                                                class="px-2 py-1 text-xs text-white btn bg-red-500 border-red-500 hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-red-400/20"
                                            >
                                                Suspend
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-zink-300">Suspended</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
