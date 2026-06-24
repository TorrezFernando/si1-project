<?php
use Illuminate\Support\Facades\Hash;
use App\Models\User;
$user = User::where('username', 'alumno_1')->first();
if ($user) {
    $user->password = Hash::make('alu001');
    $user->save();
    echo "OK";
}
