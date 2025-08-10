<?php

namespace App\interfaces;

interface TransactionRepositoryInterface
{
    public function getTransactionDataFromSession();

    public function saveTransactionDataToSession($data);

    public function saveTransaction($data);

    public function getTransactionByCode($kode);

    public function getTransactionByCodePhone($kode, $nomor);
}
