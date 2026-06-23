<?php

namespace App\Http\Controllers\Api;

use App\Application\Link\DTO\CreateShortLinkDto;
use App\Application\Link\Handler\CreateShortLinkHandler;
use App\Application\Link\Handler\DeleteShortLinkHandler;
use App\Application\Link\Handler\GetShortLinkHandler;
use App\Application\Link\Handler\ListShortLinksHandler;
use App\Application\Link\Handler\UpdateShortLinkDestinationHandler;
use App\Application\Link\Handler\UpdateShortLinkHandler;
use App\Domain\Link\Exception\InvalidDestinationUrlException;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\Exception\SlugAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Requests\UpdateDestinationRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Http\Resources\ShortLinkResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShortLinkController extends Controller
{
    public function __construct(
        private ListShortLinksHandler $listHandler,
        private CreateShortLinkHandler $createHandler,
        private GetShortLinkHandler $getHandler,
        private UpdateShortLinkHandler $updateHandler,
        private UpdateShortLinkDestinationHandler $updateDestinationHandler,
        private DeleteShortLinkHandler $deleteHandler,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $links = $this->listHandler->handle((int) $request->user()->id);

        return ShortLinkResource::collection($links);
    }

    public function store(StoreShortLinkRequest $request): JsonResponse
    {
        try {
            $link = $this->createHandler->handle(new CreateShortLinkDto(
                userId: (int) $request->user()->id,
                slug: $request->validated('slug'),
                destinationUrl: $request->validated('destination_url'),
                title: $request->validated('title'),
            ));
        } catch (SlugAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (InvalidDestinationUrlException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ShortLinkResource($link))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $link = $this->getHandler->handle($id, (int) $request->user()->id);
        } catch (ShortLinkNotFoundException) {
            return response()->json(['message' => 'Короткая ссылка не найдена'], 404);
        }

        return (new ShortLinkResource($link))->response();
    }

    public function update(UpdateShortLinkRequest $request, int $id): JsonResponse
    {
        try {
            $link = $this->updateHandler->handle(
                $id,
                (int) $request->user()->id,
                $request->validated('title'),
                $request->validated('is_active'),
            );
        } catch (ShortLinkNotFoundException) {
            return response()->json(['message' => 'Короткая ссылка не найдена'], 404);
        }

        return (new ShortLinkResource($link))->response();
    }

    public function updateDestination(UpdateDestinationRequest $request, int $id): JsonResponse
    {
        try {
            $link = $this->updateDestinationHandler->handle(
                $id,
                (int) $request->user()->id,
                $request->validated('destination_url'),
            );
        } catch (ShortLinkNotFoundException) {
            return response()->json(['message' => 'Короткая ссылка не найдена'], 404);
        } catch (InvalidDestinationUrlException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ShortLinkResource($link))->response();
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->deleteHandler->handle($id, (int) $request->user()->id);
        } catch (ShortLinkNotFoundException) {
            return response()->json(['message' => 'Короткая ссылка не найдена'], 404);
        }

        return response()->json(null, 204);
    }
}
