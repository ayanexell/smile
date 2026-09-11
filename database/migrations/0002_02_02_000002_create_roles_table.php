use App\Models\Roles;
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id("id_role");
            $table->string("nama_role")->unique();
            $table->timestamps();
        });

        // Insert Default Roles
        $roles = [
            ['nama_role' => 'Super Admin'],
            ['nama_role' => 'Admin'],
            ['nama_role' => 'Koordinator'],
            ['nama_role' => 'User'],
        ];

        // foreach ($roles as $role) {
        //     App\Models\Roles::create($role);
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
