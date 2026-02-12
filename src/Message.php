<?php

namespace Stoyishi\Bot;

use Stoyishi\Bot\Types\User;
use Stoyishi\Bot\Types\Chat;
use Stoyishi\Bot\Types\PhotoSize;
use Stoyishi\Bot\Types\Document;
use Stoyishi\Bot\Types\Audio;
use Stoyishi\Bot\Types\Video;
use Stoyishi\Bot\Types\Location;

class Message
{
    private array $data;
    public ?int $messageId = null;
    public ?User $from = null;
    public ?Chat $chat = null;
    public ?int $date = null;
    public ?string $text = null;
    public ?array $entities = null;
    public ?array $photo = null;
    public ?Document $document = null;
    public ?Audio $audio = null;
    public ?Video $video = null;
    public ?Location $location = null;
    public ?Message $replyToMessage = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->messageId = $data['message_id'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->text = $data['text'] ?? $data['caption'] ?? null;
        $this->entities = $data['entities'] ?? null;
        
        if (isset($data['from'])) {
            $this->from = new User($data['from']);
        }
        
        if (isset($data['chat'])) {
            $this->chat = new Chat($data['chat']);
        }
        
        if (isset($data['photo'])) {
            $this->photo = [];
            foreach ($data['photo'] as $photoData) {
                $this->photo[] = new PhotoSize($photoData);
            }
        }
        
        if (isset($data['document'])) {
            $this->document = new Document($data['document']);
        }
        
        if (isset($data['audio'])) {
            $this->audio = new Audio($data['audio']);
        }
        
        if (isset($data['video'])) {
            $this->video = new Video($data['video']);
        }
        
        if (isset($data['location'])) {
            $this->location = new Location($data['location']);
        }
        
        if (isset($data['reply_to_message'])) {
            $this->replyToMessage = new Message($data['reply_to_message']);
        }
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getText(): ?string
    {
        return $this->text;
    }
    
    public function hasText(): bool
    {
        return $this->text !== null;
    }
    
    public function hasPhoto(): bool
    {
        return $this->photo !== null && !empty($this->photo);
    }
    
    public function hasDocument(): bool
    {
        return $this->document !== null;
    }
    
    public function hasAudio(): bool
    {
        return $this->audio !== null;
    }
    
    public function hasVideo(): bool
    {
        return $this->video !== null;
    }
    
    public function hasLocation(): bool
    {
        return $this->location !== null;
    }
}