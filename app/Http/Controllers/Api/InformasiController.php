<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformasiController extends Controller
{
    public function index()
    {
        $informasi = Informasi::first();
        return response()->json([
            'message' => 'OK',
            'data' => $informasi
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pajak' => 'required|numeric',
            'diskon' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $informasi = Informasi::first();

        if (!$informasi) {
            Informasi::create($validated);
        } else {
            $informasi->update($validated);
        }

        return response()->json([
            'message' => 'Berhasil mengedit informasi',
            'data' => $informasi
        ]);
    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
