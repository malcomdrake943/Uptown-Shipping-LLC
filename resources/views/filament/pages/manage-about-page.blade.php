<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 text-left">
            <x-filament::button type="submit">
                Save About Us Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
