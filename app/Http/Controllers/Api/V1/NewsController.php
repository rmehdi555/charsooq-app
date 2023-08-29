<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\News\NewsRequest;
use App\Models\News;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function add(NewsRequest $request): JsonResponse
    {
        $news = News::create([
            "email" => $request->email,
        ]);
        return $this->successResponse($news->id, __('messages.email_saved_successfully'));

    }
}
