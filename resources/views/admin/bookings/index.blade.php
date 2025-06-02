@extends('admin.layout')

@section('title', 'Kelola Booking')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><i class="fas fa-calendar-check"></i> Daftar Booking</h5>
        <div>
            <span class="badge bg-primary">Total: {{ $bookings->total() }} booking</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>User</th>
                    <th>Paket</th>
                    <th>Tanggal Kunjungan</th>
                    <th>Jumlah Orang</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>                    <td>
                        <strong>{{ $booking->full_name }}</strong>
                        <br><small class="text-muted">{{ $booking->email }}</small>
                        @if($booking->phone)
                            <br><small class="text-muted">{{ $booking->phone }}</small>
                        @endif
                    </td>
                    <td>
                        @if($booking->user)
                            {{ $booking->user->name }}
                        @else
                            <span class="text-muted">Guest</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-info">{{ ucfirst($booking->package) }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}</td>
                    <td>{{ $booking->participants }} orang</td>
                    <td><strong>Rp {{ number_format($booking->total, 0, ',', '.') }}</strong></td>
                    <td>
                        <select class="form-select form-select-sm" onchange="updateStatus({{ $booking->id }}, this.value)">
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </td>
                    <td>{{ $booking->created_at->format('d M Y') }}</td>
                    <td>
                        <button class="btn btn-info btn-sm mb-1" onclick="viewBooking({{ $booking->id }})">
                            <i class="fas fa-eye"></i>
                        </button>                        <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $booking->id }}, '{{ $booking->full_name }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Belum ada booking</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetails">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus booking dari <strong id="customerName"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateStatus(bookingId, status) {
    fetch(`/admin/bookings/${bookingId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan saat mengupdate status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate status');
    });
}

function viewBooking(bookingId) {
    // You can implement this to show booking details
    // For now, let's just show a simple message
    document.getElementById('bookingDetails').innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading booking details...</p>
        </div>
    `;
    
    var viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    viewModal.show();
    
    // Here you would normally fetch booking details via AJAX
    setTimeout(() => {
        document.getElementById('bookingDetails').innerHTML = `
            <p><strong>Booking ID:</strong> ${bookingId}</p>
            <p>Detail booking akan ditampilkan di sini...</p>
        `;
    }, 1000);
}

function confirmDelete(bookingId, customerName) {
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('deleteForm').action = `/admin/bookings/${bookingId}`;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
