<?php

// app/Livewire/CheckPromoCode.php

namespace App\Livewire;

use App\Models\PromoCode;
use Livewire\Component;

class CheckPromoCode extends Component
{
    // Properti ini sesuai dengan nama di view
    public $promo_code;
    public $diskon = 0;
    public $tipe_diskon;
    public $isValid = false;

    // Livewire akan secara otomatis memanggil method updatedPromoCode()
    // saat input berubah (karena wire:model.live). Metode ini akan memanggil checkPromoCode.
    public function updatedPromoCode()
    {
        $this->checkPromoCode();
    }

    public function checkPromoCode()
    {
        $promo = $this->findPromoCode(trim($this->promo_code));

        if ($promo) {
            $this->applyPromoCode($promo);
        } else {
            $this->invalidatePromoCode();
        }

        $this->dispatchPromoCodeUpdate();
    }

    private function findPromoCode($promoCode)
    {
        return PromoCode::where('kode', $promoCode)
            ->where('valid', '>=', now())
            ->where('is_used', false)
            ->first();
    }

    private function applyPromoCode($promo)
    {
        $this->isValid = true;
        $this->diskon = $promo->diskon ?? 0;
        $this->tipe_diskon = $promo->tipe_diskon;
    }

    private function invalidatePromoCode()
    {
        $this->isValid = false;
        $this->diskon = 0;
        $this->tipe_diskon = null;
    }

    private function dispatchPromoCodeUpdate()
    {
        $this->dispatch('promoCodeUpdated', [
            'promo_code'    => $this->promo_code,
            'diskon'        => $this->diskon,
            'tipe_diskon'   => $this->tipe_diskon
        ]);
    }

    public function render()
    {
        return view('livewire.check-promo-code');
    }
}
