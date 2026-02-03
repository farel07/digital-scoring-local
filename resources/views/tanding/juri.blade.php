@extends('main.main')

@section('head')
    {{-- Meta tag untuk pertandingan ID agar JavaScript tahu match mana yang sedang dimainkan --}}
    <meta name="pertandingan-id" content="{{ $id }}">
    {{-- Meta tag untuk user ID agar JavaScript tahu juri mana yang sedang login --}}
    <meta name="user-id" content="{{ auth()->user()->id }}">
@endsection

@section('content')
    <div class="container mt-2 mb-2 rounded pb-4" style="background-color: rgb(216, 216, 216)">
        <div class="container">
            {{-- title --}}
            <div class="d-flex justify-content-between">
                <div class="m-2">
                    <p class="text-start m-0">CONTINGENT</p>
                    <h5 class="text-primary">ATHLETE</h5>
                </div>
                <div class="m-2">
                    <p class="m-0 fw-bold">PARTAI 2</p>
                    <p class="m-0 fw-bold">ARENA 1</p>
                </div>
                <div class="mt-2 me-2">
                    <p class="text-end m-0">CONTINGENT</p>
                    <h5 class="text-end text-danger">ATHLETE</h5>
                </div>
            </div>
            {{-- end title --}}
    
            {{-- score --}}
            <div class="row justify-content-between">

                {{-- team blue --}}
                <div class="col-4">
                    <div class="p-3 border bg-primary text-light text-center" style="border-radius: 10px">TEAM BLUE
                    </div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="row justify-content-between">
                        <div class="col-6 mb-3">
                            <button class="mt-3 btn btn-primary w-100" type="button" onclick="sendPoin('PUKUL', 'blue')"
                                style="border-radius: 10px; height: 100px"><img class="w-25 me-2"
                                    src="{{ asset('assets') }}/img/icon/logo-pukul.png" alt="lah" >PUKUL</button>

                        </div>
                        <div class="col-6">
                            <button class="mt-3 btn w-100 text-light" type="button"
                                style="border-radius: 10px; height: 100px; background-color:rgb(190, 0, 0)">HAPUS POINT
                                TERBARU</button>
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
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">I</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">II</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">III</div>
                </div>
                {{-- end scoring --}}

                {{-- team red --}}
                <div class="col-4">
                    <div class="p-3 border bg-danger text-light text-center" style="border-radius: 10px">TEAM RED</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="p-3 mt-3 border bg-light text-center" style="border-radius: 10px">-</div>
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <button class="mt-3 btn btn-primary w-100" type="button"
                                style="border-radius: 10px; background-color:rgb(190, 0, 0); height: 100px">HAPUS POINT
                                TERBARU</button>

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

    <script src="/js/sendEventJuriTanding.js"></script>
    <script src="/js/listenTanding.js"></script>
    <script src="/js/validationJuri.js"></script>
@endsection