<?php

namespace Stoyishi\Bot;

use Stoyishi\Bot\Types\User;
use Stoyishi\Bot\Types\Location;

class InlineQuery
{
    private array $data;
    public ?string $id = null;
    public ?User $from = null;
    public ?string $query = null;
    public ?string $offset = null;
    public ?string $chatType = null;
    public ?Location $location = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'] ?? null;
        $this->query = $data['query'] ?? null;
        $this->offset = $data['offset'] ?? null;
        $this->chatType = $data['chat_type'] ?? null;
        
        if (isset($data['from'])) {
            $this->from = new User($data['from']);
        }
        
        if (isset($data['location'])) {
            $this->location = new Location($data['location']);
        }
    }
    
    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function getQuery(): ?string
    {
        return $this->query;
    }
    
    public function getFrom(): ?User
    {
        return $this->from;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}