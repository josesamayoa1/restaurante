<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Caja;
use App\Models\TipoPago;
use App\Models\Mesa;
use App\Models\Producto;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Roles
        $roles = [
            ['nombre' => 'admin'],
            ['nombre' => 'cajero'],
            ['nombre' => 'mesero'],
            ['nombre' => 'cocinero'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Admin user
        User::create([
            'nombre' => 'Admin',
            'apellidos' => 'Restaurante',
            'edad' => 30,
            'role_id' => 1,
            'email' => 'admin@restaurante.com',
            'password' => bcrypt('password'),
        ]);

        // Cajero
        User::create([
            'nombre' => 'Juan',
            'apellidos' => 'Perez',
            'edad' => 25,
            'role_id' => 2,
            'email' => 'cajero@restaurante.com',
            'password' => bcrypt('password'),
        ]);

        // Mesero
        User::create([
            'nombre' => 'Maria',
            'apellidos' => 'Gomez',
            'edad' => 28,
            'role_id' => 3,
            'email' => 'mesero@restaurante.com',
            'password' => bcrypt('password'),
        ]);

        // Cocinero
        User::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Lopez',
            'edad' => 35,
            'role_id' => 4,
            'email' => 'cocinero@restaurante.com',
            'password' => bcrypt('password'),
        ]);

        // Cajas
        Caja::create(['nombre' => 'Caja Principal']);
        Caja::create(['nombre' => 'Caja Secundaria']);

        // Tipo de pagos
        TipoPago::create(['nombre' => 'Efectivo']);
        TipoPago::create(['nombre' => 'Tarjeta']);
        TipoPago::create(['nombre' => 'Transferencia']);

        // Mesas
        for ($i = 1; $i <= 10; $i++) {
            Mesa::create(['nombre' => 'Mesa ' . $i]);
        }

        // Productos
        $productos = [
            ['nombre' => 'Pizza Margarita', 'descripcion' => 'Pizza con queso y tomate', 'precio' => 120],
            ['nombre' => 'Hamburguesa', 'descripcion' => 'Hamburguesa con queso y papas', 'precio' => 80],
            ['nombre' => 'Ensalada César', 'descripcion' => 'Ensalada con pollo y aderezo', 'precio' => 60],
            ['nombre' => 'Sopa del día', 'descripcion' => 'Sopa variada según temporada', 'precio' => 45],
            ['nombre' => 'Refresco', 'descripcion' => 'Refresco de 500ml', 'precio' => 20],
            ['nombre' => 'Agua mineral', 'descripcion' => 'Agua mineral 500ml', 'precio' => 15],
            ['nombre' => 'Café', 'descripcion' => 'Café americano', 'precio' => 25],
            ['nombre' => 'Postre del día', 'descripcion' => 'Postre variado según temporada', 'precio' => 40],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
