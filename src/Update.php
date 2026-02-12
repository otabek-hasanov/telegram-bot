<?php

namespace Stoyishi\Bot;

class Update
{
    private array $data;
    private ?int $updateId = null;
    private ?Message $message = null;
    private ?Message $editedMessage = null;
    private ?CallbackQuery $callbackQuery = null;
    private ?InlineQuery $inlineQuery = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->updateId = $data['update_id'] ?? null;
        
        if (isset($data['message'])) {
            $this->message = new Message($data['message']);
        }
        
        if (isset($data['edited_message'])) {
            $this->editedMessage = new Message($data['edited_message']);
        }
        
        if (isset($data['callback_query'])) {
            $this->callbackQuery = new CallbackQuery($data['callback_query']);
        }
        
        if (isset($data['inline_query'])) {
            $this->inlineQuery = new InlineQuery($data['inline_query']);
        }
    }
    
    public function getUpdateId(): ?int
    {
        return $this->updateId;
    }
    
    public function getMessage(): ?Message
    {
        return $this->message;
    }
    
    public function getMessages(): ?Message
    {
        return $this->message;
    }
    
    public function getEditedMessage(): ?Message
    {
        return $this->editedMessage;
    }
    
    public function getCallbackQuery(): ?CallbackQuery
    {
        return $this->callbackQuery;
    }
    
    public function getInlineQuery(): ?InlineQuery
    {
        return $this->inlineQuery;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function hasMessage(): bool
    {
        return $this->message !== null;
    }
    
    public function hasEditedMessage(): bool
    {
        return $this->editedMessage !== null;
    }
    
    public function hasCallbackQuery(): bool
    {
        return $this->callbackQuery !== null;
    }
    
    public function hasInlineQuery(): bool
    {
        return $this->inlineQuery !== null;
    }
}