<?php

namespace Src\Model;

class User {
    private ?int $id;
    private string $fullName;
    private string $role;
    private int $efficiency;

    public function __construct(?int $id, string $fullName, string $role, int $efficiency) {
        $this->id = $id;
        $this->setFullName($fullName);
        $this->setRole($role);
        $this->setEfficiency($efficiency);
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFullName(): string {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void {
        if (strlen($fullName) > 255) {
            throw new \InvalidArgumentException("Слишком длинное имя.");
        }
        $this->fullName = $fullName;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getEfficiency(): int {
        return $this->efficiency;
    }

    public function setEfficiency(int $efficiency): void {
        if ($efficiency < 0 || $efficiency > 100) {
            throw new \InvalidArgumentException("Эффективность должна быть не больше 100.");
        }
        $this->efficiency = $efficiency;
    }

    public function toArray(): array {
        return [
            "id" => $this->id,
            "full_name" => $this->fullName,
            "role" => $this->role,
            "efficiency" => $this->efficiency
        ];
    }
}
