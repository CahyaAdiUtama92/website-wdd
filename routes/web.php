<?php

use App\Models\Pengumuman;
use App\Models\StrukturOrganisasi;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $pengumumans = Pengumuman::where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $strukturs = StrukturOrganisasi::with(['user', 'jabatan'])
        ->whereHas('periodeKepengurusan', function($query) {
            $query->where('is_active', true);
        })
        ->get();
    
    return view('welcome', compact('pengumumans', 'strukturs'));
});


