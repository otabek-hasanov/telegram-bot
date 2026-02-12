<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

// Bot tokenini kiriting
$bot = new Client("YOUR_BOT_TOKEN");

echo "Bot ishga tushdi. Yangilanishlar kutilmoqda...\n";

$offset = 0;

while (true) {
    try {
        $updates = $bot->getUpdates($offset, 100, 30);
        
        foreach ($updates as $update) {
            $offset = $update->getUpdateId() + 1;
            
            if ($update->hasMessage()) {
                $message = $update->getMessage();
                $text = $message->text;
                $chatId = $message->chat->id;
                $userName = $message->from->getFullName();
                
                echo "[{$userName}]: {$text}\n";
                
                if ($text === '/start') {
                    $bot->sendMessage($chatId, "Salom, {$userName}! 👋");
                } elseif ($text === '/help') {
                    $helpText = "📋 Mavjud komandalar:\n\n";
                    $helpText .= "/start - Botni ishga tushirish\n";
                    $helpText .= "/help - Yordam\n";
                    $helpText .= "/inline - Inline klaviatura\n";
                    $helpText .= "/keyboard - Reply klaviatura\n";
                    $helpText .= "/photo - Rasm yuborish\n";
                    
                    $bot->sendMessage($chatId, $helpText);
                } elseif ($text === '/inline') {
                    $keyboard = new Keyboard('inline');
                    $keyboard->rows(
                        ["GitHub" => ["url" => "https://github.com"]],
                        ["Telegram" => ["url" => "https://t.me"]],
                        [
                            "✅ Yes" => ["callback_data" => "yes"],
                            "❌ No" => ["callback_data" => "no"]
                        ]
                    );
                    
                    $bot->sendMessage($chatId, "Tanlang:", [
                        'reply_markup' => $keyboard
                    ]);
                } elseif ($text === '/keyboard') {
                    $keyboard = new Keyboard('resize');
                    $keyboard->setOneTime(true);
                    $keyboard->rows(
                        ["📱 Raqamni yuborish" => ["request_contact" => true]],
                        ["📍 Joylashuvni yuborish" => ["request_location" => true]],
                        ["🔙 Orqaga"]
                    );
                    
                    $bot->sendMessage($chatId, "Tanlang:", [
                        'reply_markup' => $keyboard
                    ]);
                } elseif ($text === '🔙 Orqaga') {
                    $keyboard = new Keyboard('remove');
                    $bot->sendMessage($chatId, "Asosiy menyu", [
                        'reply_markup' => $keyboard
                    ]);
                } elseif ($text === '/photo') {
                    $bot->sendChatAction($chatId, 'upload_photo');
                    sleep(1);
                    $bot->sendPhoto($chatId, 'https://picsum.photos/800/600', [
                        'caption' => '📸 Tasodifiy rasm'
                    ]);
                } else {
                    $bot->sendMessage($chatId, "Echo: {$text}");
                }
            }
            
            if ($update->hasCallbackQuery()) {
                $callback = $update->getCallbackQuery();
                $data = $callback->getData();
                
                $bot->answerCallbackQuery($callback->id, [
                    'text' => "Siz {$data} tugmasini bosdingiz"
                ]);
            }
        }
        
    } catch (\Exception $e) {
        echo "Xatolik: " . $e->getMessage() . "\n";
        sleep(5);
    }
}