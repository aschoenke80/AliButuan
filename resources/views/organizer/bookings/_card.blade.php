{{-- Reusable booking card for the organizer bookings list --}}
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap mb-1">
            <h3 class="text-base font-semibold text-gray-900 truncate">{{ $booking->event_name }}</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                {{ $booking->status === 'pending'  ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800'  : '' }}
                {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800'      : '' }}">
                {{ ucfirst($booking->status) }}
            </span>
        </div>
        <p class="text-sm text-gray-500">📍 {{ $booking->location }}</p>
        @if($booking->advertise_start && $booking->advertise_end)
        <p class="text-sm text-gray-500 mt-0.5">
            📅 <strong>{{ $booking->advertise_start->format('M j, Y') }}</strong>
            → <strong>{{ $booking->advertise_end->format('M j, Y') }}</strong>
            ({{ $booking->days }} day{{ $booking->days !== 1 ? 's' : '' }})
        </p>
        @endif
        @if($booking->time_start && $booking->time_end)
        <p class="text-sm text-gray-500 mt-0.5">
            🕐 {{ \Carbon\Carbon::createFromFormat('H:i', $booking->time_start)->format('g:i A') }}
            – {{ \Carbon\Carbon::createFromFormat('H:i', $booking->time_end)->format('g:i A') }}
            ({{ $booking->hours }} hr{{ $booking->hours !== 1 ? 's' : '' }}/day)
        </p>
        @endif
        <p class="text-sm text-gray-500 mt-0.5">
            👤 <strong>{{ $booking->contact_name }}</strong>
            — <a href="mailto:{{ $booking->contact_email }}" class="text-blue-600 hover:underline">{{ $booking->contact_email }}</a>
            @if($booking->contact_phone) · {{ $booking->contact_phone }} @endif
        </p>
        @if($booking->notes)
        <p class="text-sm text-gray-400 italic mt-1">"{{ $booking->notes }}"</p>
        @endif
    </div>

    <div class="text-right shrink-0">
        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($booking->total_cost, 2) }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $booking->total_hours }} total hours</p>
        <p class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</p>
    </div>
</div>

@if($showActions)
<div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row gap-3">
    {{-- Approve --}}
    <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}" class="flex-1">
        @csrf
        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors"
                onclick="return confirm('Approve this booking for \'{{ addslashes($booking->event_name) }}\'?')">
            ✅ Approve
        </button>
    </form>

    {{-- Reject --}}
    <div class="flex-1">
        <button type="button"
                onclick="toggleReject({{ $booking->id }})"
                class="w-full bg-red-50 hover:bg-red-100 text-red-700 font-semibold py-2.5 rounded-xl text-sm transition-colors border border-red-200">
            ❌ Reject
        </button>
        <div id="reject-form-{{ $booking->id }}" class="hidden mt-2">
            <form method="POST" action="{{ route('organizer.bookings.reject', $booking) }}">
                @csrf
                <textarea name="reject_reason" rows="2" placeholder="Reason for rejection (optional)..."
                          class="w-full px-3 py-2 border border-red-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none mb-2"></textarea>
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-xl text-sm transition-colors">
                    Confirm Rejection
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function toggleReject(id) {
        const el = document.getElementById('reject-form-' + id);
        el.classList.toggle('hidden');
    }
</script>
@endpush
