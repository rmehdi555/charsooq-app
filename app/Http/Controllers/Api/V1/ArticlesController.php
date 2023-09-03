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
        $aricles = Article::latest()
            ->paginate(config('custom.paginate_count'));
        $data=ArticleindexResource::collection($aricles);
        return $this->successResponse($data, '');
    }

    public function category(): JsonResponse
    {
        $category = ArticleCategory::select('name')->get();
        return $this->successResponse($category, '');
    }

    public function categoryshow($slug): JsonResponse
    {
        $ariclecategory = ArticleCategory::select('id')->where('slug',$slug)->get();
        $aricles = Article::where('category_id',$ariclecategory[0]->id)->get();
        $data=ArticleindexResource::collection($aricles);
        return $this->successResponse($data, '');
    }
}
