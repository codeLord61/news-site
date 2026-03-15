<?php

namespace app\models;

use app\core\Model;

class User extends Model
{
    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db()->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     *
     * @return bool Whether the insert was successful.
     */
    public function create(string $username, string $email, string $name, string $passwordHash, int $roleId): bool
    {
        $stmt = $this->db()->prepare(
            "INSERT INTO users (username, email, name, password, role_id) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$username, $email, $name, $passwordHash, $roleId]);
    }

    /**
     * Get the ID for a role by its name.
     */
    public function getRoleIdByName(string $name): int|false
    {
        $stmt = $this->db()->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }
}
