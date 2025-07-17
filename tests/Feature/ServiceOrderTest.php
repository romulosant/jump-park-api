<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste: Criar ordem de serviço com sucesso (código 200)
     */
    public function test_should_create_service_order_successfully(): void
    {
        // Criar usuário para o teste
        $user = User::create(['name' => 'João Silva']);

        // Dados válidos para criação
        $orderData = [
            'vehiclePlate' => 'ABC1234',
            'entryDateTime' => '2024-01-15 10:30:00',
            'exitDateTime' => '2024-01-15 18:45:00',
            'priceType' => 'hora',
            'price' => 25.50,
            'userId' => $user->id
        ];

        // Fazer requisição POST
        $response = $this->postJson('/api/v1/service-orders', $orderData);

        // Verificar resposta (código 200 conforme requisito)
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'vehiclePlate',
                    'entryDateTime',
                    'exitDateTime',
                    'priceType',
                    'price',
                    'userId',
                    'user' => [
                        'id',
                        'name'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Ordem de serviço criada com sucesso'
            ]);

        // Verificar se foi salvo no banco
        $this->assertDatabaseHas('service_orders', [
            'vehiclePlate' => 'ABC1234',
            'userId' => $user->id,
            'priceType' => 'hora',
            'price' => 25.50
        ]);
    }

    /**
     * Teste: Falhar com dados inválidos (código 422)
     */
    public function test_should_fail_with_invalid_data(): void
    {
        // Dados inválidos
        $invalidData = [
            'vehiclePlate' => 'INVALID_PLATE_TOO_LONG', // Muito longa
            'entryDateTime' => 'invalid-date',           // Data inválida
            'userId' => 999                              // Usuário inexistente
        ];

        // Fazer requisição POST
        $response = $this->postJson('/api/v1/service-orders', $invalidData);

        // Verificar erro de validação (código 422)
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Erro de validação'
            ]);
    }

    /**
     * Teste: Listar ordens de serviço com user_name
     */
    public function test_should_list_service_orders(): void
    {
        // Criar usuário
        $user = User::create(['name' => 'João Silva']);
        
        // Criar ordem de serviço
        ServiceOrder::create([
            'vehiclePlate' => 'ABC1234',
            'entryDateTime' => '2024-01-15 10:30:00',
            'exitDateTime' => '0001-01-01 00:00:00',
            'priceType' => 'hora',
            'price' => 25.50,
            'userId' => $user->id
        ]);

        // Fazer requisição GET
        $response = $this->getJson('/api/v1/service-orders');

        // Verificar listagem
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'vehiclePlate',
                        'entryDateTime',
                        'exitDateTime',
                        'priceType',
                        'price',
                        'userId',
                        'user_name'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);

        // Verificar se user_name está presente
        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData);
        $this->assertEquals('João Silva', $responseData[0]['user_name']);
    }
}
