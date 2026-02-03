@extends('main.main')

@section('styles')
<style>
    /* CSS untuk dropdown status Anda (tidak diubah) */
    .status-dropdown {
        font-weight: 600; border-width: 2px; transition: all 0.2s ease-in-out;
    }
    .status-menunggu_peserta {
        border-color: #6c757d; color: #6c757d; background-color: #f8f9fa;
    }
    .status-siap_dimulai {
        border-color: #0d6efd; color: #0d6efd; background-color: #cfe2ff;
    }
    .status-berlangsung {
        border-color: #ffc107; color: #664d03; background-color: #fff3cd;
    }
    .status-selesai {
        border-color: #198754; color: #198754; background-color: #d1e7dd;
    }
    .status-ditunda {
        border-color: #dc3545; color: #dc3545; background-color: #f8d7da;
    }
</style>
@endsection

@section('content')
    <div class="container mt-2 mb-0 rounded-top pb-4 pt-3"
        style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #c0c0c0">
        <div class="d-flex justify-content-center">
            {{-- MODIFIKASI: Menambahkan class 'filter-btn' dan atribut 'data-jenis' --}}
            <button id="resetFilter" class="btn btn-secondary mx-3 text-light fs-5" type="button" style="width: 150px; height:50px;">Semua</button>
            <button class="btn btn-secondary mx-3 text-light fs-5 filter-btn" data-jenis="Tanding" type="button" style="width: 150px; height:50px;">Tanding</button>
            <button class="btn mx-3 text-light fs-5 filter-btn" data-jenis="Seni" type="button" style="width: 150px; height:50px; background-color:rgb(100, 100, 100)">Artistics</button>
            <button class="btn mx-3 text-light fs-5 filter-btn" data-jenis="Jurus Baku" type="button" style="width: 150px; height:50px; background-color:rgb(100, 100, 100)">Jurus Baku</button>
        </div>
    </div>
    
    <div class="container rounded-0 pb-2 pt-3" style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #c0c0c0">
        {{-- Area kosong sesuai desain asli Anda --}}
    </div>

    <div class="container rounded-bottom pb-4 pt-3" style="background-color: rgb(216, 216, 216);">
        
        <div class="table-responsive bg-white p-3 rounded">
            <h2 class="text-center mb-4">
                Jadwal Pertandingan
                @if(isset($arena))
                    <span class="fw-normal" style="font-size:1.1em">- {{ $arena->arena_name ?? $arena->arena_name ?? '-' }}</span>
                @endif
            </h2>
            
            {{-- MODIFIKASI: Menambahkan kotak pencarian --}}
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
                {{-- MODIFIKASI: Menambahkan ID pada tbody --}}
                <tbody id="pertandinganTableBody">
                    @forelse ($daftar_pertandingan as $pertandingan)
                        {{-- MODIFIKASI: Menambahkan atribut data-jenis pada <tr> --}}
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
                                    $statusOptions = [ 'menunggu_peserta' => 'Menunggu Peserta', 'siap_dimulai' => 'Siap Dimulai', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'ditunda' => 'Ditunda'];
                                @endphp
                                <select class="form-select status-dropdown status-{{ $pertandingan->status }}" data-id="{{ $pertandingan->id }}">
                                    @foreach ($statusOptions as $value => $text)
                                        <option value="{{ $value }}" {{ $pertandingan->status == $value ? 'selected' : '' }}>
                                            {{ $text }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width: 220px;">
                                @if ($pertandingan->status == 'berlangsung')
                                    <a href="{{ url('scoring/penilaian/' . Auth::user()->id) }}" class="btn btn-sm btn-info">Lihat Match</a>

                                    <a href="{{ route('rekap-operator', ['user' => Auth::user()->id]) }}" class="btn btn-sm btn-secondary">Rekap Match</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <h4>Tidak ada data pertandingan yang ditemukan di Arena ini.</h4>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')

{{-- MODIFIKASI: SCRIPT BARU UNTUK FILTER DAN PENCARIAN (TANPA DATATABLES) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const resetButton = document.getElementById('resetFilter');
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#pertandinganTableBody tr');
    
    let activeFilter = ''; // Menyimpan filter jenis yang sedang aktif

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
                row.style.display = ''; // '' akan mengembalikan ke default (table-row)
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
        searchInput.value = ''; // Kosongkan juga input pencarian
        
        // Reset style tombol
        filterButtons.forEach(btn => {
            btn.style.backgroundColor = 'rgb(100, 100, 100)';
            btn.classList.remove('btn-danger');
        });

        runFilterAndSearch();
    });

    // Event listener untuk kotak pencarian (event 'input' lebih responsif)
    searchInput.addEventListener('input', runFilterAndSearch);
});
</script>


{{-- Script lama Anda untuk update status (tidak diubah) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusDropdowns = document.querySelectorAll('.status-dropdown');

    function updateDropdownColor(dropdown) {
        dropdown.classList.remove('status-menunggu_peserta', 'status-siap_dimulai', 'status-berlangsung', 'status-selesai', 'status-ditunda');
        dropdown.classList.add('status-' + dropdown.value);
    }

    statusDropdowns.forEach(dropdown => {
        dropdown.dataset.originalStatus = dropdown.value;

        dropdown.addEventListener('change', function () {
            const pertandinganId = this.dataset.id;
            const newStatus = this.value;
            const url = `{{ url('scoring/operator/update-status') }}/${pertandinganId}`;

            if (!confirm(`Anda yakin ingin mengubah status pertandingan #${pertandinganId} menjadi "${this.options[this.selectedIndex].text}"?`)) {
                this.value = this.dataset.originalStatus;
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Gagal mengubah status.'); });
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                this.dataset.originalStatus = newStatus;
                updateDropdownColor(this);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                this.value = this.dataset.originalStatus;
                updateDropdownColor(this);
            });
        });
    });
});
</script>
@endpush