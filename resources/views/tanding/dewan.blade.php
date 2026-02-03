@extends('main.main')

@section('head')
    {{-- Meta tag untuk pertandingan ID agar JavaScript tahu match mana yang sedang dimainkan --}}
    <meta name="pertandingan-id" content="{{ $id }}">
@endsection

@section('content')

<style>
    /* Layout Improvements - Clean & Organized */
    .dewan-container {
        background-color: rgb(216, 216, 216);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    /* Header Section */
    .header-section {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #dee2e6;
    }

    .header-section h5 {
        font-weight: 700;
        margin-bottom: 0;
    }

    .header-section p {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .match-info {
        background-color: white;
        border-radius: 10px;
        padding: 12px 20px;
        border: 2px solid #e9ecef;
    }

    .match-info p {
        margin-bottom: 3px;
        font-weight: 700;
        font-size: 15px;
    }

    /* Score Cards - Clean Design */
    .score-header {
        font-weight: 600;
        font-size: 14px;
        padding: 12px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .score-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .score-value {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
    }

    .score-value:hover {
        border-color: #adb5bd;
    }

    /* Action Buttons - Improved Layout */
    .action-btn {
        border-radius: 12px;
        height: 90px;
        font-weight: 700;
        font-size: 15px;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .action-btn:active {
        transform: translateY(-1px);
    }

    .delete-btn {
        border-radius: 12px;
        font-weight: 600;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        background-color: #dc3545;
        color: white;
    }

    .delete-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0,0,0,0.2);
        background-color: #c82333;
    }

    /* Center Panel - Clean Card Design */
    .center-panel {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }

    .score-label {
        font-weight: 700;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .round-indicator {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
    }

    .round-indicator:hover {
        border-color: #adb5bd;
        background: #e9ecef;
    }

    .control-btn {
        border-radius: 10px;
        padding: 12px;
        font-weight: 700;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    }

    .control-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0,0,0,0.2);
    }

    .validation-display {
        background: #343a40;
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 10px;
        font-weight: 600;
        text-align: center;
    }

    .validation-result {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 0 0 10px 10px;
        padding: 10px;
        font-weight: 600;
        text-align: center;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .modal-header {
        border-radius: 15px 15px 0 0;
        border-bottom: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }

    .modal-title {
        font-weight: 700;
    }

    .modal-btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.2s ease;
    }

    .juri-card {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 12px;
        transition: all 0.2s ease;
    }

    .juri-card:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dewan-container {
            padding: 15px;
        }

        .action-btn {
            height: 80px;
            font-size: 14px;
        }

        .header-section {
            padding: 15px;
        }
    }

    @media (max-width: 576px) {
        .score-header, .score-value {
            font-size: 12px;
            padding: 10px;
        }

        .action-btn {
            height: 70px;
            font-size: 13px;
        }
    }
</style>

@php
    // Konfigurasi Kolom Skor untuk Looping
    $scoreColumns = [
        ['label' => 'BINA', 'width' => '90px', 'col_class' => 'col-3 pe-0'],
        ['label' => 'TEGURAN', 'width' => '98px', 'col_class' => 'col-3 ps-0 pe-0'],
        ['label' => 'PERINGATAN', 'width' => '98px', 'col_class' => 'col-3 ps-1 p-0'],
        ['label' => 'JATUH', 'width' => '90px', 'col_class' => 'col-3 ps-2 p-0'],
    ];

    // Konfigurasi Tombol Aksi
    $actionButtons = ['JATUH', 'BINA', 'TEGURAN', 'PERINGATAN'];
@endphp

