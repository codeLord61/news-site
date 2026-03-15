<?php

namespace app\models;

use app\core\Model;

class Token extends Model
{
    /**
     * Create a new personal access token.
     */
    public function create(int $userId, string $token, string $expiresAt): bool
    {
        $stmt = $this->db()->prepare(
            "INSERT INTO personal_access_tokens (user_id, token, expires_at) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$userId, $token, $expiresAt]);
    }

    /**
     * Delete a token (logout / revoke).
     */
    public function deleteByToken(string $token): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM personal_access_tokens WHERE token = ?");
        return $stmt->execute([$token]);
    }

    /**
     * Find a valid (non-expired) token.
     */
    public function findValid(string $token): array|false
    {
        $stmt = $this->db()->prepare(
            "SELECT user_id FROM personal_access_tokens WHERE token = ? AND (expires_at IS NULL OR expires_at > NOW())"
        );
        $stmt->execute([$token]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
