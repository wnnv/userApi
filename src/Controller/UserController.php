<?php

namespace Src\Controller;

use Src\Service\UserService;
use InvalidArgumentException;
use PDOException;

class UserController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param string $action
     * @param array $params
     * @param array|null $data
     * @return array
     */
    public function handleRequest(string $action, array $params, ?array $data = null): array
    {
        try {
            return match ($action) {
                'create' => $this->userService->createUser(
                    $data['full_name'] ?? '',
                    $data['role'] ?? '',
                    $data['efficiency'] ?? 0
                ),
                'get' => isset($params['id'])
                    ? $this->userService->getUserById((int)$params['id'])
                    : $this->userService->getUsers($params),
                'update' => $this->userService->updateUser((int)$params['id'], $data ?? []),
                'delete' => isset($params['id'])
                    ? $this->userService->deleteUser((int)$params['id'])
                    : $this->userService->deleteAllUsers(),
                default => [
                    "success" => false,
                    "result" => ["error" => "Invalid action specified"]
                ],
            };
        } catch (InvalidArgumentException $e) {
            return [
                "success" => false,
                "result" => ["error" => "Invalid input: " . $e->getMessage()]
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "result" => ["error" => "Database error: " . $e->getMessage()]
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "result" => ["error" => "An unexpected error occurred: " . $e->getMessage()]
            ];
        }
    }
}
