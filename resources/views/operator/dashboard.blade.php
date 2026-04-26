@extends('main.main')

@section('content')
<style>
    .filter-btn {
        width: 150px; 
        height: 50px;
        transition: all 0.3s ease;
    }
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .filter-wide-btn {
        width: 300px;
        height: 50px;
        transition: all 0.3s ease;
    }
    .filter-wide-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Active states */
    .btn-type-active {
        box-shadow: 0 0 15px rgba(220, 53, 69, 0.6) !important;
        border: 2px solid #fff;
    }
    .btn-status-active {
        background-color: #6c757d !important;
        color: white !important;
        box-shadow: 0 0 10px rgba(108, 117, 125, 0.5) !important;
    }
</style>

<div class="container mt-2 mb-0 rounded pb-4 pt-3" style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #000000">
    <div class="d-flex justify-content-center">
        <a href="{{ route('operator.dashboard', ['type' => 'tanding', 'status' => $filterStatus]) }}" 
           class="btn filter-btn mx-3 text-light fs-5 {{ $filterType == 'tanding' ? 'btn-type-active' : '' }}" 
           style="background-color: {{ $filterType == 'tanding' ? '#dc3545' : '#e4606d' }};">
            Tanding
        </a>
        <a href="{{ route('operator.dashboard', ['type' => 'artistics', 'status' => $filterStatus]) }}" 
           class="btn filter-btn mx-3 text-light fs-5 {{ $filterType == 'artistics' ? 'btn-type-active' : '' }}" 
           style="background-color: {{ $filterType == 'artistics' ? '#dc3545' : '#e4606d' }};">
            Artistics
        </a>
        <a href="{{ route('operator.dashboard', ['type' => 'jurus baku', 'status' => $filterStatus]) }}" 
           class="btn filter-btn mx-3 text-light fs-5 {{ $filterType == 'jurus baku' ? 'btn-type-active' : '' }}" 
           style="background-color: {{ $filterType == 'jurus baku' ? '#dc3545' : '#e4606d' }};">
            Jurus Baku
        </a>
    </div>
</div>

