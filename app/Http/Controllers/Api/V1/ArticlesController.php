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
        $articles = Article::latest()
            ->paginate(config('custom.paginate_count'));
        $data=ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }

    public function category(): JsonResponse
    {
        $category = ArticleCategory::select('name')->get();
        return $this->successResponse($category, '');
    }

    public function categoryShow(ArticlesCategoryRequest $request): JsonResponse
    {
        $articlesCategory = ArticleCategory::select('id')->where('slug',$request->slug)->paginate($request->count);
        $articles = Article::where('category_id',$articlesCategory[0]->id)->get();
        $data=ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }

    public function show($slug): JsonResponse
    {
        $article = Article::whereSlug($slug)->get();
        $article->increment('view_count');
        $data=ArticleindexResource::collection($article);
        return $this->successResponse($data, '');
    }

    public function future(ArticlesIndexRequest $request): JsonResponse
    {
        $articles = Article::where('is_future',1)->latest()
            ->paginate($request->count);
        return $this->successResponse($articles, '');
    }

}
