@extends('admin.layout')

@section('title', 'Pengaturan Website')

@section('content')
<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="form-container">
            <h5 class="mb-4"><i class="fas fa-cog"></i> Pengaturan Website</h5>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Website Information -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fas fa-globe"></i> Informasi Website</h6>
                    
                    <div class="form-group mb-3">
                        <label for="site_name" class="form-label">Nama Website</label>
                        <input type="text" 
                               class="form-control" 
                               id="site_name" 
                               name="site_name" 
                               value="{{ old('site_name', 'Ceramic Art Dinoyo') }}" 
                               required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="site_description" class="form-label">Deskripsi Website</label>
                        <textarea class="form-control" 
                                  id="site_description" 
                                  name="site_description" 
                                  rows="3" 
                                  required>{{ old('site_description', 'Kampung Keramik Dinoyo - Wisata Kerajinan Keramik Tradisional di Malang') }}</textarea>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fas fa-address-book"></i> Informasi Kontak</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="contact_email" class="form-label">Email Kontak</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="contact_email" 
                                       name="contact_email" 
                                       value="{{ old('contact_email', 'info@ceramicartdinoyo.com') }}" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="contact_phone" class="form-label">Nomor Telepon</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_phone" 
                                       name="contact_phone" 
                                       value="{{ old('contact_phone', '+62 341 123456') }}" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" 
                                  id="address" 
                                  name="address" 
                                  rows="2" 
                                  required>{{ old('address', 'Kampung Keramik Dinoyo, Kota Malang, Jawa Timur') }}</textarea>
                    </div>
                </div>

                <!-- Booking Settings -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fas fa-calendar-alt"></i> Pengaturan Booking</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="max_guests" class="form-label">Maksimal Tamu per Booking</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="max_guests" 
                                       name="max_guests" 
                                       value="{{ old('max_guests', 50) }}" 
                                       min="1" 
                                       max="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="booking_advance_days" class="form-label">Minimal Hari Sebelum Booking</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="booking_advance_days" 
                                       name="booking_advance_days" 
                                       value="{{ old('booking_advance_days', 3) }}" 
                                       min="1" 
                                       max="30">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="auto_confirm_booking" 
                                   name="auto_confirm_booking" 
                                   value="1" 
                                   {{ old('auto_confirm_booking') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_confirm_booking">
                                Konfirmasi booking otomatis
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Package Prices -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fas fa-tag"></i> Harga Paket</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="basic_price" class="form-label">Harga Paket Basic (per orang)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="basic_price" 
                                           name="basic_price" 
                                           value="{{ old('basic_price', 25000) }}" 
                                           min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="premium_price" class="form-label">Harga Paket Premium (per orang)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="premium_price" 
                                           name="premium_price" 
                                           value="{{ old('premium_price', 50000) }}" 
                                           min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="vip_price" class="form-label">Harga Paket VIP (per orang)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="vip_price" 
                                           name="vip_price" 
                                           value="{{ old('vip_price', 100000) }}" 
                                           min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fab fa-instagram"></i> Media Sosial</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="instagram_url" class="form-label">Instagram URL</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="instagram_url" 
                                       name="instagram_url" 
                                       value="{{ old('instagram_url', 'https://www.instagram.com/dinoyoceramic') }}"
                                       placeholder="https://www.instagram.com/username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="facebook_url" class="form-label">Facebook URL</label>
                                <input type="url" 
                                       class="form-control" 
                                       id="facebook_url" 
                                       name="facebook_url" 
                                       value="{{ old('facebook_url', '') }}"
                                       placeholder="https://www.facebook.com/pagename">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="settings-section">
                    <h6 class="section-title"><i class="fas fa-cogs"></i> Pengaturan Sistem</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="timezone" class="form-label">Zona Waktu</label>
                                <select class="form-control" id="timezone" name="timezone">
                                    <option value="Asia/Jakarta" {{ old('timezone', 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ old('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ old('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="maintenance_mode" class="form-label">Mode Maintenance</label>
                                <select class="form-control" id="maintenance_mode" name="maintenance_mode">
                                    <option value="0" {{ old('maintenance_mode', '0') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="1" {{ old('maintenance_mode') == '1' ? 'selected' : '' }}>Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.settings-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-title {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: 20px;
}
</style>
@endsection
