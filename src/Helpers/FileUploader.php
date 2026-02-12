<?php

namespace Stoyishi\Bot\Helpers;

class FileUploader
{
    /**
     * Faylni CURLFile obyektiga aylantirish
     */
    public static function createFromPath(string $path, ?string $mimeType = null, ?string $filename = null): \CURLFile
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found: {$path}");
        }
        
        if ($mimeType === null) {
            $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        }
        
        if ($filename === null) {
            $filename = basename($path);
        }
        
        return new \CURLFile($path, $mimeType, $filename);
    }
    
    /**
     * URL dan fayl yuklab olish va CURLFile yaratish
     */
    public static function createFromUrl(string $url, ?string $filename = null): \CURLFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'telegram_');
        
        $content = file_get_contents($url);
        
        if ($content === false) {
            throw new \RuntimeException("Failed to download file from URL: {$url}");
        }
        
        file_put_contents($tempFile, $content);
        
        if ($filename === null) {
            $filename = basename(parse_url($url, PHP_URL_PATH));
        }
        
        $mimeType = mime_content_type($tempFile) ?: 'application/octet-stream';
        
        return new \CURLFile($tempFile, $mimeType, $filename);
    }
    
    /**
     * Base64 dan fayl yaratish
     */
    public static function createFromBase64(string $base64Data, string $filename, ?string $mimeType = null): \CURLFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'telegram_');
        
        $data = base64_decode($base64Data);
        
        if ($data === false) {
            throw new \InvalidArgumentException("Invalid base64 data");
        }
        
        file_put_contents($tempFile, $data);
        
        if ($mimeType === null) {
            $mimeType = mime_content_type($tempFile) ?: 'application/octet-stream';
        }
        
        return new \CURLFile($tempFile, $mimeType, $filename);
    }
}