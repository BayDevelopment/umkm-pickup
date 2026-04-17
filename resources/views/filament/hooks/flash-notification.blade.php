@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('filament:ready', function() {
                $wire.dispatch('notify', {
                    message: "{{ session('success') }}"
                });
            });
        });
    </script>
@endif
