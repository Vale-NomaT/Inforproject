@extends('layouts.master')

@section('title', 'Absences – ' . $child->first_name)

@section('content')

<div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
    <div class="grow">
        <h5 class="text-16 font-semibold text-slate-800 dark:text-zink-50">Absence Management</h5>
        <p class="text-slate-500 dark:text-zink-300 text-sm mt-0.5">
            <a href="{{ route('parent.dashboard') }}" class="text-blue-600 hover:underline dark:text-blue-400">Dashboard</a>
            <span class="mx-1.5 text-slate-300">/</span>
            {{ $child->first_name }} {{ $child->last_name }}
        </p>
    </div>
    <a href="{{ route('parent.dashboard') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 dark:bg-zink-700 dark:border-zink-600 dark:text-zink-200 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </a>
</div>

<div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

    {{-- ── Report Absence Form ── --}}
    <div class="xl:col-span-1">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20">
                        <i data-lucide="calendar-x" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Report Absence</h6>
                        <p class="text-xs text-slate-400 dark:text-zink-400">for {{ $child->first_name }} {{ $child->last_name }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('parent.absences.store', $child) }}" class="p-5 space-y-4">
                    @csrf

                    {{-- Start date --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1.5">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date"
                               min="{{ now()->toDateString() }}"
                               value="{{ old('start_date', now()->toDateString()) }}"
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg dark:bg-zink-700 dark:border-zink-600 dark:text-zink-100 focus:outline-none focus:border-blue-400">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End date --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1.5">
                            End Date <span class="text-red-500">*</span>
                            <span class="text-xs font-normal text-slate-400 ml-1">(same as start for single day)</span>
                        </label>
                        <input type="date" name="end_date" id="end_date"
                               min="{{ now()->toDateString() }}"
                               value="{{ old('end_date', now()->toDateString()) }}"
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg dark:bg-zink-700 dark:border-zink-600 dark:text-zink-100 focus:outline-none focus:border-blue-400">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Run type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1.5">
                            Which run(s)? <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['both' => 'Both', 'morning' => 'Morning', 'afternoon' => 'Afternoon'] as $val => $label)
                            <label class="flex flex-col items-center gap-1.5 p-3 border rounded-lg cursor-pointer transition-colors
                                {{ old('run_type', 'both') === $val ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/10' : 'border-slate-200 dark:border-zink-600 hover:border-blue-300' }}">
                                <input type="radio" name="run_type" value="{{ $val }}"
                                       {{ old('run_type', 'both') === $val ? 'checked' : '' }}
                                       class="sr-only" onchange="this.closest('.grid').querySelectorAll('label').forEach(l=>l.classList.remove('border-blue-500','bg-blue-50','dark:bg-blue-500/10')); this.closest('label').classList.add('border-blue-500','bg-blue-50','dark:bg-blue-500/10')">
                                <i data-lucide="{{ $val === 'morning' ? 'sunrise' : ($val === 'afternoon' ? 'sunset' : 'sun') }}" class="w-4 h-4 text-slate-500 dark:text-zink-300"></i>
                                <span class="text-xs font-medium text-slate-600 dark:text-zink-200">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('run_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zink-100 mb-1.5">
                            Reason <span class="text-xs font-normal text-slate-400">(optional)</span>
                        </label>
                        <textarea name="reason" rows="3" placeholder="e.g. Sick, family event, public holiday…"
                                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg dark:bg-zink-700 dark:border-zink-600 dark:text-zink-100 focus:outline-none focus:border-blue-400 resize-none">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-lg transition-colors"
                                style="background:#d97706;">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Report Absence &amp; Notify Driver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Absence History ── --}}
    <div class="xl:col-span-2">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-zink-600">
                    <div>
                        <h6 class="text-15 font-semibold text-slate-800 dark:text-zink-50">Reported Absences</h6>
                        <p class="text-xs text-slate-400 dark:text-zink-400 mt-0.5">All absences reported for {{ $child->first_name }}</p>
                    </div>
                    <span class="text-sm font-bold text-slate-500 dark:text-zink-300">{{ $absences->count() }}</span>
                </div>

                @if($absences->isEmpty())
                    <div class="flex flex-col items-center justify-center py-14 text-slate-400 dark:text-zink-400">
                        <i data-lucide="calendar-check" class="w-10 h-10 mb-3 opacity-30"></i>
                        <p class="text-sm font-medium text-slate-500 dark:text-zink-300">No absences reported yet</p>
                        <p class="text-xs mt-1">Use the form to notify your driver when {{ $child->first_name }} won't be available.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-zink-600">
                        @foreach($absences as $absence)
                        @php
                            $isPast = $absence->end_date->isPast();
                            $isSingleDay = $absence->start_date->isSameDay($absence->end_date);
                            $dateLabel = $isSingleDay
                                ? $absence->start_date->format('D, d M Y')
                                : $absence->start_date->format('d M') . ' – ' . $absence->end_date->format('d M Y');
                        @endphp
                        <div class="flex items-start justify-between gap-4 px-5 py-4 {{ $isPast ? 'opacity-60' : '' }}">
                            <div class="flex items-start gap-3">
                                {{-- Run type icon --}}
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0 mt-0.5
                                    {{ $absence->run_type === 'morning' ? 'bg-orange-100 dark:bg-orange-500/20' : ($absence->run_type === 'afternoon' ? 'bg-purple-100 dark:bg-purple-500/20' : 'bg-amber-100 dark:bg-amber-500/20') }}">
                                    <i data-lucide="{{ $absence->run_type === 'morning' ? 'sunrise' : ($absence->run_type === 'afternoon' ? 'sunset' : 'sun') }}"
                                       class="w-4 h-4 {{ $absence->run_type === 'morning' ? 'text-orange-500' : ($absence->run_type === 'afternoon' ? 'text-purple-500' : 'text-amber-500') }}"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-slate-700 dark:text-zink-100">{{ $dateLabel }}</p>
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                            {{ $absence->run_type === 'morning' ? 'bg-orange-100 text-orange-600' : ($absence->run_type === 'afternoon' ? 'bg-purple-100 text-purple-600' : 'bg-amber-100 text-amber-600') }}">
                                            {{ $absence->runLabel() }}
                                        </span>
                                        @if($isPast)
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-500 dark:bg-zink-600 dark:text-zink-300">Past</span>
                                        @endif
                                    </div>
                                    @if($absence->reason)
                                        <p class="text-xs text-slate-500 dark:text-zink-300 mt-0.5">{{ $absence->reason }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 dark:text-zink-400 mt-1">
                                        Reported {{ $absence->created_at->diffForHumans() }}
                                        @if($absence->driver_notified_at)
                                            · <span class="text-green-600 dark:text-green-400">Driver notified ✓</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Reinstate button (only for future/current absences) --}}
                            @if(!$isPast)
                            <form method="POST"
                                  action="{{ route('parent.absences.destroy', [$child, $absence]) }}"
                                  onsubmit="return confirm('Cancel this absence? Your driver will be notified that {{ $child->first_name }} will be available again.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 dark:bg-green-500/10 dark:border-green-500/30 dark:text-green-400 transition-colors whitespace-nowrap">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reinstate
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
