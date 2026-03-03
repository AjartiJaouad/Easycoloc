<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','active.colocation.owner']);
    }

    protected function colocation()
    {
        return auth()->user()->activeColocation();
    }

    public function index()
    {
        $cats = $this->colocation()->categories()->orderBy('name')->get();
        return view('categories.index', compact('cats'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->colocation()->categories()->create($req->only('name'));

        return redirect()->route('categories.index')
                         ->with('success','Catégorie ajoutée');
    }

    public function edit(Category $category)
    {
        $this->authorize('manage',$category); // policy si tu en as
        return view('categories.edit', compact('category'));
    }

    public function update(Request $req, Category $category)
    {
        $this->authorize('manage',$category);
        $req->validate(['name'=>'required|string|max:255']);
        $category->update($req->only('name'));
        return redirect()->route('categories.index')
                         ->with('success','Catégorie modifiée');
    }

    public function destroy(Category $category)
    {
        $this->authorize('manage',$category);
        $category->delete();
        return back()->with('success','Catégorie supprimée');
    }
}
