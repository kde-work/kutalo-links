<?php

namespace App\Infrastructure\Link\Repository;

use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;
use App\Infrastructure\Link\Eloquent\ShortLinkModel;
use App\Infrastructure\Link\Mapper\ShortLinkMapper;
use Illuminate\Support\Facades\DB;

class EloquentShortLinkRepository implements ShortLinkRepositoryInterface
{
    public function __construct(
        private ShortLinkMapper $mapper,
    ) {
    }

    public function findById(int $id): ?ShortLink
    {
        $model = ShortLinkModel::query()->withCount('clicks')->find($id);

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model, (int) $model->clicks_count);
    }

    public function findBySlug(string $slug): ?ShortLink
    {
        $model = ShortLinkModel::query()->withCount('clicks')->where('slug', $slug)->first();

        if ($model === null) {
            return null;
        }

        return $this->mapper->toDomain($model, (int) $model->clicks_count);
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = ShortLinkModel::query()->where('slug', $slug);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function findAllByUserId(int $userId): array
    {
        return ShortLinkModel::query()
            ->withCount('clicks')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (ShortLinkModel $model) => $this->mapper->toDomain($model, (int) $model->clicks_count))
            ->all();
    }

    public function save(ShortLink $link): ShortLink
    {
        if ($link->getId() === null) {
            $model = new ShortLinkModel();
        } else {
            $model = ShortLinkModel::query()->findOrFail($link->getId());
        }

        $this->mapper->fillModel($link, $model);
        $model->save();
        $model->loadCount('clicks');

        return $this->mapper->toDomain($model, (int) $model->clicks_count);
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            ShortLinkModel::query()->whereKey($id)->delete();
        });
    }
}
