# 🚀 Jump Park API - Sistema de Gerenciamento de Ordens de Serviço

## 📋 Sobre o Projeto

API REST robusta para gerenciamento de ordens de serviço de estacionamento desenvolvida com Laravel PHP, MySQL e testes automatizados com Pest PHP.

# Como Clonar o Repositório Jump Park API

## 📋 Pré-requisitos

Antes de clonar o repositório, certifique-se de ter:

- **Git** instalado no seu computador
- **PHP 8.2+** instalado
- **Composer** instalado
- **MySQL** ou XAMPP rodando

## 🚀 Passos para Clonar

### 1. Abrir Terminal/Prompt de Comando

```bash
# Windows: Abrir CMD ou PowerShell
# Mac/Linux: Abrir Terminal
```

### 2. Navegar para o Diretório Desejado

```bash
# Exemplo: Navegar para Desktop
cd Desktop

# Ou para uma pasta específica
cd C:\projetos
```

### 3. Clonar o Repositório

```bash
# Clonar usando HTTPS
git clone https://github.com/SEU-USUARIO/jump-park-api.git

# Ou usando SSH (se configurado)
git clone git@github.com:SEU-USUARIO/jump-park-api.git
```

### 4. Entrar no Diretório do Projeto

```bash
cd jump-park-api
```

### 5. Verificar se os Arquivos foram Clonados

```bash
# Listar arquivos
ls -la     # Mac/Linux
dir        # Windows

# Você deve ver:
# - app/
# - database/
# - tests/
# - composer.json
# - .env.example
# - README.md
```

## 📂 Estrutura após Clonar

```
jump-park-api/
├── app/
│   ├── Http/Controllers/Api/
│   └── Models/
├── database/
│   ├── factories/
│   ├── migrations/
├── tests/
│   └── Feature/
├── routes/
├── composer.json
├── .env.example
├── phpunit.xml
└── README.md
```

## 🔧 Próximos Passos após Clonar

### 1. Instalar Dependências

```bash
composer install
```

### 2. Configurar Ambiente

```bash
# Copiar arquivo de ambiente
cp .env.example .env        # Mac/Linux
copy .env.example .env      # Windows

# Gerar chave da aplicação
php artisan key:generate
```

### 3. Configurar Banco de Dados

```bash
# Editar arquivo .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jump_park
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Executar Migrations

```bash
php artisan migrate
```

### 5. Iniciar Servidor

```bash
php artisan serve
```

## ⚠️ Possíveis Problemas

### Erro: "git não é reconhecido"
```bash
# Instalar Git primeiro
# Download: https://git-scm.com/download
```

### Erro: "repositório não encontrado"
```bash
# Verificar se o repositório é público
# Verificar se a URL está correta
```

### Erro: "permissão negada"
```bash
# Configurar SSH keys se usando SSH
# Ou usar HTTPS ao invés de SSH
```

## 🎯 Comandos Resumidos

```bash
# Sequência completa
git clone https://github.com/SEU-USUARIO/jump-park-api.git
cd jump-park-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Após seguir estes passos, o projeto estará rodando em `http://localhost:8000` e pronto para uso!




# 🚀 Passo a Passo de Construção do Projeto Jump Park API

### **Etapa 1: Configuração Inicial**
```bash
# Criar novo projeto Laravel
composer create-project laravel/laravel jump-park-api
cd jump-park-api

# Instalar Pest PHP para testes
composer require pestphp/pest --dev --with-all-dependencies
./vendor/bin/pest --init

# Configurar ambiente (.env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jump_park
DB_USERNAME=root
DB_PASSWORD=
```

### **Etapa 2: Estrutura do Banco de Dados**
```bash
# Modificar migration users existente
# Arquivo: database/migrations/xxxx_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
});

# Criar migration para service_orders
php artisan make:migration create_service_orders_table
```

**Estrutura da tabela service_orders:**
```php
Schema::create('service_orders', function (Blueprint $table) {
    $table->id();
    $table->char('vehiclePlate', 7);
    $table->dateTime('entryDateTime');
    $table->dateTime('exitDateTime')->default('0001-01-01 00:00:00');
    $table->string('priceType', 55)->nullable();
    $table->decimal('price', 12, 2)->default(0.00);
    $table->unsignedBigInteger('userId');
    $table->foreign('userId')->references('id')->on('users');
});
```

### **Etapa 3: Criação dos Models**
```bash
# Criar/modificar Model User
php artisan make:model User
```

**Model User:**
```php
class User extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = ['name'];
    
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'userId');
    }
}
```

