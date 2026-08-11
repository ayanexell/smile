<x-layouts::error code="419" :title="__('Sesi Telah Berakhir')" :message="__(
    'Halaman ini sudah kedaluwarsa karena sesi Anda habis waktunya. Silakan muat ulang halaman dan coba lagi.',
)">
    <div class="opacity-0-init animate-fade-up delay-600 mt-6">
        <button type="button" onclick="window.location.reload()"
            class="text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 text-sm font-medium underline underline-offset-4">
            {{ __('Muat Ulang Halaman') }} →
        </button>
    </div>
</x-layouts::error>
