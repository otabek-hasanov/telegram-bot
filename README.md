# Telegram Bot SDK for PHP

[![Latest Version](https://img.shields.io/packagist/v/stoyishi/telegram-bot-sdk.svg)](https://packagist.org/packages/stoyishi/telegram-bot)
[![PHP Version](https://img.shields.io/packagist/php-v/stoyishi/telegram-bot.svg)](https://packagist.org/packages/stoyishi/telegram-bot)
[![License](https://img.shields.io/packagist/l/stoyishi/telegram-bot.svg)](https://packagist.org/packages/stoyishi/telegram-bot)

Telegram Bot API uchun sodda va kuchli PHP SDK. PHP 7.4+ versiyalarida ishlaydi.

## 📑 Mundarija

- [O'rnatish](#ornatish)
- [Tezkor Boshlash](#tezkor-boshlash)
- [Asosiy Funksiyalar](#asosiy-funksiyalar)
  - [Xabar Yuborish](#xabar-yuborish)
  - [Klaviaturalar](#klaviaturalar)
  - [Media Fayllar](#media-fayllar)
  - [Chat Boshqaruvi](#chat-boshqaruvi)
- [Webhook O'rnatish](#webhook-ornatish)
- [To'liq Misollar](#toliq-misollar)
- [Testlar](#testlar)
- [Litsenziya](#litsenziya)

## O'rnatish

Composer orqali o'rnating:
```bash
composer require stoyishi/telegram-bot
```

## Tezkor Boshlash

### Webhook usuli
```php
<?php

require_once 'vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

$bot = new Client("YOUR_BOT_TOKEN");

$update = $bot->getWebhookUpdates();
$message = $update->getMessage();

if ($message) {
    $text = $message->text;
    $chatId = $message->chat->id;
    
    if ($text === '/start') {
        $bot->sendMessage($chatId, "Salom! Botga xush kelibsiz! 👋");
    }
}
```

### Long Polling usuli
```php
<?php

require_once 'vendor/autoload.php';

use Stoyishi\Bot\Client;

$bot = new Client("YOUR_BOT_TOKEN");

$offset = 0;

while (true) {
    $updates = $bot->getUpdates($offset, 100, 30);
    
    foreach ($updates as $update) {
        $offset = $update->getUpdateId() + 1;
        
        if ($update->hasMessage()) {
            $message = $update->getMessage();
            $chatId = $message->chat->id;
            $text = $message->text;
            
            $bot->sendMessage($chatId, "Echo: " . $text);
        }
    }
}
```

## Asosiy Funksiyalar

### Xabar Yuborish

#### Oddiy xabar
```php
$bot->sendMessage($chatId, "Salom dunyo!");
```

#### HTML formatlashtirish bilan
```php
$bot->sendMessage($chatId, "<b>Qalin</b> va <i>qiya</i> matn", [
    'parse_mode' => 'HTML'
]);
```

#### Markdown formatlashtirish
```php
$bot->sendMessage($chatId, "*Qalin* va _qiya_ matn", [
    'parse_mode' => 'Markdown'
]);
```

#### Link preview o'chirish
```php
$bot->sendMessage($chatId, "Bu yerda link: https://example.com", [
    'disable_web_page_preview' => true
]);
```

#### Notification o'chirish
```php
$bot->sendMessage($chatId, "Ovozli bildirishnoma bo'lmaydi", [
    'disable_notification' => true
]);
```

### Klaviaturalar

#### Inline Klaviatura
```php
use Stoyishi\Bot\Keyboard;

// URL tugmalari
$keyboard = new Keyboard('inline');
$keyboard->rows(
    ["Google" => ["url" => "https://google.com"]],
    ["GitHub" => ["url" => "https://github.com"]]
);

$bot->sendMessage($chatId, "Tanlang:", [
    'reply_markup' => $keyboard
]);
```

#### Callback data bilan
```php
$keyboard = new Keyboard('inline');
$keyboard->rows(
    [
        "Like ❤️" => ["callback_data" => "like"],
        "Dislike 👎" => ["callback_data" => "dislike"]
    ],
    ["Ko'proq" => ["callback_data" => "more"]]
);

$bot->sendMessage($chatId, "Reaksiyangizni bildiring:", [
    'reply_markup' => $keyboard
]);
```

#### Switch inline query
```php
$keyboard = new Keyboard('inline');
$keyboard->rows(
    ["Ulashish" => ["switch_inline_query" => "Bu matnni ulashing!"]],
    ["Shu chatda ulashish" => ["switch_inline_query_current_chat" => "Matn"]]
);
```

#### Reply Klaviatura
```php
$keyboard = new Keyboard('resize');
$keyboard->rows(
    ["Tugma 1", "Tugma 2"],
    ["Tugma 3", "Tugma 4"]
);

$bot->sendMessage($chatId, "Tanlang:", [
    'reply_markup' => $keyboard
]);
```

#### Kontakt va joylashuv tugmalari
```php
$keyboard = new Keyboard('resize');
$keyboard->rows(
    ["📱 Raqamni yuborish" => ["request_contact" => true]],
    ["📍 Joylashuvni yuborish" => ["request_location" => true]]
);

$bot->sendMessage($chatId, "Ma'lumot yuboring:", [
    'reply_markup' => $keyboard
]);
```

#### One-time klaviatura
```php
$keyboard = new Keyboard('resize');
$keyboard->setOneTime(true);
$keyboard->rows(
    ["Ha", "Yo'q"]
);

$bot->sendMessage($chatId, "Rozimisiz?", [
    'reply_markup' => $keyboard
]);
```

#### Input field placeholder
```php
$keyboard = new Keyboard('resize');
$keyboard->setInputFieldPlaceholder('Bu yerga yozing...');
$keyboard->rows(
    ["Tugma 1", "Tugma 2"]
);
```

#### Klaviaturani o'chirish
```php
$keyboard = new Keyboard('remove');

$bot->sendMessage($chatId, "Klaviatura o'chirildi", [
    'reply_markup' => $keyboard
]);
```

### Media Fayllar

#### Rasm Yuborish
```php
// URL orqali
$bot->sendPhoto($chatId, "https://example.com/image.jpg", [
    'caption' => 'Bu rasm'
]);

// Mahalliy fayl
use Stoyishi\Bot\Helpers\FileUploader;

$photo = FileUploader::createFromPath('/path/to/photo.jpg');
$bot->sendPhoto($chatId, $photo, [
    'caption' => 'Mahalliy fayl'
]);

// Base64 dan
$photo = FileUploader::createFromBase64($base64Data, 'image.png', 'image/png');
$bot->sendPhoto($chatId, $photo);

// URL dan yuklab olish
$photo = FileUploader::createFromUrl('https://example.com/photo.jpg');
$bot->sendPhoto($chatId, $photo);
```

#### Video Yuborish
```php
$bot->sendVideo($chatId, "https://example.com/video.mp4", [
    'caption' => 'Bu video',
    'supports_streaming' => true,
    'duration' => 60,
    'width' => 1920,
    'height' => 1080
]);
```

#### Audio Yuborish
```php
$audio = FileUploader::createFromPath('/path/to/audio.mp3');
$bot->sendAudio($chatId, $audio, [
    'caption' => 'Qo\'shiq',
    'performer' => 'Artist',
    'title' => 'Song Title',
    'duration' => 180
]);
```

#### Hujjat Yuborish
```php
$document = FileUploader::createFromPath('/path/to/document.pdf');
$bot->sendDocument($chatId, $document, [
    'caption' => 'PDF hujjat'
]);
```

#### Ovozli Xabar
```php
$voice = FileUploader::createFromPath('/path/to/voice.ogg');
$bot->sendData('sendVoice', [
    'chat_id' => $chatId,
    'voice' => $voice,
    'duration' => 10
]);
```

#### GIF Animatsiya
```php
$bot->sendData('sendAnimation', [
    'chat_id' => $chatId,
    'animation' => 'https://example.com/animation.gif',
    'caption' => 'GIF animatsiya'
]);
```

#### Media Group (Albom)
```php
$media = [
    [
        'type' => 'photo',
        'media' => 'https://example.com/photo1.jpg',
        'caption' => 'Birinchi rasm'
    ],
    [
        'type' => 'photo',
        'media' => 'https://example.com/photo2.jpg',
        'caption' => 'Ikkinchi rasm'
    ]
];

$bot->sendData('sendMediaGroup', [
    'chat_id' => $chatId,
    'media' => json_encode($media)
]);
```

### Joylashuv va Kontakt

#### Joylashuv Yuborish
```php
$bot->sendLocation($chatId, 41.2995, 69.2401, [
    'live_period' => 900 // 15 daqiqa jonli joylashuv
]);
```

#### Kontakt Yuborish
```php
$bot->sendContact($chatId, "+998901234567", "Ismi", [
    'last_name' => 'Familiyasi'
]);
```

### So'rovnoma
```php
$bot->sendPoll($chatId, "Sevimli dasturlash tilingiz?", [
    "PHP",
    "Python",
    "JavaScript",
    "Java"
], [
    'is_anonymous' => false,
    'allows_multiple_answers' => true,
    'type' => 'regular'
]);

// Quiz turi
$bot->sendPoll($chatId, "2 + 2 = ?", [
    "3",
    "4",
    "5",
    "6"
], [
    'type' => 'quiz',
    'correct_option_id' => 1,
    'explanation' => 'To\'g\'ri javob 4'
]);
```

### Chat Action (Typing)
```php
// Typing ko'rsatish
$bot->sendChatAction($chatId, 'typing');

// Rasm yuklanmoqda
$bot->sendChatAction($chatId, 'upload_photo');

// Video yuklanmoqda
$bot->sendChatAction($chatId, 'upload_video');

// Hujjat yuklanmoqda
$bot->sendChatAction($chatId, 'upload_document');

// Ovozli xabar yozilmoqda
$bot->sendChatAction($chatId, 'record_voice');

// Video xabar yozilmoqda
$bot->sendChatAction($chatId, 'record_video');
```

### Xabarni Tahrirlash
```php
// Matnni tahrirlash
$bot->editMessageText($chatId, $messageId, "Yangi matn", [
    'parse_mode' => 'HTML'
]);

// Klaviaturani tahrirlash
$keyboard = new Keyboard('inline');
$keyboard->rows(
    ["Yangi tugma" => ["callback_data" => "new"]]
);

$bot->sendData('editMessageReplyMarkup', [
    'chat_id' => $chatId,
    'message_id' => $messageId,
    'reply_markup' => $keyboard->build()
]);

// Caption ni tahrirlash
$bot->sendData('editMessageCaption', [
    'chat_id' => $chatId,
    'message_id' => $messageId,
    'caption' => 'Yangi caption'
]);
```

### Xabarni O'chirish
```php
$bot->deleteMessage($chatId, $messageId);
```

### Xabarni Forward qilish
```php
$bot->sendData('forwardMessage', [
    'chat_id' => $chatId,
    'from_chat_id' => $fromChatId,
    'message_id' => $messageId
]);
```

### Xabarni Copy qilish
```php
$bot->sendData('copyMessage', [
    'chat_id' => $chatId,
    'from_chat_id' => $fromChatId,
    'message_id' => $messageId
]);
```

### Callback Query Javob Berish
```php
if ($update->hasCallbackQuery()) {
    $callback = $update->getCallbackQuery();
    
    // Oddiy javob
    $bot->answerCallbackQuery($callback->id, [
        'text' => 'Tugma bosildi!'
    ]);
    
    // Alert bilan
    $bot->answerCallbackQuery($callback->id, [
        'text' => 'Bu muhim xabar!',
        'show_alert' => true
    ]);
    
    // URL ochish
    $bot->answerCallbackQuery($callback->id, [
        'url' => 'https://example.com'
    ]);
}
```

### Inline Query
```php
if ($update->hasInlineQuery()) {
    $inlineQuery = $update->getInlineQuery();
    $query = $inlineQuery->getQuery();
    
    $results = [
        [
            'type' => 'article',
            'id' => '1',
            'title' => 'Birinchi natija',
            'description' => 'Tavsif',
            'input_message_content' => [
                'message_text' => 'Xabar matni'
            ]
        ],
        [
            'type' => 'photo',
            'id' => '2',
            'photo_url' => 'https://example.com/photo.jpg',
            'thumb_url' => 'https://example.com/thumb.jpg',
            'title' => 'Rasm'
        ]
    ];
    
    $bot->answerInlineQuery($inlineQuery->getId(), $results, [
        'cache_time' => 300
    ]);
}
```

## Webhook O'rnatish

### Webhook O'rnatish
```php
// Oddiy webhook
$bot->setWebhook("https://example.com/webhook.php");

// Parametrlar bilan
$bot->setWebhook("https://example.com/webhook.php", [
    'max_connections' => 100,
    'allowed_updates' => ['message', 'callback_query']
]);

// Self-signed sertifikat bilan
$certificate = FileUploader::createFromPath('/path/to/cert.pem');
$bot->setWebhook("https://example.com/webhook.php", [
    'certificate' => $certificate
]);
```

### Webhook O'chirish
```php
// Oddiy o'chirish
$bot->deleteWebhook();

// Kutilayotgan yangilanishlarni ham o'chirish
$bot->deleteWebhook(true);
```

### Webhook Ma'lumotlarini Olish
```php
$info = $bot->getWebhookInfo();

echo "URL: " . $info['result']['url'];
echo "Pending: " . $info['result']['pending_update_count'];
echo "Last error: " . ($info['result']['last_error_message'] ?? 'Yo\'q');
```

## Chat Boshqaruvi

### Chat Ma'lumotlarini Olish
```php
$chat = $bot->getChat($chatId);

echo "Title: " . $chat['result']['title'];
echo "Type: " . $chat['result']['type'];
echo "Members: " . ($chat['result']['member_count'] ?? 'N/A');
```

### A'zolar Sonini Olish
```php
$count = $bot->getChatMemberCount($chatId);
echo "A'zolar soni: " . $count['result'];
```

### Chat A'zosi Haqida Ma'lumot
```php
$member = $bot->getChatMember($chatId, $userId);

echo "Status: " . $member['result']['status'];
echo "User: " . $member['result']['user']['first_name'];
```

### Foydalanuvchini Ban Qilish
```php
// Doimiy ban
$bot->banChatMember($chatId, $userId);

// Muddatli ban (1 soatga)
$bot->banChatMember($chatId, $userId, [
    'until_date' => time() + 3600
]);

// Xabarlarni o'chirmasdan ban
$bot->banChatMember($chatId, $userId, [
    'revoke_messages' => false
]);
```

### Ban Ochish
```php
$bot->unbanChatMember($chatId, $userId);
```

### Cheklovlar O'rnatish
```php
$bot->sendData('restrictChatMember', [
    'chat_id' => $chatId,
    'user_id' => $userId,
    'permissions' => [
        'can_send_messages' => false,
        'can_send_media_messages' => false,
        'can_send_polls' => false,
        'can_send_other_messages' => false,
        'can_add_web_page_previews' => false,
        'can_change_info' => false,
        'can_invite_users' => false,
        'can_pin_messages' => false
    ],
    'until_date' => time() + 3600 // 1 soatga
]);
```

### Admin Qilish
```php
$bot->sendData('promoteChatMember', [
    'chat_id' => $chatId,
    'user_id' => $userId,
    'can_manage_chat' => true,
    'can_change_info' => true,
    'can_delete_messages' => true,
    'can_invite_users' => true,
    'can_restrict_members' => true,
    'can_pin_messages' => true,
    'can_promote_members' => false
]);
```

### Chat Title O'zgartirish
```php
$bot->sendData('setChatTitle', [
    'chat_id' => $chatId,
    'title' => 'Yangi nom'
]);
```

### Chat Photo O'rnatish
```php
$photo = FileUploader::createFromPath('/path/to/photo.jpg');
$bot->sendData('setChatPhoto', [
    'chat_id' => $chatId,
    'photo' => $photo
]);
```

### Xabarni Pin qilish
```php
$bot->sendData('pinChatMessage', [
    'chat_id' => $chatId,
    'message_id' => $messageId,
    'disable_notification' => false
]);
```

### Xabarni Unpin qilish
```php
$bot->sendData('unpinChatMessage', [
    'chat_id' => $chatId,
    'message_id' => $messageId
]);
```

## Bot Ma'lumotlarini Olish
```php
$me = $bot->getMe();

echo "Bot ID: " . $me['result']['id'];
echo "Bot username: @" . $me['result']['username'];
echo "Bot name: " . $me['result']['first_name'];
echo "Can join groups: " . ($me['result']['can_join_groups'] ? 'Ha' : 'Yo\'q');
```

## Xatoliklar Bilan Ishlash
```php
use Stoyishi\Bot\Exceptions\TelegramException;

try {
    $bot->sendMessage($chatId, "Xabar");
} catch (TelegramException $e) {
    echo "Telegram xatolik: " . $e->getMessage();
    echo "Kod: " . $e->getCode();
    
    // Oxirgi javobni ko'rish
    $response = $bot->getLastResponse();
    print_r($response);
}
```

## To'liq Misollar

### Webhook Bot
```php
<?php

require_once 'vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

$bot = new Client("YOUR_BOT_TOKEN");

try {
    $update = $bot->getWebhookUpdates();
    
    if ($update->hasMessage()) {
        $message = $update->getMessage();
        $text = $message->text;
        $chatId = $message->chat->id;
        $userName = $message->from->getFullName();
        
        switch ($text) {
            case '/start':
                $keyboard = new Keyboard('resize');
                $keyboard->rows(
                    ["📋 Menyu", "ℹ️ Ma'lumot"],
                    ["📱 Kontakt", "📍 Joylashuv"]
                );
                
                $bot->sendMessage($chatId, "Salom, {$userName}! 👋", [
                    'reply_markup' => $keyboard
                ]);
                break;
                
            case '📋 Menyu':
                $keyboard = new Keyboard('inline');
                $keyboard->rows(
                    ["📸 Rasm" => ["callback_data" => "photo"]],
                    ["🎥 Video" => ["callback_data" => "video"]],
                    ["📄 Hujjat" => ["callback_data" => "document"]]
                );
                
                $bot->sendMessage($chatId, "Tanlang:", [
                    'reply_markup' => $keyboard
                ]);
                break;
                
            case 'ℹ️ Ma\'lumot':
                $bot->sendMessage($chatId, "Bot haqida ma'lumot...");
                break;
                
            case '📱 Kontakt':
                $keyboard = new Keyboard('resize');
                $keyboard->rows(
                    ["Raqamni yuborish" => ["request_contact" => true]],
                    ["🔙 Orqaga"]
                );
                
                $bot->sendMessage($chatId, "Raqamingizni yuboring:", [
                    'reply_markup' => $keyboard
                ]);
                break;
                
            case '📍 Joylashuv':
                $keyboard = new Keyboard('resize');
                $keyboard->rows(
                    ["Joylashuvni yuborish" => ["request_location" => true]],
                    ["🔙 Orqaga"]
                );
                
                $bot->sendMessage($chatId, "Joylashuvingizni yuboring:", [
                    'reply_markup' => $keyboard
                ]);
                break;
                
            default:
                $bot->sendMessage($chatId, "Echo: {$text}");
        }
    }
    
    if ($update->hasCallbackQuery()) {
        $callback = $update->getCallbackQuery();
        $data = $callback->getData();
        $chatId = $callback->message->chat->id;
        
        $bot->answerCallbackQuery($callback->id, [
            'text' => 'Tugma bosildi: ' . $data
        ]);
        
        if ($data === 'photo') {
            $bot->sendPhoto($chatId, 'https://picsum.photos/800/600');
        }
    }
    
} catch (\Exception $e) {
    error_log("Bot error: " . $e->getMessage());
}
```

### Long Polling Bot

`examples/long-polling.php` faylida to'liq misol mavjud.

### Murakkab Bot (State Management)

`examples/advanced-bot.php` faylida to'liq misol mavjud.

### Fayllar Yuklash

`examples/file-upload.php` faylida to'liq misol mavjud.

## Testlar

PHPUnit testlarni ishga tushirish:
```bash
# Testlarni ishga tushirish
./vendor/bin/phpunit

# Coverage bilan
./vendor/bin/phpunit --coverage-html coverage
```

## Talablar

- PHP >= 7.4
- ext-json
- ext-curl

## Litsenziya

MIT License. Batafsil ma'lumot uchun [LICENSE](LICENSE) faylini ko'ring.

## Muallif

- **Otabek Hasanov** - [GitHub](https://github.com/otabek-hasanov)

## Qo'llab-quvvatlash

Agar sizda savollar yoki muammolar bo'lsa:

- 📧 Email: otabek@hasanov.uz
- 🐛 Issues: [GitHub Issues](https://github.com/otabek-hasanov/telegram-bot/issues)

## Foydali Havolalar

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [Packagist](https://packagist.org/packages/stoyishi/telegram-bot)
- [GitHub Repository](https://github.com/otabek-hasanov/telegram-bot)

## Changelog

Barcha muhim o'zgarishlar [CHANGELOG.md](CHANGELOG.md) faylida qayd etiladi.

---

<p align="center">Made with ❤️ by <a href="https://github.com/otabek-hasanov">Otabek Hasanov</a></p>
