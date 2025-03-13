<?php

use App\Http\Controllers\Api\AuthController;

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel()->ignoring(AuthController::class);
