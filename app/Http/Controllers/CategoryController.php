<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Organik,Anorganik',
            'unit' => 'required|in:kg,pcs', // Tambah validasi unit
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'unit' => $request->unit,
            'price_per_kg' => $request->price_per_kg,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori sampah berhasil ditambahkan!');
    }

    // Update data kategori
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Organik,Anorganik',
            'unit' => 'required|in:kg,pcs', // Tambah validasi unit
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'unit' => $request->unit,
            'price_per_kg' => $request->price_per_kg,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori sampah berhasil diperbarui!');
    }

    // Menghapus kategori
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori sampah berhasil dihapus!');
    }
}