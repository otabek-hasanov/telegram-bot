<?php

namespace Stoyishi\Bot\Types;

class Video
{
    private array $data;
    public ?string $fileId = null;
    public ?string $fileUniqueId = null;
    public ?int $width = null;
    public ?int $height = null;
    public ?int $duration = null;
    public ?PhotoSize $thumbnail = null;
    public ?string $fileName = null;
    public ?string $mimeType = null;
    public ?int $fileSize = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fileId = $data['file_id'] ?? null;
        $this->fileUniqueId = $data['file_unique_id'] ?? null;
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->fileName = $data['file_name'] ?? null;
        $this->mimeType = $data['mime_type'] ?? null;
        $this->fileSize = $data['file_size'] ?? null;
        
        if (isset($data['thumbnail'])) {
            $this->thumbnail = new PhotoSize($data['thumbnail']);
        }
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
    
    public function getDuration(): ?int
    {
        return $this->duration;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}