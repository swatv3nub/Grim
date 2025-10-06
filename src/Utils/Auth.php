<?php
namespace Grim\Utils;

class Auth
{
    private $users;

    public function __construct()
    {
        $this->users = require __DIR__ . '/../../config/users.php';
    }

    public function authenticate($username, $password)
    {
        foreach ($this->users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                return $user['role'];
            }
        }
        return false;
    }
}
