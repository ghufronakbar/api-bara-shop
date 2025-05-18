<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InformasiController extends Controller
{


    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

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
            Informasi::create(
                [
                    'persentase_pajak' => $validated['pajak'],
                    'persentase_diskon' => $validated['diskon'],
                ]
            );
        } else {
            $informasi->update(
                [
                    'persentase_pajak' => $validated['pajak'],
                    'persentase_diskon' => $validated['diskon'],
                ]
            );
        }

        $this->logService->saveToLog($request, 'Informasi', $informasi->toArray());

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
