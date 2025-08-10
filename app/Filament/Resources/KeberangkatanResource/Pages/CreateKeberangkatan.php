<?php

namespace App\Filament\Resources\KeberangkatanResource\Pages;

use App\Filament\Resources\KeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKeberangkatan extends CreateRecord
{
    protected static string $resource = KeberangkatanResource::class;

    protected function afterCreate(): void
    {
        $this->record->generateSeats();
    }
}
