@extends('admin.layout')

@section('title', 'Edit Informasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="form-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5><i class="fas fa-edit"></i> Edit Informasi</h5>
                <a href="{{ route('admin.information') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.information.update', $information) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                  <div class="form-group mb-3">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading"></i> Judul Informasi *
                    </label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $information->title) }}" 
                           required
                           placeholder="Masukkan judul informasi">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">
                        <i class="fas fa-quote-left"></i> Deskripsi Singkat
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              placeholder="Masukkan deskripsi singkat...">{{ old('description', $information->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="content" class="form-label">
                        <i class="fas fa-align-left"></i> Konten Informasi *
                    </label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" 
                              name="content" 
                              rows="10" 
                              required
                              placeholder="Masukkan konten informasi...">{{ old('content', $information->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="type" class="form-label">
                        <i class="fas fa-tag"></i> Tipe *
                    </label>
                    <select class="form-control @error('type') is-invalid @enderror" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">Pilih Tipe</option>
                        <option value="information" {{ old('type', $information->type) == 'information' ? 'selected' : '' }}>Informasi</option>
                        <option value="news" {{ old('type', $information->type) == 'news' ? 'selected' : '' }}>Berita</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="image" class="form-label">
                        <i class="fas fa-image"></i> Gambar
                    </label>
                    
                    @if($information->image)
                        <div class="current-image mb-2">
                            <p class="mb-1"><strong>Gambar saat ini:</strong></p>
                            <img src="{{ asset($information->image) }}" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;" 
                                 alt="Current image">
                        </div>
                    @endif
                    
                    <input type="file" 
                           class="form-control @error('image') is-invalid @enderror" 
                           id="image" 
                           name="image" 
                           accept="image/*">
                    <small class="form-text text-muted">
                        Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB. 
                        {{ $information->image ? 'Kosongkan jika tidak ingin mengubah gambar.' : '' }}
                    </small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>                <div class="form-group mb-3">
                    <label for="status" class="form-label">
                        <i class="fas fa-eye"></i> Status *
                    </label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="draft" {{ old('status', $information->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $information->status) == 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Informasi
                    </button>
                    <a href="{{ route('admin.information') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="button" class="btn btn-danger float-end" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Rich text editor for content
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const contentTextarea = document.getElementById('content');
    contentTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
    
    // Set initial height
    contentTextarea.style.height = contentTextarea.scrollHeight + 'px';
    
    // Image preview
    const imageInput = document.getElementById('image');
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                const existingPreview = document.querySelector('.image-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Create new preview
                const preview = document.createElement('div');
                preview.className = 'image-preview mt-2';
                preview.innerHTML = `
                    <p class="mb-1"><strong>Preview gambar baru:</strong></p>
                    <img src="${e.target.result}" 
                         style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;" 
                         alt="Preview">
                    <br><small class="text-muted">Gambar yang akan diupload</small>
                `;
                imageInput.parentNode.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
});

function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus informasi ini? Tindakan ini tidak dapat dibatalkan!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.information.delete", $information) }}';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
