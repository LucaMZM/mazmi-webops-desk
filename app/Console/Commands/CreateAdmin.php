<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'webops:admin {--name= : Nombre del administrador} {--email= : Email de acceso}';

    protected $description = 'Crear la primera cuenta administradora sin credenciales predeterminadas';

    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->error('Ya existen usuarios. La creación inicial está bloqueada.');

            return self::FAILURE;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Ejecuta este comando en una terminal interactiva para introducir una contraseña segura.');

            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Nombre');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->secret('Contraseña segura (mínimo 12 caracteres)');
        $confirmation = $this->secret('Repite la contraseña');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', Password::min(12)->mixedCase()->numbers()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (! is_string($confirmation) || ! hash_equals($password, $confirmation)) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('Administrador creado. Ya puedes iniciar sesión.');

        return self::SUCCESS;
    }
}
