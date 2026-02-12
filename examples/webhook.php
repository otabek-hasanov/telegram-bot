<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

// Bot tokenini kiriting
$bot = new Client("YOUR_BOT_TOKEN");

try {
    // Webhook orqali yangilanishni olish
    $update = $bot->getWebhookUpdates();
    
    // Oddiy xabar
    if ($update->hasMessage()) {
        $message = $update->getMessage();
        $text = $message->text;
        $chatId = $message->chat->id;
        
        // /start komandasi
        if ($text === '/start') {
            $bot->sendMessage($chatId, "Assalomu alaykum! Botga xush kelibsiz!");
        }
        
        // /inline komandasi
        elseif ($text === '/inline') {
            $keyboard = new Keyboard('inline');
            $keyboard->rows(
                ["Google" => ["url" => "https://google.com"]],
                ["Telegram" => ["url" => "https://t.me"]],
                ["Ma'lumot olish" => ["callback_data" => "info"]],
                [
                    "Like ❤️" => ["callback_data" => "like"],
                    "Dislike 👎" => ["callback_data" => "dislike"]
                ]
            );
            
            $bot->sendMessage($chatId, "Inline klaviatura:", [
                'reply_markup' => $keyboard
            ]);
        }
        
        // /resize komandasi
        elseif ($text === '/resize') {
            $keyboard = new Keyboard('resize');
            $keyboard->rows(
                ["Tugma 1", "Tugma 2"],
                ["Tugma 3", "Tugma 4"],
                ["Raqamni yuborish" => ["request_contact" => true]],
                ["Joylashuvni yuborish" => ["request_location" => true]]
            );
            
            $bot->sendMessage($chatId, "Reply klaviatura:", [
                'reply_markup' => $keyboard
            ]);
        }
        
        // /remove komandasi
        elseif ($text === '/remove') {
            $keyboard = new Keyboard('remove');
            
            $bot->sendMessage($chatId, "Klaviatura o'chirildi", [
                'reply_markup' => $keyboard
            ]);
        }
        
        // /photo komandasi
        elseif ($text === '/photo') {
            $bot->sendChatAction($chatId, 'upload_photo');
            $bot->sendPhoto($chatId, 'https://picsum.photos/800/600', [
                'caption' => 'Tasodifiy rasm'
            ]);
        }
        
        // Oddiy echo
        else {
            $bot->sendMessage($chatId, "Siz yozdingiz: " . $text);
        }
    }
    
    // Callback query
    if ($update->hasCallbackQuery()) {
        $callback = $update->getCallbackQuery();
        $data = $callback->getData();
        $chatId = $callback->message->chat->id;
        $messageId = $callback->message->messageId;
        
        if ($data === 'info') {
            $bot->answerCallbackQuery($callback->id, [
                'text' => 'Bu ma\'lumot tugmasi',
                'show_alert' => true
            ]);
        } elseif ($data === 'like') {
            $bot->answerCallbackQuery($callback->id, [
                'text' => 'Rahmat! ❤️'
            ]);
            
            $bot->editMessageText($chatId, $messageId, "Sizga yoqdi! ❤️");
        } elseif ($data === 'dislike') {
            $bot->answerCallbackQuery($callback->id, [
                'text' => 'Kechirasiz 😔'
            ]);
            
            $bot->editMessageText($chatId, $messageId, "Yoqmadi 👎");
        }
    }
    
} catch (\Exception $e) {
    // Xatolikni log qilish
    error_log("Bot error: " . $e->getMessage());
}