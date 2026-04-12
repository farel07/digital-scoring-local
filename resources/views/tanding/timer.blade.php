@extends('main.main')

@section('content')
<div class="container mt-2 mb-2 rounded pb-4" style="background-color: rgb(216, 216, 216)">
    <div class="container">
        {{-- Handling jika user tidak punya arena --}}
        @if(isset($error))
            <div class="alert alert-danger text-center mt-4">
                <h4>{{ $error }}</h4>
            </div>
        @else

            @if($pertandingan)
                <input type="hidden" id="pertandingan_id" value="{{ $pertandingan->id }}">
                <meta name="pertandingan-id" content="{{ $pertandingan->id }}">
            @endif
            {{-- title --}}
            <div class="d-flex justify-content-between">
                <div class="m-2">
                    <p class="text-start m-0">
                        {{-- Menampilkan nomor partai jika ada pertandingan --}}
                        {{ $pertandingan->id ?? '0' }}
                    </p>
                </div>
                <div class="m-2">
                    <p class="m-0 fw-bold">{{ $arena->arena_name ?? 'ARENA' }}</p>
                </div>
                <div class="mt-2 me-2">
                    <p class="text-end m-0" id="round-indicator">
                        {{-- Menampilkan ronde saat ini jika ada pertandingan --}}
                        ROUND {{ $pertandingan->current_round ?? '0' }}
                    </p>
                </div>
            </div>
            {{-- end title --}}

            {{-- Jika tidak ada pertandingan, tampilkan pesan menunggu --}}
            @if(!$pertandingan)
            <div class="row">
                <div class="col-12">
                    <div class="p-5 border bg-light text-dark text-center" style="border-radius: 10px">
                        <h2 class="fw-bold">MENUNGGU JADWAL PERTANDINGAN BERIKUTNYA...</h2>
                    </div>
                </div>
            </div>
            @else
            {{-- status --}}
            <div class="row">
                {{-- Sudut Merah --}}
                <div class="col-4">
                    <div class="p-3 border bg-light text-dark text-center" style="border-radius: 10px; height: 100%;">
                        <h5 class="text-danger fw-bold">{{ $peserta1->player_name ?? 'MENUNGGU PESERTA' }}</h5>
                        <p class="m-auto">{{ $peserta1->player_contingent ?? 'Kontingen' }}</p>
                    </div>
                </div>
                {{-- Sudut Biru --}}
                <div class="col-4">
                    <div class="p-3 border bg-light text-dark text-center" style="border-radius: 10px; height: 100%;">
                        <h5 class="text-primary fw-bold">{{ $peserta2->player_name ?? 'MENUNGGU PESERTA' }}</h5>
                        <p class="m-auto">{{ $peserta2->player_contingent ?? 'Kontingen' }}</p>
                    </div>
                </div>
                {{-- Info Kelas Pertandingan --}}
                <div class="col-2">
                    <div class="p-3 border bg-light text-dark text-center" style="border-radius: 10px; height: 100%;">
                        <h5 class="text-bold">{{ strtoupper($kelasInfo->gender ?? 'GENDER') }}</h5>
                        <p class="m-auto">{{ $kelasInfo->kelas->rentangUsia->rentang_usia ?? 'Kelas' }}</p>
                    </div>
                </div>
                {{-- Info Babak --}}
                <div class="col-2">
                    <div class="p-3 border bg-light text-dark text-center" style="border-radius: 10px; height: 100%;">
                        <h5 class="text-bold">{{ $roundName ?? 'BABAK' }}</h5>
                        <p class="m-auto">{{ $kelasInfo->jenis_pertandingan ?? 'Jenis' }}</p>
                        <span class="badge {{ ($jenis_pertandingan ?? 'prestasi') === 'pemasalan' ? 'bg-warning text-dark' : 'bg-success' }} mt-1">
                            {{ strtoupper($jenis_pertandingan ?? 'prestasi') }}
                        </span>
                    </div>
                </div>
            </div>
            {{-- end of status --}}
            @endif

            <!-- FITUR BARU: OPSI TIMER -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="form-group">
                        <label for="timeSelector" class="fw-bold mb-2">PILIH DURASI WAKTU</label>
                        <select id="timeSelector" class="form-select form-select-lg">
                            <option value="60">1 Menit</option>
                            <option value="90">1 Menit 30 Detik</option>
                            <option value="120" selected>2 Menit</option> <!-- Default -->
                            <option value="150">2 Menit 30 Detik</option>
                            <option value="180">3 Menit</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- END OF FITUR BARU -->

            {{-- Timer --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="p-3 border bg-light text-dark text-center" style="border-radius: 10px">
                        <span class="text-bold libertinus-font" style="font-size: 100px" id="timer">00:00</span>
                    </div>
                </div>
            </div>
            {{-- end of Timer --}}

            {{-- button timer --}}
            <div class="row mt-3">
                <div class="col-4">
                    <div class="btn w-100 pt-4" id="startBtn" style="height: 100px; background-color:rgb(1, 196, 1);">
                        <span class="text-light fs-2">
                            START
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="btn w-100 pt-4" id="pauseBtn" style="height: 100px; background-color:rgb(194, 0, 0);">
                        <span class="text-light fs-2">
                            STOP
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="btn w-100 pt-4" id="resetBtn" style="height: 100px; background-color:rgb(255, 136, 0)">
                        <span class="text-light fs-2">
                            RESET
                        </span>
                    </div>
                </div>
            </div>
            {{-- end of button timer --}}

            {{-- round --}}
            <div class="row mt-3">
                <div class="col-4">
                    <div class="btn btn-primary w-100 pt-4 @if($pertandingan && $pertandingan->current_round > 1) disabled @endif" style="height: 100px;" id="round1Btn" data-round="1">
                        <span class="text-light fs-2">ROUND 1</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="btn btn-primary w-100 pt-4 @if($pertandingan && $pertandingan->current_round > 2) disabled @endif" style="height: 100px" id="round2Btn" data-round="2">
                        <span class="text-light fs-2">ROUND 2</span>
                    </div>
                </div>

                {{-- Round 3: hanya tampil untuk prestasi (max_ronde = 3) --}}
                @if(($max_ronde ?? 3) >= 3)
                <div class="col-4">
                    <div class="btn btn-primary w-100 pt-4" style="height: 100px;" id="round3Btn" data-round="3">
                        <span class="text-light fs-2">ROUND 3</span>
                    </div>
                </div>
                @else
                {{-- Placeholder kosong agar layout col tetap simetris, tombol Round 3 disembunyikan --}}
                <div class="col-4">
                    <div class="btn btn-secondary w-100 pt-4 disabled" style="height: 100px; opacity: 0.35;" id="round3Btn" data-round="3">
                        <span class="text-light fs-2">ROUND 3</span>
                        <div><small class="text-light">(Pemasalan)</small></div>
                    </div>
                </div>
                @endif
            </div>
            {{-- end of round --}}

            {{-- Emit max_ronde to JS --}}
            <input type="hidden" id="max_ronde_timer" value="{{ $max_ronde ?? 3 }}">
            <input type="hidden" id="jenis_pertandingan_timer" value="{{ $jenis_pertandingan ?? 'prestasi' }}">
        @endif
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timerElement = document.getElementById("timer");
        const timeSelector = document.getElementById("timeSelector");
        const pertandinganIdInput = document.getElementById("pertandingan_id");
        
         if (!pertandinganIdInput || !pertandinganIdInput.value) {
            console.warn("Tidak ada pertandingan aktif. Timer dinonaktifkan.");
            if (timeSelector) timeSelector.disabled = true;
            if (document.getElementById("startBtn")) document.getElementById("startBtn").style.display = 'none';
            if (document.getElementById("pauseBtn")) document.getElementById("pauseBtn").style.display = 'none';
            if (document.getElementById("resetBtn")) document.getElementById("resetBtn").style.display = 'none';
            return; // Hentikan script
        }
    
        const pertandinganId = pertandinganIdInput.value;
        let totalSeconds = parseInt(timeSelector.value, 10);
        let currentSeconds = totalSeconds;
        let interval = null;
        
        // FUNGSI UTAMA UNTUK MENGIRIM STATUS KE SERVER
        async function sendTimerState(state, time, duration) {
            try {
                await fetch("{{ route('timer.broadcast') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pertandingan_id: pertandinganId,
                        state: state,
                        current_time: time,
                        total_duration: duration
                    })
                });
                console.log('✅ Timer state broadcasted:', state, time, duration);
            } catch (error) {
                console.error('❌ Gagal mengirim status timer:', error);
            }
        }
    
        function updateDisplay() {
            const minutes = Math.floor(currentSeconds / 60);
            const seconds = currentSeconds % 60;
            timerElement.textContent =
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    
        function startTimer() {
            if (timeSelector) timeSelector.disabled = true;
            if (interval) return;
            sendTimerState('playing', currentSeconds, totalSeconds);
            interval = setInterval(() => {
                if (currentSeconds > 0) {
                    currentSeconds--;
                    updateDisplay();
                } else {
                    clearInterval(interval);
                    interval = null;
                    if (timeSelector) timeSelector.disabled = false;
                    alert("Waktu selesai");
                    sendTimerState('reset', totalSeconds, totalSeconds);
                    currentSeconds = totalSeconds;
                    updateDisplay();
                }
            }, 1000);
        }
    
        function pauseTimer() {
            if (timeSelector) timeSelector.disabled = false;
            clearInterval(interval);
            interval = null;
            sendTimerState('paused', currentSeconds, totalSeconds);
        }
    
        function resetTimer() {
            pauseTimer();
            currentSeconds = totalSeconds;
            updateDisplay();
            sendTimerState('reset', currentSeconds, totalSeconds);
        }
    
        updateDisplay();
        sendTimerState('reset', currentSeconds, totalSeconds);
    
        if (timeSelector) {
            timeSelector.addEventListener('change', function() {
                pauseTimer();
                totalSeconds = parseInt(this.value, 10);
                currentSeconds = totalSeconds;
                updateDisplay();
                sendTimerState('reset', currentSeconds, totalSeconds);
            });
        }

        // Perbaikan Kritis 2: Periksa keberadaan tombol sebelum menambahkan listener
        const startBtn = document.getElementById("startBtn");
        const pauseBtn = document.getElementById("pauseBtn");
        const resetBtn = document.getElementById("resetBtn");

        if (startBtn) startBtn.addEventListener("click", startTimer);
        if (pauseBtn) pauseBtn.addEventListener("click", pauseTimer);
        if (resetBtn) resetBtn.addEventListener("click", resetTimer);

        // kode tambahan - Round management
        if (pertandinganIdInput && pertandinganIdInput.value) {
            const pertandinganId = pertandinganIdInput.value;
            const roundIndicator = document.getElementById('round-indicator');

            // Ambil semua tombol round
            const round1Btn = document.getElementById('round1Btn');
            const round2Btn = document.getElementById('round2Btn');
            const round3Btn = document.getElementById('round3Btn');
        
            // Fungsi untuk menangani pembaruan ronde
            async function handleRoundUpdate(roundNumber) {
                console.log(`Mengirim update untuk Ronde ${roundNumber}`);

                try {
                    const response = await fetch("{{ route('timer.updateRound') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            pertandingan_id: pertandinganId,
                            round_number: roundNumber,
                        })
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        console.log('✅', result.message);
                    
                        // Reset timer setelah ganti ronde
                        resetTimer();
                    
                        // Update tampilan indikator ronde
                        if (roundIndicator) {
                            roundIndicator.textContent = `ROUND ${result.current_round}`;
                        }

                        // Update UI tombol
                        if (roundNumber == 2) {
                            if (round1Btn) round1Btn.classList.add('disabled');
                        } else if (roundNumber == 3) {
                            if (round1Btn) round1Btn.classList.add('disabled');
                            if (round2Btn) round2Btn.classList.add('disabled');
                        }
                    } else {
                        console.error('❌ Gagal memperbarui ronde:', result.message);
                    }

                } catch (error) {
                    console.error('❌ Terjadi eror saat mengirim update ronde:', error);
                }
            }

            // Tambahkan event listener ke tombol Round 2 dan 3
            if (round2Btn) {
                round2Btn.addEventListener('click', () => {
                    if (!round2Btn.classList.contains('disabled')) {
                        handleRoundUpdate(2);
                    }
                });
            }
        
            if (round3Btn) {
                round3Btn.addEventListener('click', () => {
                    if (!round3Btn.classList.contains('disabled')) {
                         handleRoundUpdate(3);
                    }
                });
            }
        }
    });
</script>

@endsection
