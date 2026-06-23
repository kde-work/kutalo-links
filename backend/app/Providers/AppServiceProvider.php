<?php

namespace App\Providers;

use App\Domain\Link\LinkClickRepositoryInterface;
use App\Domain\Link\ShortLinkRepositoryInterface;
use App\Infrastructure\Link\Repository\EloquentLinkClickRepository;
use App\Infrastructure\Link\Repository\EloquentShortLinkRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ShortLinkRepositoryInterface::class, EloquentShortLinkRepository::class);
        $this->app->bind(LinkClickRepositoryInterface::class, EloquentLinkClickRepository::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
