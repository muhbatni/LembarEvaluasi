<?php
declare(strict_types=1);

/**
 * Auth helper (native PHP)
 * Assumption: login sets $_SESSION['admin'] (string/true) when authenticated.
 */

function is_admin(): bool {
    return isset($_SESSION['admin']) && $_SESSION['admin'] !== '';
}

function require_admin(): void {
    if (!is_admin()) {
        // If request expects JSON, return JSON; otherwise redirect.
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (str_contains($accept, 'application/json')) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        header('Location: index.php?page=login');
        exit;
    }
}
