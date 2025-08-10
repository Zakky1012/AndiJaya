<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Keberangkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_keberangkatan',
        'mobil_id',
    ];

    public function Mobil(){
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function segments(){
        return $this->hasMany(SegmentKeberangkatan::class);
    }

    public function classes(){
        return $this->hasMany(ClassKeberangkatan::class);
    }

    public function seats(){
        return $this->hasMany(KursiKeberangkatan::class);
    }

    public function transactions(){
        return $this->hasMany(Transaksi::class);
    }

    public function generateSeats(){
        Log::info("--- Memulai generateSeats() untuk keberangkatan ID: " . $this->id);

        $this->seats()->delete();
        Log::info("Kursi lama telah dihapus.");

        $classes = $this->classes;

        if ($classes->isEmpty()) {
            Log::warning("Tidak ada data ClassKeberangkatan yang ditemukan.");
            return;
        }

        foreach ($classes as $class) {
            Log::info("Memproses kelas: " . $class->tipe_kelas . " dengan total kursi: " . $class->total_kursi);

            $totalSeats = $class->total_kursi;
            $seatsPerRow = $this->getSeatsPerRow($class->tipe_kelas);
            $rows = ceil($totalSeats / $seatsPerRow);
            $seatCounter = 1;

            for ($row = 1; $row <= $rows; $row++) {
                for ($column = 1; $column <= $seatsPerRow; $column++) {
                    if ($seatCounter > $totalSeats) {
                        break 2;
                    }

                    $seatCode = $this->generateSeatCode($row, $column);

                    KursiKeberangkatan::create([
                        'keberangkatan_id'  => $this->id,
                        'name'              => $seatCode,
                        'row'               => $row,
                        'column'            => $column,
                        'is_available'      => true,
                        'tipe_kelas'        => $class->tipe_kelas,
                    ]);

                    $seatCounter++;
                }
            }
        }
        Log::info("Pembuatan kursi selesai untuk keberangkatan ID: " . $this->id);
        Log::info("--- Selesai generateSeats()");
    }

    protected function getSeatsPerRow($classType){
        switch ($classType) {
            case 'ekonomi':
                return 4;
            case 'premium':
                return 6;
            default:
                return 4;
        }
    }

    private function generateSeatCode($row, $column) {
        $rowLetter = chr(64 + $row);

        return $rowLetter . $column;
    }
}
