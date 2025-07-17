🚀 Jump Park API - Sistema de Gerenciamento de Ordens de Serviço

<strong>API REST robusta para gerenciamento de ordens de serviço de estacionamento</strong> </p>
📋 Índice
🎯 Sobre o Projeto

✨ Funcionalidades

🛠️ Tecnologias

🚀 Instalação

📡 Endpoints

🧪 Testes

📊 Estrutura do Banco

🔧 Configuração

📝 Exemplos de Uso

🤝 Contribuição

🎯 Sobre o Projeto
O Jump Park API é uma solução completa para gerenciamento de ordens de serviço de estacionamento, desenvolvida com foco em performance, segurança e escalabilidade. A API segue os padrões REST e implementa validações robustas para garantir a integridade dos dados.

🌟 Destaques
✅ Arquitetura RESTful com endpoints bem definidos

✅ Validações avançadas para entrada de dados

✅ Testes automatizados com cobertura completa

✅ Relacionamentos otimizados entre tabelas

✅ Documentação completa da API

✅ Estrutura de banco espelhada do arquivo db-structure.sql

✨ Funcionalidades
🔐 Core Features
Funcionalidade	Descrição	Status
Criação de Ordens	Criar novas ordens de serviço com validações	✅
Listagem de Ordens	Listar todas as ordens com relacionamentos	✅
Validação de Dados	Validações robustas para entrada de dados	✅
Relacionamentos	Associação entre usuários e ordens	✅
Testes Automatizados	Cobertura completa com Pest PHP	✅
📋 Validações Implementadas
Placa do Veículo: Formato brasileiro (ABC1234)

Data/Hora: Formato Y-m-d H:i:s com validação de período

Tipo de Preço: Valores pré-definidos (hora, diaria, mensal, avulso)

Usuário: Verificação de existência na base de dados

Preço: Valores numéricos positivos

🛠️ Tecnologias
Backend
Laravel 11.x - Framework PHP moderno e robusto

PHP 8.2+ - Linguagem de programação

MySQL 8.0+ - Sistema de gerenciamento de banco de dados

Composer - Gerenciador de dependências PHP

Testes
Pest PHP - Framework de testes moderno e expressivo

PHPUnit - Base para testes unitários

RefreshDatabase - Isolamento de dados entre testes

Ambiente
XAMPP - Ambiente de desenvolvimento local

phpMyAdmin - Interface gráfica para MySQL

🚀 Instalação
📋 Pré-requisitos
PHP 8.2 ou superior

Composer instalado

MySQL 8.0 ou superior

XAMPP (recomendado)

🔧 Passo a Passo
bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/jump-park-api.git
cd jump-park-api

# 2. Instalar dependências
composer install

# 3. Configurar arquivo de ambiente
cp .env.example .env

# 4. Configurar banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jump_park
DB_USERNAME=root
DB_PASSWORD=

# 5. Gerar chave da aplicação
php artisan key:generate

# 6. Executar migrations
php artisan migrate

# 7. Iniciar servidor
php artisan serve
🎯 Configuração de Testes
bash
# Criar banco de dados para testes
CREATE DATABASE jump_park_test;

# Executar migrations no ambiente de teste
php artisan migrate --env=testing

# Executar testes
vendor\bin\pest
📡 Endpoints
🔗 Base URL
text
http://localhost:8000/api/v1
📝 Endpoints Disponíveis
1. Criar Ordem de Serviço
text
POST /service-orders
Headers:

text
Content-Type: application/json
Accept: application/json
Parâmetros:

Campo	Tipo	Obrigatório	Descrição
vehiclePlate	string	✅	Placa do veículo (ABC1234)
entryDateTime	datetime	✅	Data/hora de entrada
exitDateTime	datetime	❌	Data/hora de saída
priceType	string	❌	Tipo de preço
price	decimal	❌	Valor do serviço
userId	integer	✅	ID do usuário
Exemplo de Requisição:

json
{
    "vehiclePlate": "ABC1234",
    "entryDateTime": "2024-01-15 10:30:00",
    "exitDateTime": "2024-01-15 18:45:00",
    "priceType": "hora",
    "price": 25.50,
    "userId": 1
}
Resposta de Sucesso (200):

