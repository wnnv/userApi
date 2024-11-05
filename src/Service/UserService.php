<?php

namespace Src\Service;

use Src\Model\User;
use Src\Database\Database;
use PDO;
use Src\Exception\UserNotFoundException;
use InvalidArgumentException;

class UserService {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createUser(array $userData): User {
        $user = new User(null, $userData['full_name'], $userData['role'], $userData['efficiency']);

        $this->validateUserData($user);

        $stmt = $this->pdo->prepare("INSERT INTO users (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)");
        $stmt->execute([
            'full_name' => $user->getFullName(),
            'role' => $user->getRole(),
            'efficiency' => $user->getEfficiency()
        ]);

        $user->setId((int)$this->pdo->lastInsertId());
        return $user;
    }

    public function getUserById(int $id): User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            throw new UserNotFoundException("Пользователь с id $id не найден.");
        }

        return new User(
            (int)$userData['id'],
            $userData['full_name'],
            $userData['role'],
            (int)$userData['efficiency']
        );
    }

    public function getUsers(array $filters = []): array {
        $query = "SELECT * FROM users";
        $params = [];
        $conditions = [];

        // Добавление фильтров, если они есть
        if (isset($filters['role'])) {
            $conditions[] = "role = :role";
            $params['role'] = $filters['role'];
        }
        if (isset($filters['efficiency'])) {
            $conditions[] = "efficiency = :efficiency";
            $params['efficiency'] = $filters['efficiency'];
        }

        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        $users = [];
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                (int)$userData['id'],
                $userData['full_name'],
                $userData['role'],
                (int)$userData['efficiency']
            );
        }

        return $users;
    }

    public function updateUser(int $id, array $userData): User {
        // Получаем пользователя по ID и обновляем его данные
        $user = $this->getUserById($id);

        if (isset($userData['full_name'])) {
            $user->setFullName($userData['full_name']);
        }
        if (isset($userData['role'])) {
            $user->setRole($userData['role']);
        }
        if (isset($userData['efficiency'])) {
            $user->setEfficiency((int)$userData['efficiency']);
        }

        $this->validateUserData($user);

        $stmt = $this->pdo->prepare("UPDATE users SET full_name = :full_name, role = :role, efficiency = :efficiency WHERE id = :id");
        $stmt->execute([
            'full_name' => $user->getFullName(),
            'role' => $user->getRole(),
            'efficiency' => $user->getEfficiency(),
            'id' => $user->getId()
        ]);

        return $user;
    }

    public function deleteUser(int $id): User {
        $user = $this->getUserById($id);

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $user;
    }

    public function deleteAllUsers(): void {
        $this->pdo->exec("DELETE FROM users");
    }

    private function validateUserData(User $user): void {
        if (strlen($user->getFullName()) > 255) {
            throw new InvalidArgumentException("Full name is too long.");
        }

        if ($user->getEfficiency() < 0 || $user->getEfficiency() > 100) {
            throw new InvalidArgumentException("Efficiency must be between 0 and 100.");
        }
    }
}
