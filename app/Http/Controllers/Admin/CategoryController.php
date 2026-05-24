<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', [
            'categories' => Category::withCount('auctions')->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $attributes = $request->validate(['name' => ['required', 'string', 'max:80', 'unique:categories,name']]);

        Category::create([
            'name' => $attributes['name'],
            'slug' => Str::slug($attributes['name']),
        ]);

        return back()->with('success', 'Category created.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->auctions()->exists(), 422, 'Move auctions before deleting this category.');

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
