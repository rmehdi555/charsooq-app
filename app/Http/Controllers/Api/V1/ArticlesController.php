<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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

    public function categoryshow($slug,$count): JsonResponse
    {
        $articlesCategory = ArticleCategory::select('id')->where('slug',$slug)->get()->paginate($count);
        $articles = Article::where('category_id',$articlesCategory[0]->id)->get();
        $data=ArticleindexResource::collection($articles);
        return $this->successResponse($data, '');
    }

    public function show($slug): JsonResponse
    {
        $articles = Article::where('slug',$slug)->get();
        $article = Article::where('slug',$slug)
            ->update(["view_count" => $articles[0]->view_count+1]);
        return $this->successResponse($articles, '');
    }

    public function future($count): JsonResponse
    {
        $articles = Article::where('is_future',1)->latest()
            ->paginate($count);
        return $this->successResponse($articles, '');
    }

}
