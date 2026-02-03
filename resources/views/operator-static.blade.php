@extends('main.main')

@section('styles')
<style>
    /* CSS untuk status (statis - hanya untuk tampilan) */
    .status-badge {
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-menunggu_peserta {
        color: #6c757d;
        background-color: #f8f9fa;
        border: 2px solid #6c757d;
    }
    .status-siap_dimulai {
        color: #0d6efd;
        background-color: #cfe2ff;
        border: 2px solid #0d6efd;
    }
    .status-berlangsung {
        color: #664d03;
        background-color: #fff3cd;
        border: 2px solid #ffc107;
    }
    .status-selesai {
        color: #198754;
        background-color: #d1e7dd;
        border: 2px solid #198754;
    }
    .status-ditunda {
        color: #dc3545;
        background-color: #f8d7da;
        border: 2px solid #dc3545;
    }
</style>
@endsection

@section('content')
    <div class="container mt-2 mb-0 rounded-top pb-4 pt-3"
        style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #c0c0c0">
        <div class="d-flex justify-content-center">
            <button id="resetFilter" class="btn btn-secondary mx-3 text-light fs-5" type="button" style="width: 150px; height:50px;">Semua</button>
            <button class="btn btn-secondary mx-3 text-light fs-5 filter-btn" data-jenis="Tanding" type="button" style="width: 150px; height:50px;">Tanding</button>
            <button class="btn mx-3 text-light fs-5 filter-btn" data-jenis="Seni" type="button" style="width: 150px; height:50px; background-color:rgb(100, 100, 100)">Artistics</button>
            <button class="btn mx-3 text-light fs-5 filter-btn" data-jenis="Jurus Baku" type="button" style="width: 150px; height:50px; background-color:rgb(100, 100, 100)">Jurus Baku</button>
        </div>
    </div>
    
    <div class="container rounded-0 pb-2 pt-3" style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #c0c0c0">
        {{-- Area kosong sesuai desain asli --}}
    </div>

    <div class="container rounded-bottom pb-4 pt-3" style="background-color: rgb(216, 216, 216);">
        
        <div class="table-responsive bg-white p-3 rounded">
            <h2 class="text-center mb-4">
                Jadwal Pertandingan (Statis)
                @if(isset($arena))
                    <span class="fw-normal" style="font-size:1.1em">- {{ $arena->arena_name ?? '-' }}</span>
                @endif
            </h2>
            
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari pertandingan (kelas, nama, kontingen, dll)...">
            </div>

            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Kelas & Kategori</th>
                        <th scope="col">Babak</th>
                        <th scope="col" class="table-primary">Sudut Biru</th>
                        <th scope="col" class="table-danger">Sudut Merah</th>
                        <th scope="col" style="width: 180px;">Status</th>
                        <th scope="col" style="width: 180px;">Penilaian</th>
                    </tr>
                </thead>
                <tbody id="pertandinganTableBody">
                    @forelse ($daftar_pertandingan as $pertandingan)
                        <tr data-jenis="{{ $pertandingan->kelasPertandingan?->jenisPertandingan?->nama_jenis ?? '' }}">
                            <th scope="row">{{ $pertandingan->id }}</th>
                            <td>
                                <strong>{{ $pertandingan->kelasPertandingan?->kelas?->nama_kelas ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $pertandingan->kelasPertandingan?->kategoriPertandingan?->nama_kategori ?? 'N/A' }}</small>
                            </td>
                            <td>
                                Babak {{ $pertandingan->round_number }} / Match {{ $pertandingan->match_number }}
                            </td>
                            <td>
                                @forelse ($pertandingan->pemain_unit_1 as $peserta)
                                    <div><strong>{{ $peserta->player?->name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $peserta->player?->contingent?->name }}</small></div>
                                    @if(!$loop->last)<hr class="my-1">@endif
                                @empty
                                    <span class="text-muted">-- Belum Ada --</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse ($pertandingan->pemain_unit_2 as $peserta)
                                    <div><strong>{{ $peserta->player?->name ?? 'N/A' }}</strong><br><small class="text-muted">{{ $peserta->player?->contingent?->name }}</small></div>
                                    @if(!$loop->last)<hr class="my-1">@endif
                                @empty
                                    <span class="text-muted">-- Belum Ada --</span>
                                @endforelse
                            </td>
                            <td>
                                @php
                                    $statusText = [
                                        'menunggu_peserta' => 'Menunggu Peserta',
                                        'siap_dimulai' => 'Siap Dimulai',
                                        'berlangsung' => 'Berlangsung',
                                        'selesai' => 'Selesai',
                                        'ditunda' => 'Ditunda'
                                    ][$pertandingan->status] ?? $pertandingan->status;
                                @endphp
                                <span class="status-badge status-{{ $pertandingan->status }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td style="min-width: 220px;">
                                @if ($pertandingan->status == 'berlangsung')
                                    <a href="{{ url('scoring/penilaian/' . ($user_id ?? '')) }}" class="btn btn-sm btn-info">Lihat Match</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <h4>Tidak ada data pertandingan yang ditemukan.</h4>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')

{{-- Script untuk filter dan pencarian (statis - client side only) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const resetButton = document.getElementById('resetFilter');
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#pertandinganTableBody tr');
    
    let activeFilter = ''; // Filter jenis yang sedang aktif

    function runFilterAndSearch() {
        const searchTerm = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const rowJenis = row.dataset.jenis;
            const rowText = row.textContent.toLowerCase();

            // Cek kondisi filter
            const filterMatch = activeFilter === '' || rowJenis === activeFilter;

            // Cek kondisi pencarian
            const searchMatch = rowText.includes(searchTerm);
            
            // Tampilkan baris jika cocok dengan KEDUA kondisi
            if (filterMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Event listener untuk tombol filter jenis
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            activeFilter = this.dataset.jenis;
            
            // Atur style tombol
            filterButtons.forEach(btn => {
                btn.style.backgroundColor = 'rgb(100, 100, 100)';
                btn.classList.remove('btn-danger');
            });
            this.style.backgroundColor = '';
            this.classList.add('btn-danger');

            runFilterAndSearch();
        });
    });

    // Event listener untuk tombol reset
    resetButton.addEventListener('click', function() {
        activeFilter = '';
        searchInput.value = '';
        
        // Reset style tombol
        filterButtons.forEach(btn => {
            btn.style.backgroundColor = 'rgb(100, 100, 100)';
            btn.classList.remove('btn-danger');
        });

        runFilterAndSearch();
    });

    // Event listener untuk kotak pencarian
    searchInput.addEventListener('input', runFilterAndSearch);
});
</script>

@endpush
