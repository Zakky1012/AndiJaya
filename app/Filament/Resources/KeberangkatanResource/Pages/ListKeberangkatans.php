<?php

namespace App\Filament\Resources\KeberangkatanResource\Pages;

use App\Filament\Resources\KeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeberangkatans extends ListRecords
{
    protected static string $resource = KeberangkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
