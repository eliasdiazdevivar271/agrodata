# AgroData

Plataforma de gestión de datos para pymes agropecuarias paraguayas. Centraliza inventario, lotes, ventas, compras y reportes en una base de datos relacional accesible desde el navegador.

## Estructura del proyecto

- `backend/` — API REST en Laravel 11 + MySQL + Sanctum + Scramble
- `frontend/` — Interfaz web en React 18 + Vite + Tailwind CSS

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8 (o XAMPP)

## Instalación

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Editar .env con las credenciales de MySQL
php artisan migrate --seed
php artisan serve
```

Backend: http://127.0.0.1:8000
Documentación Swagger: http://127.0.0.1:8000/docs/api

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Frontend: http://localhost:5173

## Usuario de prueba

- Email: admin@agrodata.com
- Password: password

## Stack

### Backend
- Laravel 11
- MySQL
- Laravel Sanctum
- Scramble (documentación automática)
- Pest (testing)

### Frontend
- React 18
- Vite
- Tailwind CSS
- React Router
- Axios
- Recharts

## Autor

[Nombre completo del estudiante]
Instituto [Nombre del instituto]
Año 2026
