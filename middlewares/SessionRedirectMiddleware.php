<?php

namespace App\Middlewares;
use App\Middlewares\Middleware;

class SessionRedirectMiddleware implements Middleware
{
    public function handle()
    {
        if (isset($_SESSION['user_id']) && isset($_SESSION['roles_user'])) {
            $role = strtolower($_SESSION['roles_user']);




            switch ($role) {
                case 'user':

                    header('Location: ./home');


                    break;

            }

            exit();
        }

        // Si no hay sesión, permitir continuar normalmente.
    }
}
