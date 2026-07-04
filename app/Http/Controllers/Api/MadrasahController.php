<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use Illuminate\Http\JsonResponse;

class MadrasahController extends Controller
{
    public function index(): JsonResponse
    {
        try {

            $madrasah = Madrasah::get();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Data madrasah berhasil diambil.',
                'data' => $madrasah
            ],200);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Internal Server Error.',
                'error' => $e->getMessage()
            ],500);

        }
    }

    public function show($id): JsonResponse
    {
        try {

            $madrasah = Madrasah::find($id);

            if(!$madrasah){

                return response()->json([
                    'success' => false,
                    'code' => 404,
                    'message' => 'Data madrasah tidak ditemukan.',
                ],404);

            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Detail madrasah berhasil diambil.',
                'data' => $madrasah
            ],200);

        } catch (\Throwable $e){

            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Internal Server Error.',
                'error' => $e->getMessage()
            ],500);

        }
    }
}