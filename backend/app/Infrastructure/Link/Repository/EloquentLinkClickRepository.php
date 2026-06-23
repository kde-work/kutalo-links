<?php

namespace App\Infrastructure\Link\Repository;

use App\Domain\Link\LinkClick;
use App\Domain\Link\LinkClickRepositoryInterface;
use App\Infrastructure\Link\Eloquent\LinkClickModel;
use App\Infrastructure\Link\Mapper\LinkClickMapper;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class EloquentLinkClickRepository implements LinkClickRepositoryInterface
{
    public function __construct(
        private LinkClickMapper $mapper,
    ) {
    }

    public function save(LinkClick $click): LinkClick
    {
        $model = new LinkClickModel();
        $this->mapper->fillModel($click, $model);
        $model->save();

        return $this->mapper->toDomain($model);
    }

    public function countByShortLinkId(int $shortLinkId): int
    {
        return LinkClickModel::query()->where('short_link_id', $shortLinkId)->count();
    }

    public function countByShortLinkIdSince(int $shortLinkId, DateTimeImmutable $since): int
    {
        return LinkClickModel::query()
            ->where('short_link_id', $shortLinkId)
            ->where('clicked_at', '>=', $since)
            ->count();
    }

    public function countByDay(int $shortLinkId, int $days): array
    {
        $since = new DateTimeImmutable("-{$days} days");

        $rows = LinkClickModel::query()
            ->selectRaw('DATE(clicked_at) as click_date, COUNT(*) as click_count')
            ->where('short_link_id', $shortLinkId)
            ->where('clicked_at', '>=', $since)
            ->groupBy('click_date')
            ->orderBy('click_date')
            ->get();

        return $rows->map(static fn ($row) => [
            'date' => (string) $row->click_date,
            'count' => (int) $row->click_count,
        ])->all();
    }

    public function findRecentByShortLinkId(int $shortLinkId, int $limit): array
    {
        return LinkClickModel::query()
            ->where('short_link_id', $shortLinkId)
            ->orderByDesc('clicked_at')
            ->limit($limit)
            ->get()
            ->map(fn (LinkClickModel $model) => $this->mapper->toDomain($model))
            ->all();
    }
}
