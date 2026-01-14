<?php
declare(strict_types=1);

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_validate(?string $token): void {
    $ok = isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
    if (!$ok) {
        http_response_code(400);
        exit('Invalid CSRF token');
    }
}
