<?php

namespace Stoyishi\Bot\Types;

class User
{
    private array $data;
    public ?int $id = null;
    public ?bool $isBot = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $username = null;
    public ?string $languageCode = null;
    public ?bool $isPremium = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'] ?? null;
        $this->isBot = $data['is_bot'] ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->languageCode = $data['language_code'] ?? null;
        $this->isPremium = $data['is_premium'] ?? null;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }
    
    public function getFullName(): string
    {
        $name = $this->firstName ?? '';
        
        if ($this->lastName) {
            $name .= ' ' . $this->lastName;
        }
        
        return trim($name);
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}