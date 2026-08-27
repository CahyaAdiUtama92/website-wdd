<?php

use App\Models\Pengumuman;
use App\Models\StrukturOrganisasi;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Route::get('/', function () {
//     $pengumumans = Pengumuman::where('is_published', true)
//         ->orderBy('created_at', 'desc')
//         ->get();
    
//     $strukturs = StrukturOrganisasi::with(['user', 'jabatan'])
//         ->whereHas('periodeKepengurusan', function($query) {
//             $query->where('is_active', true);
//         })
//         ->get();
    
//     return view('welcome', compact('pengumumans', 'strukturs'));
// });

Route::get('/', function () {
    $pengumumans = Pengumuman::where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $strukturs = StrukturOrganisasi::with(['user', 'jabatan'])
        ->whereHas('periodeKepengurusan', function ($query) {
            $query->where('is_active', true);
        })
        ->get();
    
    return view('welcome', compact('pengumumans', 'strukturs'));
});

Route::get('/pengumuman/file/{filename}', function (string $filename) {
    $path = 'pengumuman/' . basename($filename);

    abort_unless(
        Storage::disk('public')->exists($path),
        404
    );

    return Storage::disk('public')->response($path);
})->name('pengumuman.file');


