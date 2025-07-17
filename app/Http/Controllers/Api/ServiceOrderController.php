<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ServiceOrderController extends Controller
{
    /**
     * Criar ordem de serviço
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vehiclePlate' => 'required|string|size:7|regex:/^[A-Z]{3}[0-9]{4}$/',
                'entryDateTime' => 'required|date_format:Y-m-d H:i:s',
                'exitDateTime' => 'nullable|date_format:Y-m-d H:i:s|after:entryDateTime',
                'priceType' => 'nullable|string|max:55',
                'price' => 'nullable|numeric|min:0|max:999999999.99',
                'userId' => 'required|integer|exists:users,id'
            ]);

            // Valores padrão
            if (!isset($validated['exitDateTime'])) {
                $validated['exitDateTime'] = '0001-01-01 00:00:00';
            }
            if (!isset($validated['price'])) {
                $validated['price'] = 0.00;
            }

            $serviceOrder = ServiceOrder::create($validated);
            $serviceOrder->load('user:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Ordem de serviço criada com sucesso',
                'data' => $serviceOrder
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Listar ordens de serviço
     */
    public function index(): JsonResponse
    {
        try {
            $serviceOrders = ServiceOrder::with('user:id,name')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'vehiclePlate' => $order->vehiclePlate,
                        'entryDateTime' => $order->entryDateTime->format('Y-m-d H:i:s'),
                        'exitDateTime' => $order->exitDateTime->format('Y-m-d H:i:s'),
                        'priceType' => $order->priceType,
                        'price' => $order->price,
                        'userId' => $order->userId,
                        'user_name' => $order->user->name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $serviceOrders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
