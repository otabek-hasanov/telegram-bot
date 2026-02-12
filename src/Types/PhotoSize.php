<?php

namespace Stoyishi\Bot\Types;

class PhotoSize
{
    private array $data;
    public ?string $fileId = null;
    public ?string $fileUniqueId = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?int $fileSize = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fileId = $data['file_id'] ?? null;
        $this->fileUniqueId = $data['file_unique_id'] ?? null;
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
        $this->fileSize = $data['file_size'] ?? null;
    }
    
    public function getFileId(): ?string
    {
        return $this->fileId;
    }
    
    public function getWidth(): ?int
    {
        return $this->width;
    }
    
    public function getHeight(): ?int
    {
        return $this->height;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}