<div class="container rounded pb-4 pt-3" style="background-color: rgb(216, 216, 216); border-bottom: 1px solid #000000">
    <div class="d-flex justify-content-center align-items-center gap-3">
        <!-- These filters are placeholders for future functionality, currently inactive -->
        <a href="{{ route('operator.dashboard', ['type' => 'all', 'status' => 'all', 'class' => 'all', 'gender' => 'all']) }}" class="btn btn-light filter-wide-btn fs-5 text-decoration-none text-dark d-flex align-items-center justify-content-center">All Matches</a>
        
        <select class="form-select filter-wide-btn fs-5" id="classFilter" onchange="window.location.href=this.value">
            <option value="{{ route('operator.dashboard', request()->except('class') + ['class' => 'all']) }}" {{ $filterClass == 'all' ? 'selected' : '' }}>All Class</option>
            @foreach($availableClasses as $kelas)
                <option value="{{ route('operator.dashboard', request()->except('class') + ['class' => $kelas->id]) }}" {{ $filterClass == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
        
        {{-- <select class="form-select filter-wide-btn fs-5" id="genderFilter" onchange="window.location.href=this.value">
            <option value="{{ route('operator.dashboard', request()->except('gender') + ['gender' => 'all']) }}" {{ $filterGender == 'all' ? 'selected' : '' }}>All Gender</option>
            <option value="{{ route('operator.dashboard', request()->except('gender') + ['gender' => 'putra']) }}" {{ $filterGender == 'putra' ? 'selected' : '' }}>Putra</option>
            <option value="{{ route('operator.dashboard', request()->except('gender') + ['gender' => 'putri']) }}" {{ $filterGender == 'putri' ? 'selected' : '' }}>Putri</option>
        </select> --}}
    </div>
    <div class="d-flex justify-content-center mt-3">
        <a href="{{ route('operator.dashboard', request()->except('status') + ['status' => 'belum_dimulai']) }}" 
           class="btn btn-light filter-wide-btn mx-3 fs-5 {{ $filterStatus == 'belum_dimulai' ? 'btn-status-active' : '' }}">
            Undone (Belum Dimulai)
        </a>
        <a href="{{ route('operator.dashboard', request()->except('status') + ['status' => 'berlangsung']) }}" 
           class="btn btn-light filter-wide-btn mx-3 fs-5 {{ $filterStatus == 'berlangsung' ? 'btn-status-active' : '' }}">
            Session (Berlangsung)
        </a>
        <a href="{{ route('operator.dashboard', request()->except('status') + ['status' => 'selesai']) }}" 
           class="btn btn-light filter-wide-btn mx-3 fs-5 {{ $filterStatus == 'selesai' ? 'btn-status-active' : '' }}">
            Selesai
        </a>
    </div>
</div>

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="{{ route('operator.dashboard') }}" method="GET" class="d-flex">
                <!-- Preserve existing filters -->
                <input type="hidden" name="type" value="{{ $filterType }}">
                <input type="hidden" name="status" value="{{ $filterStatus }}">
                <input type="hidden" name="class" value="{{ $filterClass }}">
                <input type="hidden" name="gender" value="{{ $filterGender }}">
                
                <input type="text" name="search" class="form-control me-2 fs-5" placeholder="Cari nama peserta atau partai..." value="{{ $searchQuery ?? '' }}">
                <button type="submit" class="btn btn-primary fs-5 px-4">Cari</button>
                @if(!empty($searchQuery))
                    <a href="{{ route('operator.dashboard', ['type' => $filterType, 'status' => $filterStatus, 'class' => $filterClass, 'gender' => $filterGender]) }}" class="btn btn-secondary fs-5 px-3 ms-2">Reset</a>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="container rounded pb-4 pt-3 mt-3" style="background-color: rgb(216, 216, 216);">
    <h3 class="text-center mb-4">Daftar Pertandingan - {{ $arena->arena_name }}</h3>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Partai</th>
                    <th>Kelas</th>
                    <th>Sudut Biru</th>
                    <th>Sudut Merah</th>
                    <th>Status</th>
                    <th style="width: 250px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftar_pertandingan as $match)
                    @php
                        $biru = $match->players->where('side_number', 1)->first();
                        $merah = $match->players->where('side_number', 2)->first();
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $match->id }}</td>
                        <td>{{ $match->kelas->nama_kelas ?? '-' }}</td>
                        <td class="text-primary fw-bold">{{ $biru ? $biru->player_name : '-' }} <br> <small class="text-muted">{{ $biru ? $biru->player_contingent : '' }}</small></td>
                        <td class="text-danger fw-bold">{{ $merah ? $merah->player_name : '-' }} <br> <small class="text-muted">{{ $merah ? $merah->player_contingent : '' }}</small></td>
                        <td>
                            @if($match->status == 'belum_dimulai')
                                <span class="badge bg-secondary">Belum Dimulai</span>
                            @elseif($match->status == 'berlangsung')
                                <span class="badge bg-success shadow-sm" style="animation: pulse 2s infinite;">Berlangsung</span>
                            @else
                                <span class="badge bg-dark">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <select class="form-select form-select-sm status-dropdown" data-id="{{ $match->id }}" style="width: 130px;">
                                    <option value="belum_dimulai" {{ $match->status == 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                                    <option value="berlangsung" {{ $match->status == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                    <option value="selesai" {{ $match->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                
                                @if($match->status == 'berlangsung')
                                    <a href="/penilaian/{{ $match->id }}" class="btn btn-sm btn-success">
                                        Lihat Match
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted py-5">
                            <h4 class="mb-0">Tidak ada data pertandingan.</h4>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $daftar_pertandingan->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Handle dropdown status change
        const statusDropdowns = document.querySelectorAll('.status-dropdown');
        statusDropdowns.forEach(dropdown => {
            // Save original value to revert if cancelled
            dropdown.addEventListener('focus', function() {
                this.setAttribute('data-original-value', this.value);
            });
            
            dropdown.addEventListener('change', function() {
                const matchId = this.getAttribute('data-id');
                const newStatus = this.value;
                const originalValue = this.getAttribute('data-original-value') || 'belum_dimulai';
                
                let confirmMessage = '';
                let confirmTitle = 'Ubah Status?';
                
                if (newStatus === 'selesai') {
                    confirmTitle = 'Akhiri Pertandingan?';
                    confirmMessage = "Pertandingan ini akan ditandai selesai.";
                } else if (newStatus === 'berlangsung') {
                    confirmTitle = 'Mulai Pertandingan?';
                    confirmMessage = "Pertandingan ini akan dimulai dan pertandingan lain yang sedang berlangsung akan otomatis diselesaikan.";
                } else {
                    confirmMessage = "Status pertandingan akan dikembalikan ke Belum Dimulai.";
                }
                
                Swal.fire({
                    title: confirmTitle,
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: newStatus === 'selesai' ? '#d33' : '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateMatchStatus(matchId, newStatus);
                    } else {
                        // Revert selection if cancelled
                        this.value = originalValue;
                    }
                });
            });
        });

        function updateMatchStatus(matchId, newStatus) {
            fetch('{{ route('operator.updateStatus') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    pertandingan_id: matchId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            });
        }
    });
</script>
@endpush
@endsection
