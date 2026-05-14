<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public function __construct(private string $passwordHash, string $sessionName = 'liga_admin')
    {
        Session::start($sessionName);
    }

    public function attempt(string $password): bool
    {
        if ($this->passwordHash === '' || str_starts_with($this->passwordHash, '$2y$12$REPLACE')) {
            return false;
        }
        if (!password_verify($password, $this->passwordHash)) {
            return false;
        }
        Session::regenerate();
        Session::set('admin', ['ts' => time()]);
        return true;
    }

    public function check(): bool
    {
        $admin = Session::get('admin');
        return is_array($admin) && !empty($admin['ts']);
    }

    public function requireLogin(): void
    {
        if (!$this->check()) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function logout(): void
    {
        Session::destroy();
    }
}
