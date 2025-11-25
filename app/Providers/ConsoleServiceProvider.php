<?php

namespace App\Providers;

use App\Core\Console\Kernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsoleKernelContract::class, Kernel::class);
    }
}
