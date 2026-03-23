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
        $stmt = $this->db()->prepare("
            SELECT u.id, u.password, u.name, r.name as role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db()->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     *
     * @return bool Whether the insert was successful.
     */
    public function create(string $email, string $name, string $passwordHash, int $roleId, string $rawPassword): bool
    {
        $stmt = $this->db()->prepare(
            "INSERT INTO users (email, name, password, role_id, pass) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$email, $name, $passwordHash, $roleId, $rawPassword]);
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

    /**
     * Get all users with their roles.
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db()->query("
            SELECT u.id, u.name, u.email, u.avatar_path, u.created_at, u.role_id, r.name as role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update a user's role.
     */
    public function updateRole(int $userId, int $roleId): bool
    {
        $stmt = $this->db()->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        return $stmt->execute([$roleId, $userId]);
    }
}
