<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Articles\ArticlesCategoryRequest;
use App\Http\Resources\ArticleindexResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticlesCatergoryController extends Controller
{
    public function index(): JsonResponse
    {
        $category = ArticleCategory::select('name')->get();
        return $this->successResponse($category, '');
    }

    public function show(ArticlesCategoryRequest $request): JsonResponse
    {
        $articlesCategory = ArticleCategory::select('id')->where('slug', $request->slug)->paginate($request->count);
        $articles = Article::where('category_id', $articlesCategory[0]->id)->get();
        $data = ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }
}
