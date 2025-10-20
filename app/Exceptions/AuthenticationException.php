<?php
// app/Exceptions/AuthenticationException.php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
            'error' => 'Authentication token is missing or invalid.'
        ], 401);
    }
}