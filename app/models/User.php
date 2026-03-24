<?php

namespace app\models;

use app\core\Model;

class User extends Model
{
    /**
     * Find a user by email address.
     *
     * Input: user email string.
     * Output: row array (id/password/name/role_name) or false.
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
     *
     * Input: user id.
     * Output: full user row + role_name or false.
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
     * Input: email, display name, hashed password, role id, raw password copy.
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
     *
     * Input example: "Reader".
     * Output: role id or false.
     */
    public function getRoleIdByName(string $name): int|false
    {
        $stmt = $this->db()->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetchColumn();
    }

    /**
     * Get all users with their roles.
     *
     * Input: none.
     * Output: list of users sorted by newest first.
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
     *
     * Input: user id + role id.
     * Output: true when update query executes successfully.
     */
    public function updateRole(int $userId, int $roleId): bool
    {
        $stmt = $this->db()->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        return $stmt->execute([$roleId, $userId]);
    }

    /**
     * Update a user's profile (name and avatar).
     *
     * Input: user id, new name, avatar web path (or null).
     * Output: true when update query executes successfully.
     */
    public function updateProfile(int $userId, string $name, ?string $avatarPath): bool
    {
        $stmt = $this->db()->prepare("UPDATE users SET name = ?, avatar_path = ? WHERE id = ?");
        return $stmt->execute([$name, $avatarPath, $userId]);
    }
}
