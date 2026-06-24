<?php
use Illuminate\Support\Facades\DB;

$sql = file_get_contents('insert_usuarios.sql');
$sql = str_replace("\xEF\xBB\xBF", '', $sql);
$queries = explode(';', $sql);

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('usuario')->truncate();

foreach ($queries as $query) {
    if (trim($query)) {
        DB::unprepared($query);
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Users restored. Total: " . App\Models\User::count() . "\n";
