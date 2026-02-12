<?php

namespace Stoyishi\Bot;

use Stoyishi\Bot\Types\User;

class CallbackQuery
{
    private array $data;
    public ?string $id = null;
    public ?User $from = null;
    public ?Message $message = null;
    public ?string $inlineMessageId = null;
    public ?string $chatInstance = null;
    public ?string $data_value = null;
    public ?string $gameShortName = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'] ?? null;
        $this->inlineMessageId = $data['inline_message_id'] ?? null;
        $this->chatInstance = $data['chat_instance'] ?? null;
        $this->data_value = $data['data'] ?? null;
        $this->gameShortName = $data['game_short_name'] ?? null;
        
        if (isset($data['from'])) {
            $this->from = new User($data['from']);
        }
        
        if (isset($data['message'])) {
            $this->message = new Message($data['message']);
        }
    }
    
    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function getData(): ?string
    {
        return $this->data_value;
    }
    
    public function getRawData(): array
    {
        return $this->data;
    }
    
    public function getFrom(): ?User
    {
        return $this->from;
    }
    
    public function getMessage(): ?Message
    {
        return $this->message;
    }
}