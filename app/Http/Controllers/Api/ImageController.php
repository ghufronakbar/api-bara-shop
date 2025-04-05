<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Upload image to Cloudinary and return the URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // Validasi input gambar
        $validate = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Harap lengkapi data',
                'errors' => $validate->errors(),
            ], 400);
        }

        // Ambil file gambar yang diupload
        $image = $request->file('image');

        try {
            // Upload gambar ke Cloudinary
            $uploadedImage = Cloudinary::upload($image->getRealPath(), [
                'folder' => env('APP_NAME', 'BaraShop'),
            ]);

            // Mendapatkan URL gambar yang diupload
            $imageUrl = $uploadedImage->getSecurePath();

            // Mengembalikan URL gambar sebagai response
            return response()->json([
                'message' => 'Berhasil mengunggah gambar',
                'data' => $imageUrl,
            ], 200);
        } catch (\Exception $e) {
            // Jika ada error saat upload
            return response()->json([
                'message' => 'Gagal mengunggah gambar: ',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
