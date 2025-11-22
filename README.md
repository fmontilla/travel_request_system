# Travel Request System API

Sistema de gerenciamento de pedidos de viagem corporativa desenvolvido em Laravel.

---

## Requisitos

- Docker e Docker Compose
- PHP 8.2+
- Composer
- MySQL 8.0+

## Configuracao e Instalacao

#### 1. Clonar o repositorio

```bash
git clone <repository-url>
cd travel_request_system
```

#### 2. Configurar variaveis de ambiente

```bash
cp .env.example .env
```

O arquivo `.env.example` já vem pré-configurado com os valores padrão.

#### 3. Executar com Docker

```bash
docker-compose up -d
```

#### 4. Instalar dependencias

```bash
docker-compose exec app composer install
```

#### 5. Gerar chave da aplicacao e JWT secret

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret
```

#### 6. Executar migrations e seeders

```bash
docker-compose exec app php artisan migrate --seed
```

#### 7. Gerar documentacao Swagger

```bash
docker-compose exec app php artisan l5-swagger:generate
```

### Verificação

A aplicacao estara disponivel em: http://localhost:8000

Para verificar se tudo está funcionando:

```bash
# Verificar status dos containers
docker-compose ps

# Testar a API
curl http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"password","password_confirmation":"password"}'
```

## Usuarios Padrao

Apos executar os seeders, os seguintes usuarios estarao disponiveis:

**Administrador:**
- Email: admin@example.com
- Password: password
- Is Admin: true

**Usuario Regular 1:**
- Email: john@example.com
- Password: password
- Is Admin: false

**Usuario Regular 2:**
- Email: jane@example.com
- Password: password
- Is Admin: false

## Documentacao da API

A documentacao completa da API esta disponivel via Swagger em:

```
http://localhost:8000/api/documentation
```

### Endpoints Principais

#### Autenticacao

- POST `/api/v1/register` - Registrar novo usuario
- POST `/api/v1/login` - Login
- POST `/api/v1/logout` - Logout
- GET `/api/v1/me` - Obter perfil do usuario autenticado
- POST `/api/v1/refresh` - Renovar token

#### Travel Requests

- POST `/api/v1/travel-requests` - Criar pedido de viagem
- GET `/api/v1/travel-requests` - Listar pedidos do usuario com filtros
- GET `/api/v1/travel-requests/{id}` - Consultar pedido especifico
- PATCH `/api/v1/travel-requests/{id}/status` - Atualizar status (admin only)

### Parametros de Filtro

A listagem de pedidos aceita os seguintes parametros de consulta:

- `status`: requested, approved, cancelled
- `start_date`: Data inicial (YYYY-MM-DD)
- `end_date`: Data final (YYYY-MM-DD)
- `destination`: Busca por destino (parcial)

## Executar Testes

### Todos os testes

```bash
docker-compose exec app php artisan test
```

### Testes unitarios

```bash
docker-compose exec app php artisan test --testsuite=Unit
```

### Testes de feature

```bash
docker-compose exec app php artisan test --testsuite=Feature
```

## Comandos Uteis

### Limpar cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Verificar rotas

```bash
docker-compose exec app php artisan route:list
```

### Acessar container

```bash
docker-compose exec app bash
```

### Ver logs

```bash
docker-compose logs -f app
```

### Parar containers

```bash
docker-compose down
```

### Rebuild containers

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```
