<?php
namespace App\Services;

class ResponseService
{
    /**
     * Tworzy odpowiedź sukcesu.
     */
    public function successResponse($message)
    {
        return [
            'errors' => [], // Brak błędów przy sukcesie
            'message' => $message
        ];
    }

    /**
     * Tworzy odpowiedź błędu.
     * Może obsługiwać różne typy błędów (np. customer, server, validation)
     */
    public function errorResponse($type, $message, $details = null)
    {
        return [
            'errors' => [
                $type => [$message] // Błąd może dotyczyć różnych typów, np. customer, validation, server
            ],
            'message' => $details ? $details : $message // Jeśli są dodatkowe szczegóły, będą zawarte w message
        ];
    }
}
