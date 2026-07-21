<x-layouts::app.sidebar :title="$title ?? null">
    <main class="m-4">
        {{ $slot }}
    </main>
</x-layouts::app.sidebar>
