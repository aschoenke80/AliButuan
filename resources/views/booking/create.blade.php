@extends('layouts.app')

@section('title', 'Book Advertisement')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header --}}
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-2xl mb-4">
            <span class="text-3xl">📣</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Book an Advertisement</h1>
        <p class="text-gray-500 mt-2">Promote your event on AliButuan. Fill in the details below and we'll get back to you.</p>
    </div>

    {{-- Rate Info Card --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8 flex items-center gap-4">
        <div class="text-3xl">💰</div>
        <div>
            <p class="font-semibold text-blue-800 text-sm">Advertisement Rate</p>
            <p class="text-blue-700 text-2xl font-bold">₱100 <span class="text-base font-normal">per hour</span></p>
            <p class="text-blue-600 text-xs mt-0.5">= ₱2,400 per day (24 hours)</p>
        </div>
    </div>

    <form method="POST" action="{{ route('booking.store') }}" class="space-y-6">
        @csrf

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-start gap-3">
            <span class="text-xl">✅</span>
            <div>
                <p class="font-semibold text-green-800 text-sm">Booking Submitted!</p>
                <p class="text-green-700 text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        {{-- Event Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">📋 Event Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Event Name <span class="text-red-500">*</span></label>
                <input type="text" name="event_name" value="{{ old('event_name') }}"
                       placeholder="e.g. Butuan City Summer Festival"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('event_name') border-red-400 @enderror" />
                @error('event_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Event Location / Venue <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Robinsons Place Butuan, Km. 4"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-400 @enderror" />
                @error('location')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Date Range + Duration & Cost --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">📅 Advertisement Period</h2>

            {{-- Date Range Picker --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Date Range <span class="text-red-500">*</span></label>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1.5">From</p>
                        <input type="date" name="advertise_start" id="input-start"
                               value="{{ old('advertise_start') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               onchange="onDatesChange()"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('advertise_start') border-red-400 @enderror" />
                        @error('advertise_start')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-2.5 text-gray-400 font-bold text-lg select-none">→</div>

                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1.5">To</p>
                        <input type="date" name="advertise_end" id="input-end"
                               value="{{ old('advertise_end') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               onchange="onDatesChange()"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('advertise_end') border-red-400 @enderror" />
                        @error('advertise_end')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Auto-computed days badge --}}
            <div id="days-display" class="hidden bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center gap-3">
                <span class="text-2xl">📆</span>
                <div>
                    <p id="days-text" class="font-bold text-blue-800"></p>
                    <p id="date-range-text" class="text-xs text-blue-600 mt-0.5"></p>
                </div>
            </div>

            {{-- Hidden days field (computed by JS, verified server-side) --}}
            <input type="hidden" name="days" id="input-days" value="{{ old('days', 0) }}" />

            {{-- Daily Hour Range --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Daily Hour Range <span class="text-red-500">*</span></label>
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1.5">From</p>
                        <input type="time" name="time_start" id="input-time-start"
                               value="{{ old('time_start', '08:00') }}"
                               onchange="onTimesChange()"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('time_start') border-red-400 @enderror" />
                        @error('time_start')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-2.5 text-gray-400 font-bold text-lg select-none">→</div>

                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 mb-1.5">To</p>
                        <input type="time" name="time_end" id="input-time-end"
                               value="{{ old('time_end', '17:00') }}"
                               onchange="onTimesChange()"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('time_end') border-red-400 @enderror" />
                        @error('time_end')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Hours-per-day badge --}}
                <div id="hours-per-day-display" class="mt-3 hidden bg-purple-50 border border-purple-200 rounded-xl px-4 py-2.5 flex items-center gap-2">
                    <span class="text-lg">🕐</span>
                    <p id="hours-per-day-text" class="text-sm font-semibold text-purple-800"></p>
                </div>
            </div>

            {{-- Hidden hours-per-day field (computed by JS) --}}
            <input type="hidden" name="hours" id="input-hours" value="{{ old('hours', 9) }}" />

            {{-- Cost Summary --}}
            <div id="cost-summary" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Total Duration:</span>
                    <span id="display-hours" class="text-sm font-semibold text-gray-800">0 hours</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Rate:</span>
                    <span class="text-sm text-gray-600">₱100 / hour</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Estimated Cost:</span>
                    <span id="display-cost" class="text-xl font-bold text-blue-600">₱0.00</span>
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">📞 Contact Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="contact_name"
                       value="{{ old('contact_name', auth()->user()->name ?? '') }}"
                       placeholder="Your full name"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_name') border-red-400 @enderror" />
                @error('contact_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="contact_email"
                       value="{{ old('contact_email', auth()->user()->email ?? '') }}"
                       placeholder="your@email.com"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_email') border-red-400 @enderror" />
                @error('contact_email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number <span class="text-gray-400 text-xs">(optional)</span></label>
                <input type="text" name="contact_phone"
                       value="{{ old('contact_phone', auth()->user()->phone_number ?? '') }}"
                       placeholder="e.g. 09XX XXX XXXX"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Additional Notes <span class="text-gray-400 text-xs">(optional)</span></label>
                <textarea name="notes" rows="3"
                          placeholder="Any special requests or details about your advertisement..."
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl text-sm transition-colors shadow-sm">
            📩 Submit Booking Request
        </button>
        <p class="text-center text-xs text-gray-400">We will review your request and contact you within 24 hours.</p>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const RATE = 100; // ₱ per hour

    // ── Date range changed ────────────────────────────────────────────────────
    function onDatesChange() {
        const startEl  = document.getElementById('input-start');
        const endEl    = document.getElementById('input-end');
        const startVal = startEl.value;
        const endVal   = endEl.value;

        // End date can't be before start date
        if (startVal) {
            endEl.min = startVal;
            if (endVal && endVal < startVal) endEl.value = startVal;
        }

        let days = 0;
        const daysDisplay = document.getElementById('days-display');

        if (startVal && endEl.value) {
            const start = new Date(startVal);
            const end   = new Date(endEl.value);
            // Inclusive: May1→May3 = 3 days
            days = Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1;

            daysDisplay.classList.remove('hidden');
            document.getElementById('days-text').textContent =
                `${days} day${days !== 1 ? 's' : ''}`;

            const opts = { month: 'long', day: 'numeric', year: 'numeric' };
            document.getElementById('date-range-text').textContent =
                `${start.toLocaleDateString('en-PH', opts)} → ${end.toLocaleDateString('en-PH', opts)}`;
        } else {
            daysDisplay.classList.add('hidden');
        }

        document.getElementById('input-days').value = days;
        recalculate();
    }

    // ── Time range changed ────────────────────────────────────────────────────
    function onTimesChange() {
        const tStartVal = document.getElementById('input-time-start').value;
        const tEndVal   = document.getElementById('input-time-end').value;
        const hpdDisplay = document.getElementById('hours-per-day-display');
        let hoursPerDay = 0;

        if (tStartVal && tEndVal) {
            const [sh, sm] = tStartVal.split(':').map(Number);
            const [eh, em] = tEndVal.split(':').map(Number);
            const diffMins = (eh * 60 + em) - (sh * 60 + sm);
            hoursPerDay = diffMins > 0 ? Math.round(diffMins / 60) : 0;

            if (hoursPerDay > 0) {
                hpdDisplay.classList.remove('hidden');
                const fmt = t => {
                    const [h, m] = t.split(':').map(Number);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    const h12  = h % 12 || 12;
                    return `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
                };
                document.getElementById('hours-per-day-text').textContent =
                    `${fmt(tStartVal)} – ${fmt(tEndVal)} = ${hoursPerDay} hr${hoursPerDay !== 1 ? 's' : ''}/day`;
            } else {
                hpdDisplay.classList.add('hidden');
            }
        } else {
            hpdDisplay.classList.add('hidden');
        }

        document.getElementById('input-hours').value = hoursPerDay;
        recalculate();
    }

    // ── Recalculate cost ──────────────────────────────────────────────────────
    function recalculate() {
        const days        = parseInt(document.getElementById('input-days').value)  || 0;
        const hoursPerDay = parseInt(document.getElementById('input-hours').value) || 0;
        const totalHours  = days * hoursPerDay;
        const totalCost   = totalHours * RATE;

        let durationText = '';
        if (days > 0 && hoursPerDay > 0) {
            durationText = `${days} day${days !== 1 ? 's' : ''} × ${hoursPerDay} hr${hoursPerDay !== 1 ? 's' : ''}/day = ${totalHours} hours`;
        } else if (days > 0) {
            durationText = `${days} day${days !== 1 ? 's' : ''} (set hour range)`;
        } else {
            durationText = 'Select dates and hour range above';
        }

        document.getElementById('display-hours').textContent = durationText;
        document.getElementById('display-cost').textContent  =
            '₱' + totalCost.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        const summary = document.getElementById('cost-summary');
        if (totalHours > 0) {
            summary.classList.add('border-blue-300', 'bg-blue-50');
            summary.classList.remove('border-gray-200', 'bg-gray-50');
        } else {
            summary.classList.remove('border-blue-300', 'bg-blue-50');
            summary.classList.add('border-gray-200', 'bg-gray-50');
        }
    }

    // Run on load to restore old() values after validation failure
    onDatesChange();
    onTimesChange();
</script>
@endpush
