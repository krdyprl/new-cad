@extends('admin.layout')

@section('title', 'Kelola Informasi')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><i class="fas fa-info-circle"></i> Daftar Informasi</h5>
        <a href="{{ route('admin.information.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Informasi
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($information as $info)
                <tr>
                    <td>{{ $info->id }}</td>
                    <td>
                        @if($info->image)
                            <img src="{{ asset($info->image) }}" alt="Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                    </td>                    <td>
                        <strong>{{ $info->title }}</strong>
                        @if($info->description)
                            <br><small class="text-muted">{{ Str::limit(strip_tags($info->description), 50) }}</small>
                        @else
                            <br><small class="text-muted">{{ Str::limit(strip_tags($info->content), 50) }}</small>
                        @endif
                    </td><td>
                        @if($info->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning">Draft</span>
                        @endif
                    </td>
                    <td>{{ $info->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.information.edit', $info->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $info->id }}, '{{ $info->title }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada informasi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $information->links() }}
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Informasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus informasi <strong id="infoTitle"></strong>?</p>
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
function confirmDelete(infoId, infoTitle) {
    document.getElementById('infoTitle').textContent = infoTitle;
    document.getElementById('deleteForm').action = `/admin/information/${infoId}`;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
