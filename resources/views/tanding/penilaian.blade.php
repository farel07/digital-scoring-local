@extends('layouts.app')

@section('content')
    <div class="container px-0 mt-2 mb-2 rounded pb-4" style="background-color: rgb(216, 216, 216)">
       
        {{-- Navbar --}}
        <div class="border rounded-top w-100" style="background-color: blueviolet; ">
            <div class="container">
                <p class="text-light text-center m-0 p-2" style="font-size: 20px">
                    SCORING DIGITAL
                </p>
            </div>
        </div>
        {{-- End Navbar --}}

        <div class="container">
            {{-- Title --}}
            <div class="d-flex justify-content-between">
                <div class="m-2">
                    <p class="text-start m-0">Kontingen Biru</p>
                    <h5 class="text-primary">Nama Pemain Biru</h5>
                </div>
                <div class="m-2">
                    <div class="px-3 pt-5 text-dark text-center" style="border-radius: 10px">
                        <span class="text-bold libertinus-font" style="font-size: 50px" id="timer">02:00</span>
                    </div>
                </div>
                <div class="mt-2 me-2 text-end">
                    <p class="m-0">Kontingen Merah</p>
                    <h5 class="text-danger">Nama Pemain Merah</h5>
                </div>
            </div>
            {{-- End Title --}}

            {{-- Score Section --}}
            <div class="row justify-content-between">
                {{-- Team Blue --}}
                <div class="col-5">
                    <div class="row">
                        <div class="col-5">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Binaan 1" style="width:60px; height:60px; rotate:270deg;" id="blue-notif-binaan-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Binaan 2" style="width:60px; height:60px; rotate:270deg;" id="blue-notif-binaan-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Teguran 1" style="width:60px ; height: 60px;" id="blue-notif-teguran-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Teguran 2" style="width:60px ; height: 60px;" id="blue-notif-teguran-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 1" style="width:60px ; height: 60px;" id="blue-notif-peringatan-1">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 2" style="width:60px ; height: 60px;" id="blue-notif-peringatan-2">
                                </div>
                            </div>
                        </div>
                        <div class="col-7 h-100">
                            <div class="border bg-primary px-4 pb-3 rounded">
                                <p id="total-point-blue" class="text-center text-light m-0" style="font-size: 150px">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Round Indicator --}}
                <div class="col-2 justify-content-center text-center">
                    <div class="scoring">
                        <div id="round-box-1" class="p-1 mt-3 border bg-warning" style="border-radius: 10px; cursor: pointer;">I</div>
                        <div id="round-box-2" class="p-1 mt-3 border bg-light" style="border-radius: 10px; cursor: pointer;">II</div>
                        <div id="round-box-3" class="p-1 mt-3 border bg-light" style="border-radius: 10px; cursor: pointer;">III</div>
                    </div>
                </div>

                {{-- Team Red --}}
                <div class="col-5">
                    <div class="row">
                        <div class="col-7 h-100">
                            <div class="border bg-danger px-4 pb-3 rounded">
                                <p id="total-point-red" class="text-center text-light m-0" style="font-size: 150px">0</p>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="d-flex flex-column">
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Binaan 1" style="width:60px; height:60px; rotate:270deg;" id="red-notif-binaan-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Binaan 2" style="width:60px; height:60px; rotate:270deg;" id="red-notif-binaan-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Teguran 1" style="width:60px ; height: 60px;" id="red-notif-teguran-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Teguran 2" style="width:60px ; height: 60px;" id="red-notif-teguran-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 1" style="width:60px ; height: 60px;" id="red-notif-peringatan-1">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 2" style="width:60px ; height: 60px;" id="red-notif-peringatan-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Juri Indicators --}}
            <div class="row mt-4">
                <div class="col-6 pt-3" style="padding-right: 40px">
                    <div class="d-flex justify-content-end">
                        <div class="kiri d-flex">
                            <div class="bg-light mx-2 rounded border" id="blue-notif-juri-1-pukul"><p class="px-4 py-2 m-0">Juri 1</p></div>
                            <div class="bg-light mx-2 rounded border" id="blue-notif-juri-2-pukul"><p class="px-4 py-2 m-0">Juri 2</p></div>
                            <div class="bg-light mx-2 rounded border" id="blue-notif-juri-3-pukul"><p class="px-4 py-2 m-0">Juri 3</p></div>
                        </div>
                        <div class="d-flex flex-column">
                            <img src="{{ asset('assets/img/icon/icon-pkl.png') }}" alt="pukul icon" style="width: 60px; height: 60px">
                            <img src="{{ asset('assets/img/icon/icon-tdg.png') }}" alt="tendang icon" style="width: 50px; height: 50px; transform: scaleX(-1);">
                        </div>
                    </div>
                </div>
                <div class="col-6 pt-3" style="padding-left: 40px">
                    <div class="d-flex justify-content-start">
                        <div class="d-flex flex-column">
                            <img src="{{ asset('assets/img/icon/icon-pkl.png') }}" alt="pukul icon" style="width: 60px; height: 60px;  transform: scaleX(-1);">
                            <img src="{{ asset('assets/img/icon/icon-tdg.png') }}" alt="tendang icon" style="width: 50px; height: 50px">
                        </div>
                        <div class="kanan d-flex">
                            <div class="bg-light mx-2 rounded border" id="red-notif-juri-1-pukul"><p class="px-4 py-2 m-0">Juri 1</p></div>
                            <div class="bg-light mx-2 rounded border" id="red-notif-juri-2-pukul"><p class="px-4 py-2 m-0">Juri 2</p></div>
                            <div class="bg-light mx-2 rounded border" id="red-notif-juri-3-pukul"><p class="px-4 py-2 m-0">Juri 3</p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Stats Table --}}
            <div class="d-flex justify-content-center mt-4">
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light" style="width: 110px">Sudut</div>
                    <div class="border border-primary bg-primary py-2 px-1 text-center text-light" style="width: 110px">Biru</div>
                    <div class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light" style="width: 110px">Merah</div>
                </div>
                <!-- Contoh Data Statis Tabel -->
                @php $cols = ['Peringatan 3', 'Peringatan 2', 'Peringatan 1', 'Teguran 2', 'Teguran 1', 'Jatuhan', 'Tendangan', 'Pukulan']; @endphp
                @foreach($cols as $col)
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light" style="width: 110px">{{ $col }}</div>
                    <div class="border border-primary bg-primary py-2 px-1 text-center text-light" style="width: 110px">0</div>
                    <div class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light" style="width: 110px">0</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Script untuk menjalankan fitur dasar di Frontend --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Logika Timer Sederhana
            // let time = 120; // 2 Menit
            // const timerElement = document.getElementById("timer");

            // setInterval(() => {
            //     if (time > 0) {
            //         time--;
            //         const minutes = Math.floor(time / 60);
            //         const seconds = time % 60;
            //         timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            //     }
            // }, 1000);

            // Logika ganti Round statis
            const rounds = ['round-box-1', 'round-box-2', 'round-box-3'];
            rounds.forEach(id => {
                document.getElementById(id).addEventListener('click', function() {
                    rounds.forEach(r => document.getElementById(r).classList.replace('bg-warning', 'bg-light'));
                    this.classList.replace('bg-light', 'bg-warning');
                });
            });
        });
    </script>
     {{-- 1. Memuat Laravel Echo dari app.js --}}
    @vite(['resources/js/app.js'])
    <script type="module" src='/js/listenTanding.js'></script>
@endsection