json
{
    "success": true,
    "message": "Ordem de serviço criada com sucesso",
    "data": {
        "id": 1,
        "vehiclePlate": "ABC1234",
        "entryDateTime": "2024-01-15T10:30:00.000000Z",
        "exitDateTime": "2024-01-15T18:45:00.000000Z",
        "priceType": "hora",
        "price": "25.50",
        "userId": 1,
        "user": {
            "id": 1,
            "name": "João Silva"
        }
    }
}
2. Listar Ordens de Serviço
text
GET /service-orders
Resposta de Sucesso (200):

json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "vehiclePlate": "ABC1234",
            "entryDateTime": "2024-01-15 10:30:00",
            "exitDateTime": "2024-01-15 18:45:00",
            "priceType": "hora",
            "price": "25.50",
            "userId": 1,
            "user_name": "João Silva"
        }
    ]
}
⚠️ Códigos de Erro
Código	Descrição
200	Sucesso
422	Erro de validação
500	Erro interno do servidor
🧪 Testes
🎯 Cobertura de Testes
A API possui 100% de cobertura dos cenários críticos:

✅ Criação com sucesso (código 200)

✅ Validação de erros (código 422)

✅ Listagem com relacionamentos (user_name)

🏃‍♂️ Executar Testes
bash
# Todos os testes
vendor\bin\pest

# Testes específicos da API
vendor\bin\pest tests/Feature/ServiceOrderTest.php

# Testes com detalhes
vendor\bin\pest --verbose

# Testes com cobertura
vendor\bin\pest --coverage
📊 Resultado Esperado
text
✓ should create service order successfully
✓ should fail with invalid data
✓ should list service orders

Tests:  3 passed
Time:   0.87s
📊 Estrutura do Banco
🗄️ Diagrama de Relacionamentos
text
users (1) ←→ (N) service_orders
📋 Tabelas
users
sql
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);
service_orders
sql
CREATE TABLE `service_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vehiclePlate` CHAR(7) NOT NULL,
  `entryDateTime` DATETIME NOT NULL,
  `exitDateTime` DATETIME DEFAULT '0001-01-01 00:00:00',
  `priceType` VARCHAR(55) DEFAULT NULL,
  `price` DECIMAL(12,2) DEFAULT '0.00',
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_service_orders_users` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
);
🔧 Configuração
📁 Estrutura do Projeto
text
jump-park-api/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── ServiceOrderController.php
│   └── Models/
│       ├── User.php
│       └── ServiceOrder.php
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── ServiceOrderFactory.php
│   └── migrations/
├── tests/
│   └── Feature/
│       └── ServiceOrderTest.php
├── routes/
│   └── api.php
└── README.md
⚙️ Configurações Importantes
Database (config/database.php)
php
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
PHPUnit (phpunit.xml)
xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="testing"/>
    <env name="DB_DATABASE" value="jump_park_test"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
</php>
📝 Exemplos de Uso
🎯 Cenário 1: Criação Básica
bash
curl -X POST http://localhost:8000/api/v1/service-orders \
  -H "Content-Type: application/json" \
  -d '{
    "vehiclePlate": "ABC1234",
    "entryDateTime": "2024-01-15 10:30:00",
    "userId": 1
  }'
🎯 Cenário 2: Criação Completa
bash
curl -X POST http://localhost:8000/api/v1/service-orders \
  -H "Content-Type: application/json" \
  -d '{
    "vehiclePlate": "XYZ9876",
    "entryDateTime": "2024-01-15 14:00:00",
    "exitDateTime": "2024-01-15 20:00:00",
    "priceType": "diaria",
    "price": 50.00,
    "userId": 2
  }'
🎯 Cenário 3: Listagem
bash
curl -X GET http://localhost:8000/api/v1/service-orders \
  -H "Accept: application/json"
🤝 Contribuição
🌟 Como Contribuir
Fork o projeto

Crie uma branch para sua feature (git checkout -b feature/nova-funcionalidade)

Commit suas mudanças (git commit -m 'Adiciona nova funcionalidade')

Push para a branch (git push origin feature/nova-funcionalidade)

Abra um Pull Request