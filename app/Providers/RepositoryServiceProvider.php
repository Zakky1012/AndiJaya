<?php

namespace App\Providers;

use App\interfaces\DestinasiRepositoryInterface;
use App\interfaces\KeberangkatanRepositoryInterface;
use App\interfaces\MobilRepositoryInterface;
use App\interfaces\TransactionRepositoryInterface;
use App\Repositories\DestinasiRepository;
use App\Repositories\KeberangkatanRepository;
use App\Repositories\MobilRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(MobilRepositoryInterface::class, MobilRepository::class);
        $this->app->bind(DestinasiRepositoryInterface::class, DestinasiRepository::class);
        $this->app->bind(KeberangkatanRepositoryInterface::class, KeberangkatanRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