<div class="container mt-4 mb-4">
    <div class="dewan-container">
        {{-- Header Section --}}
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <p class="text-muted m-0">CONTINGENT</p>
                    <h5 class="text-primary">ATHLETE BLUE</h5>
                </div>
                <div class="col-md-4 text-center my-3 my-md-0">
                    <div class="match-info">
                        <p class="m-0 text-dark">PARTAI 2</p>
                        <p class="m-0 text-dark">ARENA 1</p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <p class="text-muted m-0">CONTINGENT</p>
                    <h5 class="text-danger">ATHLETE RED</h5>
                </div>
            </div>
        </div>

        <div class="row justify-content-between g-4">
            {{-- ==================== TEAM BLUE (KIRI) ==================== --}}
            <div class="col-lg-5">
                {{-- Header Skor --}}
                <div class="d-flex gap-2 justify-content-start">
                    @foreach($scoreColumns as $col)
                        <div class="flex-fill">
                            <div class="score-header bg-primary text-white">
                                {{ $col['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Baris Nilai (Loop 3 Ronde) --}}
                @for($i = 0; $i < 3; $i++)
                    <div class="d-flex gap-2 mt-2">
                        @foreach($scoreColumns as $col)
                            <div class="flex-fill">
                                <div class="score-value">-</div>
                            </div>
                        @endforeach
                    </div>
                @endfor

                {{-- Tombol Aksi Biru --}}
                <div class="row g-3 mt-3">
                    @foreach($actionButtons as $btn)
                        <div class="col-6">
                            <button class="action-btn btn btn-primary w-100" type="button" onclick="sendAction('{{ $btn }}', 'blue')">{{ $btn }}</button>
                        </div>
                    @endforeach
                    {{-- Tombol Hapus --}}
                    @foreach(['HAPUS JATUHAN', 'HAPUS PELANGGARAN'] as $delBtn)
                        <div class="col-6">
                            <button class="delete-btn w-100 py-3" type="button">{{ $delBtn }}</button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ==================== CENTER (SCORE & MODAL) ==================== --}}
            <div class="col-lg-2">
                <div class="center-panel">
                    <div class="score-label bg-warning text-white mb-3">SCORE</div>
                    
                    @foreach(['I', 'II', 'III'] as $round)
                        <div class="round-indicator mb-2">{{ $round }}</div>
                    @endforeach

                    <div class="mt-4 d-grid gap-2">
                        <button type="button" class="control-btn btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            REQUEST VALIDATION
                        </button>
                        <button class="control-btn btn btn-success text-white">TENTUKAN PEMENANG</button>
                        
                        <div class="mt-3">
                            <div class="validation-display">LAST VALIDATION</div>
                            <div class="validation-result" id="last-validation-result">
                                <p class="m-0 text-muted">No result yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TEAM RED (KANAN) ==================== --}}
            <div class="col-lg-5">
                {{-- Header Skor --}}
                <div class="d-flex gap-2 justify-content-end">
                    @foreach($scoreColumns as $col)
                        <div class="flex-fill">
                            <div class="score-header bg-danger text-white">
                                {{ $col['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Baris Nilai (Loop 3 Ronde) --}}
                @for($i = 0; $i < 3; $i++)
                    <div class="d-flex gap-2 justify-content-end mt-2">
                        @foreach($scoreColumns as $col)
                            <div class="flex-fill">
                                <div class="score-value">-</div>
                            </div>
                        @endforeach
                    </div>
                @endfor

                {{-- Tombol Aksi Merah --}}
                <div class="row g-3 mt-3">
                    @foreach($actionButtons as $btn)
                        <div class="col-6">
                            <button class="action-btn btn btn-danger w-100" type="button" onclick="sendAction('{{ $btn }}', 'red')">{{ $btn }}</button>
                        </div>
                    @endforeach
                    {{-- Tombol Hapus --}}
                    @foreach(['HAPUS JATUHAN', 'HAPUS PELANGGARAN'] as $delBtn)
                        <div class="col-6">
                            <button class="delete-btn w-100 py-3" type="button">{{ $delBtn }}</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL - Request Validation --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 text-center" id="exampleModalLabel">REQUEST VALIDATION</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-center mb-4">Pilih jenis validasi dan tim:</p>
                
                <!-- Blue Team -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">Team Blue</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="requestValidation('jatuhan', 'blue')">
                            🟦 Jatuhan - Blue
                        </button>
                        <button class="btn btn-outline-primary" onclick="requestValidation('pelanggaran', 'blue')">
                            🟦 Pelanggaran - Blue
                        </button>
                    </div>
                </div>
                
                <!-- Red Team -->
                <div>
                    <h6 class="text-danger mb-3">Team Red</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-danger" onclick="requestValidation('jatuhan', 'red')">
                            🟥 Jatuhan - Red
                        </button>
                        <button class="btn btn-outline-danger" onclick="requestValidation('pelanggaran', 'red')">
                            🟥 Pelanggaran - Red
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="/js/sendEventTanding.js"></script>
<script src="/js/validationDewan.js"></script>

@endsection