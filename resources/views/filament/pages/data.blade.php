<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getHeaderWidgets()"
        :columns="$this->getHeaderWidgetsColumns()"
    />

    <x-filament-widgets::widgets
        :widgets="$this->getFooterWidgets()"
        :columns="$this->getFooterWidgetsColumns()"
    />
</x-filament-panels::page>