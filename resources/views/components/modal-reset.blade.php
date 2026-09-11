@props([
    'modal_name' => '',
    'action_reset' => '',
    'title' => '',
    'description' => '',
])

{{-- Modal Reset --}}
<div x-data="{
    show: false,
    id: null,
    init() {
        window.addEventListener('{{ $modal_name }}', (e) => {
            this.id = e.detail.id;
            this.show = true;
        });
    },
    reset() {
        $wire.{{ $action_reset }}(this.id)
            .then(() => { this.show = false; });
    }
}" x-show="show" x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm dark:bg-black/60"
    x-cloak>
    <div class="w-full max-w-xs rounded-xl border border-stone-200 bg-white p-5 shadow-2xl dark:border-stone-800 dark:bg-stone-900"
        @click.outside="show = false">
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50 dark:bg-amber-950">
                <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="mb-0.5 text-sm font-semibold text-stone-800 dark:text-stone-100">{{ $title }}
                </h3>
                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $description }}</p>
            </div>
        </div>
        <div class="mt-5 flex gap-2">
            <button @click="show = false"
                class="flex-1 cursor-pointer rounded-lg border border-stone-200 px-3 py-2 text-xs font-medium text-stone-700 transition-colors hover:bg-stone-50 dark:border-stone-700 dark:text-stone-300 dark:hover:bg-stone-800">
                Batal
            </button>
            <button @click="reset()"
                class="flex-1 cursor-pointer rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-amber-600">
                <span wire:loading.remove wire:target="{{ $action_reset }}">
                    Ya, Reset
                </span>
                <span wire:loading wire:target="{{ $action_reset }}">
                    Loading...
                </span>
            </button>
        </div>
    </div>
</div>
