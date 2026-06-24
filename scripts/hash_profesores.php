<?php
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Hashear contraseñas de todos los profesores (id_rol = 2)
$profesores = User::where('id_rol', 2)->get();

foreach ($profesores as $prof) {
    $plainPassword = $prof->password;
    // Solo hashear si la contraseña NO está ya hasheada (los hashes de bcrypt empiezan con $2y$)
    if (!str_starts_with($plainPassword, '$2y$')) {
        $prof->password = Hash::make($plainPassword);
        $prof->save();
    }
}

echo "Profesores actualizados: " . $profesores->count() . "\n";
