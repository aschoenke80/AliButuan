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

        {{-- Duration & Cost --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">⏱️ Advertisement Duration</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Days</label>
                    <div class="relative">
                        <input type="number" name="days" id="input-days" value="{{ old('days', 0) }}"
                               min="0" max="365" placeholder="0"
                               oninput="recalculate()"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('days') border-red-400 @enderror" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">day(s)</span>
                    </div>
                    @error('days')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Additional Hours</label>
                    <div class="relative">
                        <input type="number" name="hours" id="input-hours" value="{{ old('hours', 0) }}"
                               min="0" max="23" placeholder="0"
                               oninput="recalculate()"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hours') border-red-400 @enderror" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">hr(s)</span>
                    </div>
                    @error('hours')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
    const RATE = 100; // pesos per hour

    function recalculate() {
        const days  = parseInt(document.getElementById('input-days').value)  || 0;
        const hours = parseInt(document.getElementById('input-hours').value) || 0;
        const totalHours = (days * 24) + hours;
        const totalCost  = totalHours * RATE;

        let durationText = '';
        if (days > 0 && hours > 0) {
            durationText = `${days} day${days !== 1 ? 's' : ''} + ${hours} hr${hours !== 1 ? 's' : ''} = ${totalHours} hours`;
        } else if (days > 0) {
            durationText = `${days} day${days !== 1 ? 's' : ''} = ${totalHours} hours`;
        } else {
            durationText = `${totalHours} hour${totalHours !== 1 ? 's' : ''}`;
        }

        document.getElementById('display-hours').textContent = durationText;
        document.getElementById('display-cost').textContent  = '₱' + totalCost.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        // Highlight when cost is calculated
        const summary = document.getElementById('cost-summary');
        if (totalHours > 0) {
            summary.classList.add('border-blue-300', 'bg-blue-50');
            summary.classList.remove('border-gray-200', 'bg-gray-50');
        } else {
            summary.classList.remove('border-blue-300', 'bg-blue-50');
            summary.classList.add('border-gray-200', 'bg-gray-50');
        }
    }

    // Run once on load in case of old() values
    recalculate();
</script>
@endpush
