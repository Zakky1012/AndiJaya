<?php

namespace App\Filament\Resources\KeberangkatanResource\Pages;

use App\Filament\Resources\KeberangkatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeberangkatan extends EditRecord
{
    protected static string $resource = KeberangkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave()
    {
        $this->record->generateSeats();
    }
}
