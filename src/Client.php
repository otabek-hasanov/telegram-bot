<?php

namespace Stoyishi\Bot;

use Stoyishi\Bot\Exceptions\TelegramException;
use Stoyishi\Bot\Helpers\FileUploader;

class Client
{
    private string $token;
    private string $apiUrl = 'https://api.telegram.org/bot';
    private ?array $lastResponse = null;
    private int $timeout = 30;
    
    public function __construct(string $token)
    {
        $this->token = $token;
    }
    
    /**
     * Webhook orqali yangilanishlarni olish
     */
    public function getWebhookUpdates(): Update
    {
        $input = file_get_contents('php://input');
        
        if (empty($input)) {
            throw new TelegramException('No webhook data received');
        }
        
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TelegramException('Invalid JSON in webhook data');
        }
        
        return new Update($data);
    }
    
    /**
     * Long polling orqali yangilanishlarni olish
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 0, array $allowedUpdates = []): array
    {
        $params = [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout
        ];
        
        if (!empty($allowedUpdates)) {
            $params['allowed_updates'] = $allowedUpdates;
        }
        
        $response = $this->sendData('getUpdates', $params);
        
        $updates = [];
        foreach ($response['result'] as $updateData) {
            $updates[] = new Update($updateData);
        }
        
        return $updates;
    }
    
    /**
     * Webhook o'rnatish
     */
    public function setWebhook(string $url, array $params = []): array
    {
        $params['url'] = $url;
        return $this->sendData('setWebhook', $params);
    }
    
    /**
     * Webhook o'chirish
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): array
    {
        return $this->sendData('deleteWebhook', [
            'drop_pending_updates' => $dropPendingUpdates
        ]);
    }
    
    /**
     * Webhook ma'lumotlarini olish
     */
    public function getWebhookInfo(): array
    {
        return $this->sendData('getWebhookInfo');
    }
    
    /**
     * Xabar yuborish
     */
    public function sendMessage(
        $chatId, 
        string $text, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['text'] = $text;
        
        return $this->sendData('sendMessage', $params);
    }
    
    /**
     * Rasm yuborish
     */
    public function sendPhoto(
        $chatId, 
        $photo, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['photo'] = $photo;
        
        return $this->sendData('sendPhoto', $params);
    }
    
    /**
     * Video yuborish
     */
    public function sendVideo(
        $chatId, 
        $video, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['video'] = $video;
        
        return $this->sendData('sendVideo', $params);
    }
    
    /**
     * Audio yuborish
     */
    public function sendAudio(
        $chatId, 
        $audio, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['audio'] = $audio;
        
        return $this->sendData('sendAudio', $params);
    }
    
    /**
     * Hujjat yuborish
     */
    public function sendDocument(
        $chatId, 
        $document, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['document'] = $document;
        
        return $this->sendData('sendDocument', $params);
    }
    
    /**
     * Joylashuv yuborish
     */
    public function sendLocation(
        $chatId, 
        float $latitude, 
        float $longitude, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['latitude'] = $latitude;
        $params['longitude'] = $longitude;
        
        return $this->sendData('sendLocation', $params);
    }
    
    /**
     * Kontakt yuborish
     */
    public function sendContact(
        $chatId, 
        string $phoneNumber, 
        string $firstName, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['phone_number'] = $phoneNumber;
        $params['first_name'] = $firstName;
        
        return $this->sendData('sendContact', $params);
    }
    
    /**
     * So'rovnoma yuborish
     */
    public function sendPoll(
        $chatId, 
        string $question, 
        array $options, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['question'] = $question;
        $params['options'] = $options;
        
        return $this->sendData('sendPoll', $params);
    }
    
    /**
     * Typing action yuborish
     */
    public function sendChatAction($chatId, string $action): array
    {
        return $this->sendData('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action
        ]);
    }
    
    /**
     * Xabarni tahrirlash
     */
    public function editMessageText(
        $chatId, 
        int $messageId, 
        string $text, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['message_id'] = $messageId;
        $params['text'] = $text;
        
        return $this->sendData('editMessageText', $params);
    }
    
    /**
     * Xabarni o'chirish
     */
    public function deleteMessage($chatId, int $messageId): array
    {
        return $this->sendData('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ]);
    }
    
    /**
     * Callback query javob berish
     */
    public function answerCallbackQuery(
        string $callbackQueryId, 
        array $params = []
    ): array {
        $params['callback_query_id'] = $callbackQueryId;
        
        return $this->sendData('answerCallbackQuery', $params);
    }
    
    /**
     * Inline query javob berish
     */
    public function answerInlineQuery(
        string $inlineQueryId, 
        array $results, 
        array $params = []
    ): array {
        $params['inline_query_id'] = $inlineQueryId;
        $params['results'] = $results;
        
        return $this->sendData('answerInlineQuery', $params);
    }
    
    /**
     * Bot ma'lumotlarini olish
     */
    public function getMe(): array
    {
        return $this->sendData('getMe');
    }
    
    /**
     * Chat ma'lumotlarini olish
     */
    public function getChat($chatId): array
    {
        return $this->sendData('getChat', ['chat_id' => $chatId]);
    }
    
    /**
     * Chat a'zolari sonini olish
     */
    public function getChatMemberCount($chatId): array
    {
        return $this->sendData('getChatMemberCount', ['chat_id' => $chatId]);
    }
    
    /**
     * Chat a'zosi ma'lumotlarini olish
     */
    public function getChatMember($chatId, int $userId): array
    {
        return $this->sendData('getChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId
        ]);
    }
    
    /**
     * Foydalanuvchini ban qilish
     */
    public function banChatMember(
        $chatId, 
        int $userId, 
        array $params = []
    ): array {
        $params['chat_id'] = $chatId;
        $params['user_id'] = $userId;
        
        return $this->sendData('banChatMember', $params);
    }
    
    /**
     * Foydalanuvchi banini ochish
     */
    public function unbanChatMember(
        $chatId, 
        int $userId, 
        bool $onlyIfBanned = true
    ): array {
        return $this->sendData('unbanChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'only_if_banned' => $onlyIfBanned
        ]);
    }
    
    /**
     * Asosiy API so'rov yuborish funksiyasi
     */
    public function sendData(string $method, array $params = []): array
    {
        $url = $this->apiUrl . $this->token . '/' . $method;
        
        // Keyboard obyektini array ga aylantirish
        if (isset($params['reply_markup']) && $params['reply_markup'] instanceof Keyboard) {
            $params['reply_markup'] = $params['reply_markup']->build();
        }
        
        // Fayl yuklash tekshiruvi
        $hasFile = $this->hasFile($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        if ($hasFile) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new TelegramException('cURL error: ' . $error);
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TelegramException('Invalid JSON response from Telegram API');
        }
        
        $this->lastResponse = $result;
        
        if (!isset($result['ok']) || $result['ok'] !== true) {
            $errorMessage = $result['description'] ?? 'Unknown error';
            throw new TelegramException('Telegram API error: ' . $errorMessage, $httpCode);
        }
        
        return $result;
    }
    
    /**
     * Parametrlarda fayl borligini tekshirish
     */
    private function hasFile(array $params): bool
    {
        foreach ($params as $value) {
            if ($value instanceof \CURLFile) {
                return true;
            }
            if (is_string($value) && strpos($value, '@') === 0) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Oxirgi javobni olish
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }
    
    /**
     * Timeout o'rnatish
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
}