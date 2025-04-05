<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsAppController;
use App\Mail\EmailKirimPesan;
use App\Models\Pelanggan;
use App\Models\PesanTerkirim;
use App\Services\LogService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PesanController extends Controller
{

    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }


    public function index()
    {
        $pesanTerkirim = PesanTerkirim::withCount([])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $pesanTerkirim
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subjek' => 'required|string',
            'pesan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7); // potong "Bearer "

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            if (!$decoded->id) {
                return response()->json(['message' => 'Unauthorized', 'errors' => 'Unauthorized'], 401);
            }

            $pelanggans = Pelanggan::where('is_deleted', false)->get();

            foreach ($pelanggans as $pelanggan) {
                if ($pelanggan->jenis_kode == "Email") {
                    $data = [
                        'subjek' => $validated['subjek'],
                        'body_text' => $validated['pesan'],
                        'nama' => $pelanggan->nama
                    ];
                    Mail::to($pelanggan->kode)->queue(new EmailKirimPesan($data));
                }
            }

            $pelangganWithPhone = $pelanggans->filter(function ($pelanggan) {
                return $pelanggan->jenis_kode == 'Phone';
            });

            $phones = $pelangganWithPhone->pluck('kode')->toArray();
            $names = $pelangganWithPhone->pluck('nama')->toArray();


            $whatsappController = new WhatsAppController();
            $whatsappController->sendBulkWhatsapp($phones, $names, $validated['subjek'], $validated['pesan']);

            $pesanTerkirim = PesanTerkirim::create([
                'subjek' => $validated['subjek'],
                'pesan' => $validated['pesan'],
                'user_id' => $decoded->id
            ]);

            // Daftar ID pelanggan yang ingin dikaitkan dengan pesan terkirim
            $pelangganIds = $pelanggans->pluck('id')->toArray();

            // Mengaitkan pelanggan dengan pesan terkirim
            $pesanTerkirim->pelanggan()->attach($pelangganIds);

            $this->logService->saveToLog($request, 'PesanTerkirim', $pesanTerkirim->toArray());

            return response()->json([
                'message' => 'Berhasil mengirimkan pesan',
                'data' => $pesanTerkirim
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized', 'error' => $e->getMessage()], 401);
        }
    }


    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Id tidak valid',
                'errors' => $validator->errors(),
            ], 400);
        }

        $pesanTerkirim = PesanTerkirim::where('id', $id)->first();

        if (!$pesanTerkirim || $pesanTerkirim->is_deleted) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $pesanTerkirim
        ]);
    }

    public function update(Request $request, string $id) {}

    public function destroy(string $id, Request $request) {}
}
