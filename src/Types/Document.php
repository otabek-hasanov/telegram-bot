<?php

namespace Stoyishi\Bot\Types;

class Document
{
    private array $data;
    public ?string $fileId = null;
    public ?string $fileUniqueId = null;
    public ?string $fileName = null;
    public ?string $mimeType = null;
    public ?int $fileSize = null;
    public ?PhotoSize $thumbnail = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fileId = $data['file_id'] ?? null;
        $this->fileUniqueId = $data['file_unique_id'] ?? null;
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
    
    public function getFileName(): ?string
    {
        return $this->fileName;
    }
    
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}