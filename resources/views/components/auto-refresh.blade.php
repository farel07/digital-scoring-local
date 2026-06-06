@php
    $global_arena_id = $arena_id ?? '';
    if (!$global_arena_id && isset($pertandingan) && $pertandingan instanceof \App\Models\Pertandingan) {
        $global_arena_id = $pertandingan->arena_id;
    }
    if (!$global_arena_id && Auth::check() && Auth::user()->arenas()->exists()) {
        $global_arena_id = Auth::user()->arenas()->first()->id;
    }
@endphp
<meta name="arena-id" content="{{ $global_arena_id }}">

{{-- Load Vite assets for Echo/Pusher initialization if not already loaded --}}
@vite(['resources/js/app.js', 'resources/css/app.css'])

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const arenaId = document.querySelector('meta[name="arena-id"]').getAttribute('content');
        if (arenaId && window.Echo) {
            window.Echo.channel('arena.' + arenaId)
                .listen('MatchStatusChanged', (e) => {
                    console.log('Match Status Changed in this arena!', e);
                    if (e.status === 'berlangsung') {
                        // If it's a public penonton page, use the public redirect endpoint
                        const isPenontonPage = location.pathname.startsWith('/penonton-');
                        const urlEndpoint = isPenontonPage 
                            ? '/api/active-match-url-public/' + arenaId
                            : '/api/active-match-url';

                        fetch(urlEndpoint)
                            .then(response => response.json())
                            .then(data => {
                                if (data.url && data.url !== '/waiting-match' && data.url !== '/') {
                                    window.location.href = data.url;
                                }
                            })
                            .catch(err => {
                                console.error('Error fetching new match URL:', err);
                                window.location.reload();
                            });
                    } else if (e.status === 'selesai') {
                        if (location.pathname.startsWith('/operator/dashboard')) {
                            window.location.reload();
                        } else if (location.pathname.startsWith('/penilaian/') || location.pathname.startsWith('/dewan-operator-')) {
                            window.location.href = '/operator/dashboard';
                        } else if (location.pathname.startsWith('/penonton-')) {
                            // Fetch final results to show winner overlay
                            console.log('Match completed. Fetching final results to show winner.');
                            if (typeof fetchMatchInfo === 'function') fetchMatchInfo();
                            if (typeof fetchMatchData === 'function') fetchMatchData();
                            if (typeof fetchEvents === 'function') fetchEvents();
                        } else if (location.pathname !== '/waiting-match' && location.pathname !== '/login' && location.pathname !== '/') {
                            window.location.href = '/waiting-match';
                        }
                    }
                });
        }
    });
</script>

