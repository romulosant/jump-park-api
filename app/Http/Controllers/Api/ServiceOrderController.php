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
     * Criar nova ordem de serviço
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vehiclePlate' => 'required|string|size:7|regex:/^[A-Z]{3}[0-9]{4}$/',
                'entryDateTime' => 'required|date_format:Y-m-d H:i:s|before_or_equal:now',
                'exitDateTime' => 'nullable|date_format:Y-m-d H:i:s|after:entryDateTime',
                'priceType' => 'nullable|string|max:55|in:hora,diaria,mensal,avulso',
                'price' => 'nullable|numeric|min:0|max:999999999.99',
                'userId' => 'required|integer|exists:users,id'
            ], [
                'vehiclePlate.required' => 'A placa do veículo é obrigatória.',
                'vehiclePlate.size' => 'A placa deve ter exatamente 7 caracteres.',
                'vehiclePlate.regex' => 'A placa deve seguir o formato ABC1234.',
                'entryDateTime.required' => 'A data/hora de entrada é obrigatória.',
                'entryDateTime.date_format' => 'A data/hora deve estar no formato Y-m-d H:i:s.',
                'entryDateTime.before_or_equal' => 'A data/hora de entrada não pode ser no futuro.',
                'exitDateTime.after' => 'A data/hora de saída deve ser posterior à entrada.',
                'priceType.in' => 'Tipo de preço deve ser: hora, diaria, mensal ou avulso.',
                'price.numeric' => 'O preço deve ser um valor numérico.',
                'price.min' => 'O preço não pode ser negativo.',
                'userId.required' => 'O ID do usuário é obrigatório.',
                'userId.exists' => 'O usuário especificado não existe.'
            ]);

            // Aplicar valores padrão se não informados
            if (!isset($validated['exitDateTime'])) {
                $validated['exitDateTime'] = '0001-01-01 00:00:00';
            }
            if (!isset($validated['price'])) {
                $validated['price'] = 0.00;
            }

            // Criar ordem de serviço
            $serviceOrder = ServiceOrder::create($validated);
            $serviceOrder->load('user:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Ordem de serviço criada com sucesso',
                'data' => $serviceOrder
            ], 200); // Código 200 conforme requisito

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
     * Listar todas as ordens de serviço
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
