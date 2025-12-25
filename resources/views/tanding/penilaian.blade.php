@extends('layouts.app')

@section('content')
<style>
    /* Full screen 16:9 layout */
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }
    
    .fullscreen-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Base responsive styles */
    .responsive-score {
        font-size: clamp(60px, 15vw, 150px);
    }
    
    .responsive-timer {
        font-size: clamp(24px, 5vw, 50px);
    }
    
    .responsive-icon {
        width: clamp(30px, 6vw, 60px);
        height: clamp(30px, 6vw, 60px);
    }
    
    .responsive-juri-icon {
        width: clamp(25px, 5vw, 50px);
        height: clamp(25px, 5vw, 50px);
    }
    
    .responsive-juri-box {
        font-size: clamp(10px, 2vw, 14px);
        padding: 0.25rem 0.5rem !important;
    }
    
    .responsive-stat-col {
        width: clamp(60px, 12vw, 110px);
        font-size: clamp(10px, 2vw, 16px);
    }
    
    /* 16:9 optimized spacing */
    .content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1vh 2vw;
    }
    
    .score-section {
        flex: 1;
        display: flex;
        align-items: center;
    }
    
    /* Mobile devices (max-width: 576px) */
    @media (max-width: 576px) {
        .container {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        
        .title-section h5 {
            font-size: 14px !important;
        }
        
        .title-section p {
            font-size: 11px !important;
        }
        
        .score-section .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        .penalty-icons .d-flex {
            margin-top: 0.5rem !important;
        }
        
        .round-box {
            padding: 0.25rem !important;
            margin-top: 0.5rem !important;
            font-size: 12px !important;
        }
        
        .juri-section {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        
        .stats-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .stats-wrapper::-webkit-scrollbar {
            height: 6px;
        }
        
        .stats-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
    }
    
    /* Tablet devices (577px - 768px) */
    @media (min-width: 577px) and (max-width: 768px) {
        .responsive-score {
            font-size: 100px;
        }
        
        .responsive-timer {
            font-size: 35px;
        }
        
        .title-section h5 {
            font-size: 18px !important;
        }
    }
    
    /* Small tablets and large phones (769px - 992px) */
    @media (min-width: 769px) and (max-width: 992px) {
        .responsive-score {
            font-size: 120px;
        }
    }
    
    /* 16:9 aspect ratio optimization (typical displays) */
    @media (min-aspect-ratio: 16/9) {
        .content-wrapper {
            padding: 2vh 3vw;
        }
    }
</style>

    <div class="fullscreen-container px-0 rounded" style="background-color: rgb(216, 216, 216)">
       
        {{-- Navbar --}}
        <div class="border-bottom w-100" style="background-color: blueviolet; ">
            <div class="container-fluid">
                <p class="text-light text-center m-0 py-2" style="font-size: clamp(16px, 2.5vw, 24px)">
                    SCORING DIGITAL
                </p>
            </div>
        </div>

        <div class="content-wrapper">
        <div class="container-fluid px-3">
            {{-- Title --}}
            <div class="d-flex justify-content-between title-section flex-wrap">
                <div class="m-2">
                    <p class="text-start m-0">Kontingen Biru</p>
                    <h5 class="text-primary">Nama Pemain Biru</h5>
                </div>
                <div class="m-2">
                    <div class="px-3 pt-2 pt-md-5 text-dark text-center" style="border-radius: 10px">
                        <span class="text-bold libertinus-font responsive-timer" id="timer">02:00</span>
                    </div>
                </div>
                <div class="mt-2 me-2 text-end">
                    <p class="m-0">Kontingen Merah</p>
                    <h5 class="text-danger">Nama Pemain Merah</h5>
                </div>
            </div>

            {{-- Score Section --}}
            <div class="row justify-content-between score-section">
                {{-- Team Blue --}}
                <div class="col-5 col-sm-5">
                    <div class="row">
                        <div class="col-5">
                            <div class="d-flex flex-column penalty-icons">
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Binaan 1" class="responsive-icon" style="rotate:270deg;" id="blue-notif-binaan-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Binaan 2" class="responsive-icon" style="rotate:270deg;" id="blue-notif-binaan-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Teguran 1" class="responsive-icon" id="blue-notif-teguran-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Teguran 2" class="responsive-icon" id="blue-notif-teguran-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 1" class="responsive-icon" id="blue-notif-peringatan-1">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 2" class="responsive-icon" id="blue-notif-peringatan-2">
                                </div>
                            </div>
                        </div>
                        <div class="col-7 h-100">
                            <div class="border bg-primary px-2 px-md-4 pb-3 rounded">
                                <p id="total-point-blue" class="text-center text-light m-0 responsive-score">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-2 justify-content-center text-center">
                    <div class="scoring">
                        <div id="round-box-1" class="round-box p-1 mt-3 border bg-warning" style="border-radius: 10px;">I</div>
                        <div id="round-box-2" class="round-box p-1 mt-3 border bg-light" style="border-radius: 10px;">II</div>
                        <div id="round-box-3" class="round-box p-1 mt-3 border bg-light" style="border-radius: 10px;">III</div>
                    </div>
                </div>

                {{-- Team Red --}}
                <div class="col-5 col-sm-5">
                    <div class="row">
                        <div class="col-7 h-100">
                            <div class="border bg-danger px-2 px-md-4 pb-3 rounded">
                                <p id="total-point-red" class="text-center text-light m-0 responsive-score">0</p>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="d-flex flex-column penalty-icons">
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Binaan 1" class="responsive-icon" style="rotate:270deg;" id="red-notif-binaan-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Binaan 2" class="responsive-icon" style="rotate:270deg;" id="red-notif-binaan-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-onefinger.png') }}" alt="Teguran 1" class="responsive-icon" id="red-notif-teguran-1">
                                    <img src="{{ asset('assets/img/icon/icon-twofinger.png') }}" alt="Teguran 2" class="responsive-icon" id="red-notif-teguran-2">
                                </div>
                                <div class="d-flex justify-content-around mt-3">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 1" class="responsive-icon" id="red-notif-peringatan-1">
                                    <img src="{{ asset('assets/img/icon/icon-wasit.png') }}" alt="Peringatan 2" class="responsive-icon" id="red-notif-peringatan-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Juri Indicators --}}
            <div class="row mt-4 juri-section">
                <div class="col-12 col-md-6" style="padding-right: 10px">
                    <div class="d-flex justify-content-end align-items-center mb-2">
                        <div class="d-flex flex-wrap">
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-1-pukul"><p class="m-0">Juri 1</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-2-pukul"><p class="m-0">Juri 2</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-3-pukul"><p class="m-0">Juri 3</p></div>
                        </div>
                        <img src="{{ asset('assets/img/icon/icon-pkl.png') }}" alt="pukul icon" class="responsive-juri-icon ms-2">
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="d-flex flex-wrap">
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-1-tendang"><p class="m-0">Juri 1</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-2-tendang"><p class="m-0">Juri 2</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="blue-notif-juri-3-tendang"><p class="m-0">Juri 3</p></div>
                        </div>
                        <img src="{{ asset('assets/img/icon/icon-tdg.png') }}" alt="tendang icon" class="responsive-juri-icon ms-2" style="transform: scaleX(-1);">
                    </div>
                </div>
                <div class="col-12 col-md-6" style="padding-left: 10px">
                    <div class="d-flex justify-content-start align-items-center mb-2 mt-3 mt-md-0">
                        <img src="{{ asset('assets/img/icon/icon-pkl.png') }}" alt="pukul icon" class="responsive-juri-icon me-2" style="transform: scaleX(-1);">
                        <div class="d-flex flex-wrap">
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-1-pukul"><p class="m-0">Juri 1</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-2-pukul"><p class="m-0">Juri 2</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-3-pukul"><p class="m-0">Juri 3</p></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center">
                        <img src="{{ asset('assets/img/icon/icon-tdg.png') }}" alt="tendang icon" class="responsive-juri-icon me-2">
                        <div class="d-flex flex-wrap">
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-1-tendang"><p class="m-0">Juri 1</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-2-tendang"><p class="m-0">Juri 2</p></div>
                            <div class="bg-light mx-1 rounded border shadow-sm responsive-juri-box" id="red-notif-juri-3-tendang"><p class="m-0">Juri 3</p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Stats Table --}}
            <div class="stats-wrapper mt-4">
            <div class="d-flex justify-content-center">
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Sudut</div>
                    <div class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">Biru</div>
                    <div class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">Merah</div>
                </div>
                {{-- Kolom Pukulan --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Pukulan</div>
                    <div id="stat-blue-pukul" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-pukul" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Tendangan --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Tendangan</div>
                    <div id="stat-blue-tendang" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-tendang" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Jatuhan --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Jatuhan</div>
                    <div id="stat-blue-jatuhan" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-jatuhan" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Teguran 1 --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Teguran 1</div>
                    <div id="stat-blue-teguran1" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-teguran1" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Teguran 2 --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Teguran 2</div>
                    <div id="stat-blue-teguran2" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-teguran2" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Peringatan 1 --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Peringatan 1</div>
                    <div id="stat-blue-peringatan1" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-peringatan1" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                {{-- Kolom Peringatan 2 --}}
                <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Peringatan 2</div>
                    <div id="stat-blue-peringatan2" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-peringatan2" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
                 {{-- Kolom Peringatan 3 --}}
                 <div class="d-flex flex-column mx-1">
                    <div class="border border-info bg-info rounded-top py-2 px-1 text-center text-light responsive-stat-col">Peringatan 3</div>
                    <div id="stat-blue-peringatan3" class="border border-primary bg-primary py-2 px-1 text-center text-light responsive-stat-col">0</div>
                    <div id="stat-red-peringatan3" class="border border-danger bg-danger rounded-bottom py-2 px-1 text-center text-light responsive-stat-col">0</div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script type="module" src='/js/listenTanding.js'></script>
@endsection