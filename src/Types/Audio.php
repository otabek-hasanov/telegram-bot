<?php

namespace Stoyishi\Bot\Types;

class Audio
{
    private array $data;
    public ?string $fileId = null;
    public ?string $fileUniqueId = null;
    public ?int $duration = null;
    public ?string $performer = null;
    public ?string $title = null;
    public ?string $fileName = null;
    public ?string $mimeType = null;
    public ?int $fileSize = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fileId = $data['file_id'] ?? null;
        $this->fileUniqueId = $data['file_unique_id'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->performer = $data['performer'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->fileName = $data['file_name'] ?? null;
        $this->mimeType = $data['mime_type'] ?? null;
        $this->fileSize = $data['file_size'] ?? null;
    }
    
    public function getFileId(): ?string
    {
        return $this->fileId;
    }
    
    public function getDuration(): ?int
    {
        return $this->duration;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}