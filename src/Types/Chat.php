<?php

namespace Stoyishi\Bot\Types;

class Chat
{
    private array $data;
    public ?int $id = null;
    public ?string $type = null;
    public ?string $title = null;
    public ?string $username = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?bool $isForum = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->isForum = $data['is_forum'] ?? null;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getType(): ?string
    {
        return $this->type;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function isPrivate(): bool
    {
        return $this->type === 'private';
    }
    
    public function isGroup(): bool
    {
        return $this->type === 'group' || $this->type === 'supergroup';
    }
    
    public function isChannel(): bool
    {
        return $this->type === 'channel';
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}