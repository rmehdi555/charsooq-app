<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Articles\ArticlesCategoryRequest;
use App\Http\Requests\V1\Articles\ArticlesIndexRequest;
use App\Http\Resources\ArticleindexResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = Article::where('is_show',true)->with(['category', 'thumbnail', 'seo', 'tags', 'author'])->latest()
            ->paginate(config('custom.paginate_count'));
        $data = ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }

    public function show($slug): JsonResponse
    {
        $article = Article::whereSlug($slug)->where('is_show', true)
            ->with(['category', 'thumbnail', 'seo', 'tags', 'author'])->first();
        if ($article)
            $this->errorResponse(__('message.field_not_find'), 404);
        $article->increment('view_count');
        $data = new ArticleindexResource($article);
        return $this->successResponse($data, '');
    }

    public function future(ArticlesIndexRequest $request): JsonResponse
    {
        $articles = Article::where('is_future', 1)->latest()
            ->paginate($request->count);
        return $this->successResponse($articles, '');
    }

    public function mostView(ArticlesIndexRequest $request): JsonResponse
    {
        $articles = Article::orderBy('view_count')->with(['category', 'thumbnail', 'seo', 'tags', 'author'])
            ->paginate($request->count);
        $data = ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }
}
