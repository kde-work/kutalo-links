<?php

namespace App\Http\Controllers;

use App\Application\Link\DTO\ClickDataDto;
use App\Application\Link\Handler\RecordClickHandler;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __construct(
        private RecordClickHandler $recordClickHandler,
    ) {
    }

    public function redirect(Request $request, string $slug): RedirectResponse
    {
        try {
            $link = $this->recordClickHandler->handle($slug, new ClickDataDto(
                ip: $request->ip(),
                userAgent: $request->userAgent(),
                referer: $request->headers->get('referer'),
            ));
        } catch (ShortLinkNotFoundException) {
            abort(404);
        }

        return redirect()->away($link->getDestinationUrl(), 302);
    }
}
