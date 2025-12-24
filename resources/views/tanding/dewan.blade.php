@extends('main.main')
@section('content')

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

<div class="container">
    <div class="container mt-2 mb-2 rounded" style="background-color: rgb(216, 216, 216)">
        <div class="container pb-3">
            {{-- Title --}}
            <div class="d-flex justify-content-between pt-2">
                <div class="m-2">
                    <p class="text-start m-0">CONTINGENT</p>
                    <h5 class="text-primary">ATHLETE</h5>
                </div>
                <div class="m-2 text-center">
                    <p class="m-0 fw-bold">PARTAI 2</p>
                    <p class="m-0 fw-bold">ARENA 1</p>
                </div>
                <div class="mt-2 me-2 text-end">
                    <p class="m-0">CONTINGENT</p>
                    <h5 class="text-danger">ATHLETE</h5>
                </div>
            </div>
            <hr>

            <div class="row justify-content-between">
                {{-- ==================== TEAM BLUE (KIRI) ==================== --}}
                <div class="col-5">
                    {{-- Header Skor --}}
                    <div class="row justify-content-start">
                        @foreach($scoreColumns as $col)
                            <div class="{{ $col['col_class'] }}" style="width: {{ $col['width'] }}">
                                <div class="py-3 border bg-primary text-light text-center" 
                                     style="font-size:14px; border-radius: 10px; width: 90px">
                                    {{ $col['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Baris Nilai (Loop 3 Ronde) --}}
                    @for($i = 0; $i < 3; $i++)
                        <div class="row mt-2">
                            @foreach($scoreColumns as $col)
                                <div class="{{ $col['col_class'] }}" style="width: {{ $col['width'] }}">
                                    <div class="py-3 border bg-light text-dark text-center" 
                                         style="font-size:14px; border-radius: 10px; width: 90px">-</div>
                                </div>
                            @endforeach
                        </div>
                    @endfor

                    {{-- Tombol Aksi Biru --}}
                    <div class="row justify-content-between me-4">
                        @foreach($actionButtons as $btn)
                            <div class="col-6">
                                <button class="mt-3 btn btn-primary w-100" type="button" style="border-radius: 10px; height:100px" onclick="sendAction('{{ $btn }}')">{{ $btn }}</button>
                            </div>
                        @endforeach
                        {{-- Tombol Hapus --}}
                        @foreach(['HAPUS JATUHAN', 'HAPUS PELANGGARAN'] as $delBtn)
                            <div class="col-6">
                                <button class="mt-3 btn btn-primary w-100 h-75" type="button" 
                                        style="border-radius: 10px; background-color:rgb(190, 0, 0)">{{ $delBtn }}</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ==================== CENTER (SCORE & MODAL) ==================== --}}
                <div class="col-2">
                    <div class="p-3 border bg-warning text-light text-center" style="border-radius: 10px; height:55px">SCORE</div>
                    
                    @foreach(['I', 'II', 'III'] as $round)
                        <div class="p-3 mt-2 border bg-light text-center" style="border-radius: 10px; height:55px">{{ $round }}</div>
                    @endforeach

                    <div class="mt-5 d-grid gap-2">
                        <button type="button" class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            REQUEST VALIDATION
                        </button>
                        <div class="btn btn-success">TENTUKAN PEMENANG</div>
                        
                        <div class="border bg-dark p-2 mt-2 rounded-top"><p class="m-0 text-center text-light">LAST VALIDATION</p></div>
                        <div class="border bg-light p-2 mb-2 rounded-bottom"><p class="m-0 text-center">NO RESULT</p></div>
                    </div>
                </div>

                {{-- ==================== TEAM RED (KANAN) ==================== --}}
                <div class="col-5">
                    {{-- Header Skor --}}
                    <div class="row justify-content-end">
                        @foreach($scoreColumns as $col)
                            <div class="{{ $col['col_class'] }}" style="width: {{ $col['width'] }}">
                                <div class="py-3 border bg-danger text-light text-center" 
                                     style="font-size:14px; border-radius: 10px; width: 90px">
                                    {{ $col['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Baris Nilai (Loop 3 Ronde) --}}
                    @for($i = 0; $i < 3; $i++)
                        <div class="row justify-content-end mt-2">
                            @foreach($scoreColumns as $col)
                                <div class="{{ $col['col_class'] }}" style="width: {{ $col['width'] }}">
                                    <div class="py-3 border bg-light text-dark text-center" 
                                         style="font-size:14px; border-radius: 10px; width: 90px">-</div>
                                </div>
                            @endforeach
                        </div>
                    @endfor

                    {{-- Tombol Aksi Merah --}}
                    <div class="row justify-content-between">
                        @foreach($actionButtons as $index => $btn)
                            <div class="col-6 {{ $index % 2 == 0 ? 'ps-3' : 'pe-3' }}">
                                <button class="mt-3 btn btn-danger w-100" type="button" style="border-radius: 10px; height:100px">{{ $btn }}</button>
                            </div>
                        @endforeach
                        {{-- Tombol Hapus --}}
                        @foreach(['HAPUS JATUHAN', 'HAPUS PELANGGARAN'] as $delBtn)
                            <div class="col-6">
                                <button class="mt-3 btn btn-primary w-100 h-75" type="button" 
                                        style="border-radius: 10px; background-color:rgb(190, 0, 0)">{{ $delBtn }}</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL (Dipisah di bawah agar rapi) --}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5 class="modal-title text-center" id="exampleModalLabel">PILIH VALIDASI</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6"><div class="btn btn-light border w-100">VALIDASI JATUHAN</div></div>
                    <div class="col-6"><div class="btn btn-light border w-100">VALIDASI PELANGGARAN</div></div>
                </div>
                <div class="d-flex justify-content-around mt-5">
                    @for($j=1; $j<=3; $j++)
                        <div class="d-flex flex-column">
                            <p class="text-center mb-2">Juri {{ $j }}</p>
                            <div class="border bg-light p-2 rounded" style="width: 100px">
                                <p class="text-center m-0">Juri {{ $j }}</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script src="/js/sendEventTanding.js"></script>

@endsection