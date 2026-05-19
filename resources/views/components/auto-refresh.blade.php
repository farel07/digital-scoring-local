@php
    $global_arena_id = '';
    if (Auth::check() && Auth::user()->arenas()->exists()) {
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
                        // Fetch the appropriate URL for the new active match
                        fetch('/api/active-match-url')
                            .then(response => response.json())
                            .then(data => {
                                if (data.url) {
                                    window.location.href = data.url;
                                }
                            })
                            .catch(err => {
                                console.error('Error fetching new match URL:', err);
                                window.location.reload();
                            });
                    }
                });
        }
    });
</script>
