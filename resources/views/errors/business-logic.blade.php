@extends('layouts.master')

@section('title', 'Error - Business Logic')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi Kesalahan Bisnis
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Error Code:</strong> {{ $error_code ?? 'BUSINESS_ERROR' }}
                    </div>
                    
                    <p class="card-text">{{ $message ?? 'Terjadi kesalahan dalam proses bisnis.' }}</p>
                    
                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-primary me-2">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i>
                            Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
