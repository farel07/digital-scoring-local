@extends('main.main')

@section('head')
    {{-- Meta tag untuk pertandingan ID agar JavaScript tahu match mana yang sedang dimainkan --}}
    <meta name="pertandingan-id" content="{{ $id }}">
    {{-- Meta tag untuk user ID agar JavaScript tahu juri mana yang sedang login --}}
    <meta name="user-id" content="{{ auth()->user()->id }}">
    {{-- Meta tag untuk max ronde agar JS bisa menyesuaikan --}}
    <meta name="max-ronde" content="{{ $max_ronde ?? 3 }}">
    <meta name="jenis-pertandingan" content="{{ $jenis_pertandingan ?? 'prestasi' }}">
    <style>
        /* Score log box */
        .score-log-box {
            display: flex;
            align-items: center;
            overflow: hidden;          /* Clips digits — box NEVER grows */
            white-space: nowrap;
            height: 56px;
            border-radius: 10px;
            border: 2px solid #ced4da;
            background: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 3px;
            padding: 0 10px;
            font-family: monospace;
        }
        /* Blue: digits slide left, newest on right */
        .score-log-box.blue-log  { direction: rtl; color: #0d6efd; }
        .score-log-box.blue-log span { direction: ltr; }
        /* Red: same idea */
        .score-log-box.red-log   { direction: rtl; color: #dc3545; }
        .score-log-box.red-log span { direction: ltr; }

        /* Round-box active (kuning, seperti penilaian) */
        .round-box.bg-warning { box-shadow: 0 0 10px rgba(255, 193, 7, 0.6); }

        /* Dim inactive score-log rows */
        .score-log-box       { transition: opacity 0.3s, border 0.3s; }
    </style>
@endsection

@section('content')
    <div class="container mt-2 mb-2 rounded pb-4" style="background-color: rgb(216, 216, 216)">
        <div class="container">
            {{-- title --}}
            <div class="d-flex justify-content-between">
                <div class="m-2">
                    <p class="text-start m-0">{{ $playerBlue->player_contingent }}</p>
                    <h5 class="text-primary">{{ $playerBlue->player_name }}</h5>
                </div>
                <div class="m-2">
                    <p class="m-0 fw-bold">PARTAI 2</p>
                    <p class="m-0 fw-bold">ARENA 1</p>
                    <span class="badge {{ ($jenis_pertandingan ?? 'prestasi') === 'pemasalan' ? 'bg-warning text-dark' : 'bg-success' }} mt-1">
                        {{ strtoupper($jenis_pertandingan ?? 'prestasi') }}
                    </span>
                </div>
                <div class="mt-2 me-2">
                    <p class="text-end m-0">{{ $playerRed->player_contingent }}</p>
                    <h5 class="text-end text-danger">{{ $playerRed->player_name }}</h5>
                </div>
            </div>
            {{-- end title --}}
    
            {{-- score --}}
            <div class="row justify-content-between">

                {{-- team blue --}}
                <div class="col-4">
                    <div class="p-3 border bg-primary text-light text-center" style="border-radius: 10px">TEAM BLUE
                    </div>

                    {{-- Score log boxes: 1 per round --}}
                    @foreach(range(1, $max_ronde ?? 3) as $r)
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div class="score-log-box blue-log flex-grow-1" id="score-log-blue-{{ $r }}">
                            <span id="score-log-blue-inner-{{ $r }}"></span>
                        </div>
                    </div>
                    @endforeach

                    <div class="row justify-content-between">
                        <div class="col-6 mb-3">
                            <button class="mt-3 btn btn-primary w-100" type="button" onclick="sendPoin('PUKUL', 'blue')"
                                style="border-radius: 10px; height: 100px"><img class="w-25 me-2"
                                    src="{{ asset('assets') }}/img/icon/logo-pukul.png" alt="lah" >PUKUL</button>

                        </div>
                        <div class="col-6">
                            <button class="mt-3 btn btn-secondary w-100 text-light" type="button"
                                style="border-radius: 10px; height: 100px" onclick="hapusTerakhir('blue')">
                                HAPUS TERBARU
                            </button>
                        </div>
                        <div class="d-grid gap-2 col-6 me-auto mt-3">
                            <button class="btn btn-primary" type="button" style="border-radius: 10px; height: 100px" onclick="sendPoin('TENDANG', 'blue')"><img
                                    class="w-25 me-1 mb-3" src="{{ asset('assets') }}/img/icon/logo-tendang.png"
                                    alt="lah" >TENDANG</button>
                        </div>
                    </div>
                </div>

                {{-- end team blue --}}

                {{-- scoring --}}
                <div class="col-3">
                    <div class="p-3 border bg-warning text-light text-center" style="border-radius: 10px">SCORE</div>
                    @foreach(range(1, $max_ronde ?? 3) as $r)
                        @php $roman = ['I','II','III'][$r-1] ?? $r; @endphp
                        <div id="round-box-{{ $r }}" class="round-box p-3 mt-3 border text-center {{ $r === 1 ? 'bg-warning text-white' : 'bg-light' }}" style="border-radius: 10px; transition: background-color 0.3s;">{{ $roman }}</div>
                    @endforeach
                </div>
                {{-- end scoring --}}

                {{-- team red --}}
                <div class="col-4">
                    <div class="p-3 border bg-danger text-light text-center" style="border-radius: 10px">TEAM RED</div>

                    {{-- Score log boxes: 1 per round --}}
                    @foreach(range(1, $max_ronde ?? 3) as $r)
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <div class="score-log-box red-log flex-grow-1" id="score-log-red-{{ $r }}">
                            <span id="score-log-red-inner-{{ $r }}"></span>
                        </div>
                    </div>
                    @endforeach

                    <div class="row justify-content-between">
                        <div class="col-6">
                            <button class="mt-3 btn btn-secondary w-100"
                                style="border-radius: 10px; height: 100px" onclick="hapusTerakhir('red')">
                                HAPUS TERBARU
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="mt-3 btn btn-danger w-100" type="button"
                                style="border-radius: 10px; height:100px" onclick="sendPoin('PUKUL', 'red')"><img class="w-25 me-1"
                                    src="{{ asset('assets') }}/img/icon/logo-pukul.png" alt="lah"> PUKUL</button>
                        </div>
                        <div class="d-grid gap-2 col-6 ms-auto mt-3">
                            <button class="btn btn-danger" type="button" style="border-radius: 10px; height: 100px" onclick="sendPoin('TENDANG', 'red')"><img
                                    class="w-25 me-1 mb-3" src="{{ asset('assets') }}/img/icon/logo-tendang.png"
                                    alt="lah">TENDANG</button>
                        </div>
                    </div>
                </div>
                {{-- end team red --}}
            </div>
            {{-- end score --}}
        </div>
    </div>

    {{-- Validation Popup Modal --}}
    <div class="modal fade" id="validationModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title w-100 text-center">⚠️ Validation Request</h5>
                </div>
                <div class="modal-body p-4">
                    <p id="validation-description" class="text-center mb-4 fs-5 fw-bold">Dewan requests validation...</p>
                    <div class="d-grid gap-3">
                        <button class="btn btn-success btn-lg py-3 fw-bold" onclick="submitVote('SAH')">
                            ✅ SAH
                        </button>
                        <button class="btn btn-danger btn-lg py-3 fw-bold" onclick="submitVote('TIDAK SAH')">
                            ❌ TIDAK SAH
                        </button>
                        <button class="btn btn-secondary btn-lg py-3 fw-bold" onclick="submitVote('NETRAL')">
                            ⚪ NETRAL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Define globals needed by listenTanding.js BEFORE loading it --}}
    <script>
        const PERTANDINGAN_ID = {{ $id }};
        const MAX_RONDE = {{ $max_ronde ?? 3 }};
        const JENIS_PERTANDINGAN = '{{ $jenis_pertandingan ?? 'prestasi' }}';
    </script>
    <script src="/js/sendEventJuriTanding.js"></script>
    <script src="/js/listenTanding.js"></script>
    <script src="/js/validationJuri.js"></script>
@endsection