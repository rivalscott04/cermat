<?php

namespace App\Http\Controllers;

use App\Models\KategoriSoal;
use Illuminate\Http\Request;

class KategoriSoalController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSoal::withCount('soals')->paginate(20);
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:kategori_soal,kode',
            'deskripsi' => 'nullable|string'
        ]);

        KategoriSoal::create([
            'nama' => $request->nama,
            'kode' => strtoupper($request->kode),
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(KategoriSoal $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriSoal $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:kategori_soal,kode,' . $kategori->id,
            'deskripsi' => 'nullable|string'
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'kode' => strtoupper($request->kode),
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(KategoriSoal $kategori)
    {
        if ($kategori->soals()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki soal');
        }

        $kategori->delete();
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus');
    }

    public function toggleStatus(KategoriSoal $kategori)
    {
        $kategori->update(['is_active' => !$kategori->is_active]);
        
        $status = $kategori->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori berhasil {$status}");
    }
} 