**Model ServiceOrder:**
```php
class ServiceOrder extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'vehiclePlate', 'entryDateTime', 'exitDateTime',
        'priceType', 'price', 'userId'
    ];
    
    protected $casts = [
        'entryDateTime' => 'datetime',
        'exitDateTime' => 'datetime',
        'price' => 'decimal:2',
        'userId' => 'integer'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
```

### **Etapa 4: Criar Controller**
```bash
php artisan make:controller Api/ServiceOrderController
```

**Controller com validações:**
```php
class ServiceOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehiclePlate' => 'required|string|size:7|regex:/^[A-Z]{3}[0-9]{4}$/',
            'entryDateTime' => 'required|date_format:Y-m-d H:i:s|before_or_equal:now',
            'exitDateTime' => 'nullable|date_format:Y-m-d H:i:s|after:entryDateTime',
            'priceType' => 'nullable|string|max:55|in:hora,diaria,mensal,avulso',
            'price' => 'nullable|numeric|min:0|max:999999999.99',
            'userId' => 'required|integer|exists:users,id'
        ]);
        
        // Aplicar valores padrão
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
        ], 200);
    }
    
    public function index(): JsonResponse
    {
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
    }
}
```

### **Etapa 5: Configurar Rotas**
```php
// Arquivo: routes/api.php
Route::prefix('v1')->group(function () {
    Route::post('/service-orders', [ServiceOrderController::class, 'store']);
    Route::get('/service-orders', [ServiceOrderController::class, 'index']);
});
```

### **Etapa 6: Criar Factories**
```bash
# UserFactory
php artisan make:factory UserFactory
```

**UserFactory:**
```php
public function definition(): array
{
    return [
        'name' => $this->faker->name(),
    ];
}
```

**ServiceOrderFactory:**
```php
public function definition()
{
    return [
        'vehiclePlate' => strtoupper($this->faker->regexify('[A-Z]{3}[0-9]{4}')),
        'entryDateTime' => $this->faker->dateTime(),
        'exitDateTime' => '0001-01-01 00:00:00',
        'priceType' => $this->faker->optional()->randomElement(['hora', 'diaria', 'mensal']),
        'price' => $this->faker->randomFloat(2, 0, 999.99),
        'userId' => User::factory()
    ];
}
```

### **Etapa 7: Configurar Testes**
```bash
# Criar banco de teste
CREATE DATABASE jump_park_test;
```

**Configurar phpunit.xml:**
```xml

    
    
    
    
    
    

```

**Configurar database.php:**
```php
'testing' => [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'jump_park_test',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

### **Etapa 8: Criar Testes**
```php
// Arquivo: tests/Feature/ServiceOrderTest.php
class ServiceOrderTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_should_create_service_order_successfully(): void
    {
        $user = User::create(['name' => 'João Silva']);
        
        $response = $this->postJson('/api/v1/service-orders', [
            'vehiclePlate' => 'ABC1234',
            'entryDateTime' => '2024-01-15 10:30:00',
            'exitDateTime' => '2024-01-15 18:45:00',
            'priceType' => 'hora',
            'price' => 25.50,
            'userId' => $user->id
        ]);
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
    
    public function test_should_fail_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/service-orders', [
            'vehiclePlate' => 'INVALID_PLATE',
            'entryDateTime' => 'invalid-date',
            'userId' => 999
        ]);
        
        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }
    
    public function test_should_list_service_orders(): void
    {
        $user = User::create(['name' => 'João Silva']);
        
        ServiceOrder::create([
            'vehiclePlate' => 'ABC1234',
            'entryDateTime' => '2024-01-15 10:30:00',
            'exitDateTime' => '0001-01-01 00:00:00',
            'priceType' => 'hora',
            'price' => 25.50,
            'userId' => $user->id
        ]);
        
        $response = $this->getJson('/api/v1/service-orders');
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
```

### **Etapa 9: Executar e Testar**
```bash
# Executar migrations
php artisan migrate
php artisan migrate --env=testing

# Executar testes
vendor\bin\pest

# Iniciar servidor
php artisan serve

# Testar endpoints
POST http://localhost:8000/api/v1/service-orders
GET http://localhost:8000/api/v1/service-orders
```

### **Etapa 10: Resultado Final**
- ✅ API REST com 2 endpoints funcionais
- ✅ Validações robustas implementadas
- ✅ Testes automatizados (3 passed)
- ✅ Relacionamentos funcionando (user_name)
- ✅ Códigos de resposta corretos (200/422)
- ✅ Estrutura baseada em db-structure.sql

**Projeto concluído com sucesso!**


