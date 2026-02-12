<?php
/**
 * Advanced Bot Example - Murakkab bot funksiyalari misoli
 * Bu faylda state management, anketa, va boshqa ilg'or funksiyalar ko'rsatilgan
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

$bot = new Client("YOUR_BOT_TOKEN");

// Session saqlash uchun
session_start();

try {
    $update = $bot->getWebhookUpdates();
    
    if ($update->hasMessage()) {
        $message = $update->getMessage();
        $chatId = $message->chat->id;
        $userId = $message->from->id;
        $text = $message->text;
        $userName = $message->from->getFullName();
        
        // Foydalanuvchi holatini saqlash
        if (!isset($_SESSION['user_' . $userId])) {
            $_SESSION['user_' . $userId] = [
                'state' => 'start',
                'data' => [],
                'language' => 'uz'
            ];
        }
        
        $userState = &$_SESSION['user_' . $userId];
        
        // ==================== KOMANDALAR ====================
        
        if ($text === '/start') {
            $userState['state'] = 'main_menu';
            
            $keyboard = new Keyboard('inline');
            $keyboard->rows(
                ["📝 Anketa to'ldirish" => ["callback_data" => "start_form"]],
                ["📊 Statistika" => ["callback_data" => "stats"]],
                ["⚙️ Sozlamalar" => ["callback_data" => "settings"]],
                ["ℹ️ Yordam" => ["callback_data" => "help"]]
            );
            
            $welcomeText = "👋 Salom, {$userName}!\n\n";
            $welcomeText .= "Men sizga yordam beruvchi botman.\n";
            $welcomeText .= "Quyidagi tugmalardan birini tanlang:";
            
            $bot->sendMessage($chatId, $welcomeText, [
                'reply_markup' => $keyboard
            ]);
        }
        
        elseif ($text === '/cancel') {
            $userState['state'] = 'main_menu';
            $userState['data'] = [];
            
            $keyboard = new Keyboard('remove');
            
            $bot->sendMessage($chatId, "❌ Amal bekor qilindi.\n/start tugmasini bosing.", [
                'reply_markup' => $keyboard
            ]);
        }
        
        elseif ($text === '/stats') {
            // Statistikani ko'rsatish
            $totalUsers = count(glob(session_save_path() . '/sess_*'));
            
            $stats = "📊 *Bot Statistikasi*\n\n";
            $stats .= "👥 Foydalanuvchilar: {$totalUsers}\n";
            $stats .= "🆔 Sizning ID: `{$userId}`\n";
            $stats .= "👤 Ism: {$userName}\n";
            $stats .= "🌐 Til: " . strtoupper($userState['language']);
            
            $bot->sendMessage($chatId, $stats, [
                'parse_mode' => 'Markdown'
            ]);
        }
        
        elseif ($text === '/help') {
            $help = "ℹ️ *Yordam*\n\n";
            $help .= "*Mavjud komandalar:*\n";
            $help .= "/start - Botni boshlash\n";
            $help .= "/cancel - Amalni bekor qilish\n";
            $help .= "/stats - Statistika\n";
            $help .= "/help - Yordam\n\n";
            $help .= "*Funksiyalar:*\n";
            $help .= "• Anketa to'ldirish\n";
            $help .= "• Rasm va fayllar yuklash\n";
            $help .= "• Joylashuv va kontakt yuborish\n";
            $help .= "• Inline va reply klaviaturalar";
            
            $bot->sendMessage($chatId, $help, [
                'parse_mode' => 'Markdown'
            ]);
        }
        
        // ==================== ANKETA JARAYONI ====================
        
        elseif ($userState['state'] === 'waiting_name') {
            if (strlen($text) < 2) {
                $bot->sendMessage($chatId, "❌ Ism juda qisqa. Iltimos, to'liq ismingizni kiriting:");
                return;
            }
            
            $userState['data']['name'] = $text;
            $userState['state'] = 'waiting_age';
            
            $bot->sendMessage($chatId, "✅ Ism qabul qilindi: {$text}\n\nEndi yoshingizni kiriting:");
        }
        
        elseif ($userState['state'] === 'waiting_age') {
            if (!is_numeric($text) || $text < 1 || $text > 150) {
                $bot->sendMessage($chatId, "❌ Iltimos, to'g'ri yosh kiriting (1-150):");
                return;
            }
            
            $userState['data']['age'] = (int)$text;
            $userState['state'] = 'waiting_city';
            
            $bot->sendMessage($chatId, "✅ Yosh qabul qilindi: {$text}\n\nShaharingizni kiriting:");
        }
        
        elseif ($userState['state'] === 'waiting_city') {
            if (strlen($text) < 2) {
                $bot->sendMessage($chatId, "❌ Shahar nomi juda qisqa:");
                return;
            }
            
            $userState['data']['city'] = $text;
            $userState['state'] = 'waiting_phone';
            
            $keyboard = new Keyboard('resize');
            $keyboard->setOneTime(true);
            $keyboard->rows(
                ["📱 Raqamni yuborish" => ["request_contact" => true]],
                ["⏭️ O'tkazib yuborish"]
            );
            
            $bot->sendMessage($chatId, 
                "✅ Shahar qabul qilindi: {$text}\n\nTelefon raqamingizni yuboring yoki o'tkazib yuboring:",
                ['reply_markup' => $keyboard]
            );
        }
        
        elseif ($userState['state'] === 'waiting_phone') {
            if ($text === '⏭️ O\'tkazib yuborish') {
                $userState['data']['phone'] = 'Kiritilmagan';
            } else {
                $userState['data']['phone'] = $text;
            }
            
            $userState['state'] = 'waiting_email';
            
            $keyboard = new Keyboard('resize');
            $keyboard->setOneTime(true);
            $keyboard->rows(
                ["⏭️ O'tkazib yuborish"]
            );
            
            $bot->sendMessage($chatId, 
                "Email manzilingizni kiriting yoki o'tkazib yuboring:",
                ['reply_markup' => $keyboard]
            );
        }
        
        elseif ($userState['state'] === 'waiting_email') {
            if ($text === '⏭️ O\'tkazib yuborish') {
                $userState['data']['email'] = 'Kiritilmagan';
            } else {
                if (!filter_var($text, FILTER_VALIDATE_EMAIL)) {
                    $bot->sendMessage($chatId, "❌ Email manzil noto'g'ri. Qaytadan kiriting yoki o'tkazib yuboring:");
                    return;
                }
                $userState['data']['email'] = $text;
            }
            
            $userState['state'] = 'main_menu';
            
            // Ma'lumotlarni ko'rsatish
            $name = $userState['data']['name'];
            $age = $userState['data']['age'];
            $city = $userState['data']['city'];
            $phone = $userState['data']['phone'];
            $email = $userState['data']['email'];
            
            $result = "✅ *Anketa muvaffaqiyatli to'ldirildi!*\n\n";
            $result .= "📋 *Sizning ma'lumotlaringiz:*\n\n";
            $result .= "👤 Ism: {$name}\n";
            $result .= "🎂 Yosh: {$age}\n";
            $result .= "🏙️ Shahar: {$city}\n";
            $result .= "📱 Telefon: {$phone}\n";
            $result .= "📧 Email: {$email}";
            
            $keyboard = new Keyboard('inline');
            $keyboard->rows(
                ["✏️ Tahrirlash" => ["callback_data" => "edit_form"]],
                ["💾 Saqlash" => ["callback_data" => "save_form"]],
                ["🏠 Bosh menyu" => ["callback_data" => "main_menu"]]
            );
            
            $bot->sendMessage($chatId, $result, [
                'parse_mode' => 'Markdown',
                'reply_markup' => $keyboard
            ]);
        }
        
        // ==================== KONTAKT VA JOYLASHUV ====================
        
        // Kontakt qabul qilish
        if (isset($message->from) && property_exists($message, 'contact')) {
            $phone = $message->contact->phoneNumber ?? $message->contact->phone_number;
            $firstName = $message->contact->firstName ?? $message->contact->first_name;
            
            if ($userState['state'] === 'waiting_phone') {
                $userState['data']['phone'] = $phone;
                $userState['state'] = 'waiting_email';
                
                $keyboard = new Keyboard('resize');
                $keyboard->setOneTime(true);
                $keyboard->rows(["⏭️ O'tkazib yuborish"]);
                
                $bot->sendMessage($chatId, 
                    "✅ Raqam qabul qilindi: {$phone}\n\nEmail manzilingizni kiriting yoki o'tkazib yuboring:",
                    ['reply_markup' => $keyboard]
                );
            } else {
                $bot->sendMessage($chatId, 
                    "📱 Raqam qabul qilindi!\n\nIsm: {$firstName}\nRaqam: {$phone}"
                );
            }
        }
        
        // Joylashuv qabul qilish
        if ($message->hasLocation()) {
            $lat = $message->location->latitude;
            $lon = $message->location->longitude;
            
            $bot->sendMessage($chatId,
                "📍 Joylashuv qabul qilindi!\n\n" .
                "Latitude: {$lat}\n" .
                "Longitude: {$lon}\n\n" .
                "Google Maps: https://maps.google.com/?q={$lat},{$lon}"
            );
        }
        
        // ==================== RASM VA FAYLLAR ====================
        
        // Rasm qabul qilish
        if ($message->hasPhoto()) {
            $photos = $message->photo;
            $largestPhoto = end($photos);
            
            $caption = $message->text ?? 'Rasmsiz';
            
            $bot->sendMessage($chatId, 
                "📸 Rasm qabul qilindi!\n\n" .
                "File ID: `{$largestPhoto->fileId}`\n" .
                "Hajm: {$largestPhoto->fileSize} bytes\n" .
                "Caption: {$caption}",
                ['parse_mode' => 'Markdown']
            );
            
            // Rasmni qayta yuborish
            $bot->sendPhoto($chatId, $largestPhoto->fileId, [
                'caption' => 'Sizning rasmingiz qaytarildi'
            ]);
        }
        
        // Hujjat qabul qilish
        if ($message->hasDocument()) {
            $document = $message->document;
            
            $fileName = $document->fileName ?? 'unknown';
            $fileSize = round($document->fileSize / 1024, 2);
            $mimeType = $document->mimeType ?? 'unknown';
            
            $bot->sendMessage($chatId,
                "📄 Hujjat qabul qilindi!\n\n" .
                "Fayl nomi: {$fileName}\n" .
                "Hajm: {$fileSize} KB\n" .
                "Tur: {$mimeType}\n" .
                "File ID: `{$document->fileId}`",
                ['parse_mode' => 'Markdown']
            );
        }
        
        // Video qabul qilish
        if ($message->hasVideo()) {
            $video = $message->video;
            
            $duration = $video->duration;
            $fileSize = round($video->fileSize / 1024 / 1024, 2);
            
            $bot->sendMessage($chatId,
                "🎥 Video qabul qilindi!\n\n" .
                "Davomiyligi: {$duration} soniya\n" .
                "Hajm: {$fileSize} MB\n" .
                "File ID: `{$video->fileId}`",
                ['parse_mode' => 'Markdown']
            );
        }
        
        // Audio qabul qilish
        if ($message->hasAudio()) {
            $audio = $message->audio;
            
            $duration = $audio->duration;
            $performer = $audio->performer ?? 'Noma\'lum';
            $title = $audio->title ?? 'Noma\'lum';
            
            $bot->sendMessage($chatId,
                "🎵 Audio qabul qilindi!\n\n" .
                "Ijrochi: {$performer}\n" .
                "Nomi: {$title}\n" .
                "Davomiyligi: {$duration} soniya"
            );
        }
    }
    
    // ==================== CALLBACK QUERY ====================
    
    if ($update->hasCallbackQuery()) {
        $callback = $update->getCallbackQuery();
        $data = $callback->getData();
        $chatId = $callback->message->chat->id;
        $messageId = $callback->message->messageId;
        $userId = $callback->from->id;
        
        if (!isset($_SESSION['user_' . $userId])) {
            $_SESSION['user_' . $userId] = [
                'state' => 'start',
                'data' => [],
                'language' => 'uz'
            ];
        }
        
        $userState = &$_SESSION['user_' . $userId];
        
        switch ($data) {
            case 'start_form':
                $userState['state'] = 'waiting_name';
                $userState['data'] = [];
                
                $bot->answerCallbackQuery($callback->id, [
                    'text' => 'Anketa boshlandi'
                ]);
                
                $bot->sendMessage($chatId, "📝 Anketa to'ldirish boshlandi.\n\nIsmingizni kiriting:");
                break;
                
            case 'stats':
                $bot->answerCallbackQuery($callback->id);
                
                $totalUsers = count(glob(session_save_path() . '/sess_*'));
                
                $stats = "📊 *Statistika*\n\n";
                $stats .= "👥 Foydalanuvchilar: {$totalUsers}\n";
                $stats .= "🆔 Sizning ID: `{$userId}`";
                
                $bot->editMessageText($chatId, $messageId, $stats, [
                    'parse_mode' => 'Markdown'
                ]);
                break;
                
            case 'settings':
                $bot->answerCallbackQuery($callback->id);
                
                $keyboard = new Keyboard('inline');
                $keyboard->rows(
                    ["🇺🇿 O'zbek" => ["callback_data" => "lang_uz"]],
                    ["🇷🇺 Русский" => ["callback_data" => "lang_ru"]],
                    ["🇬🇧 English" => ["callback_data" => "lang_en"]],
                    ["🔙 Orqaga" => ["callback_data" => "main_menu"]]
                );
                
                $bot->editMessageText($chatId, $messageId, 
                    "⚙️ Sozlamalar\n\nTilni tanlang:",
                    ['reply_markup' => $keyboard]
                );
                break;
                
            case 'lang_uz':
            case 'lang_ru':
            case 'lang_en':
                $lang = substr($data, 5);
                $userState['language'] = $lang;
                
                $bot->answerCallbackQuery($callback->id, [
                    'text' => 'Til o\'zgartirildi: ' . strtoupper($lang)
                ]);
                
                $bot->editMessageText($chatId, $messageId,
                    "✅ Til o'zgartirildi: " . strtoupper($lang)
                );
                break;
                
            case 'help':
                $bot->answerCallbackQuery($callback->id);
                
                $help = "ℹ️ *Yordam*\n\n";
                $help .= "*Mavjud funksiyalar:*\n";
                $help .= "• Anketa to'ldirish\n";
                $help .= "• Statistika ko'rish\n";
                $help .= "• Tilni o'zgartirish\n";
                $help .= "• Fayllar yuklash\n\n";
                $help .= "Yordam kerakmi? /help yozing";
                
                $bot->editMessageText($chatId, $messageId, $help, [
                    'parse_mode' => 'Markdown'
                ]);
                break;
                
            case 'edit_form':
                $bot->answerCallbackQuery($callback->id, [
                    'text' => 'Tahrirlash funksiyasi tez orada...'
                ]);
                break;
                
            case 'save_form':
                $bot->answerCallbackQuery($callback->id, [
                    'text' => '💾 Ma\'lumotlar saqlandi!',
                    'show_alert' => true
                ]);
                
                // Bu yerda ma'lumotlarni database ga saqlash mumkin
                
                $bot->editMessageText($chatId, $messageId,
                    "✅ Ma'lumotlaringiz muvaffaqiyatli saqlandi!\n\n" .
                    "Rahmat ishtirok etganingiz uchun! 🎉"
                );
                break;
                
            case 'main_menu':
                $bot->answerCallbackQuery($callback->id);
                
                $keyboard = new Keyboard('inline');
                $keyboard->rows(
                    ["📝 Anketa to'ldirish" => ["callback_data" => "start_form"]],
                    ["📊 Statistika" => ["callback_data" => "stats"]],
                    ["⚙️ Sozlamalar" => ["callback_data" => "settings"]],
                    ["ℹ️ Yordam" => ["callback_data" => "help"]]
                );
                
                $bot->editMessageText($chatId, $messageId, 
                    "🏠 Bosh menyu\n\nTanlang:",
                    ['reply_markup' => $keyboard]
                );
                break;
        }
    }
    
    // ==================== INLINE QUERY ====================
    
    if ($update->hasInlineQuery()) {
        $inlineQuery = $update->getInlineQuery();
        $query = $inlineQuery->getQuery();
        
        $results = [];
        
        if (empty($query)) {
            // Default natijalar
            $results[] = [
                'type' => 'article',
                'id' => '1',
                'title' => 'Salom!',
                'description' => 'Salomlashish xabari yuborish',
                'input_message_content' => [
                    'message_text' => '👋 Assalomu alaykum! Men Telegram botman.'
                ]
            ];
        } else {
            // Qidiruv natijalari
            $results[] = [
                'type' => 'article',
                'id' => '1',
                'title' => 'Echo: ' . $query,
                'description' => 'Sizning xabaringiz',
                'input_message_content' => [
                    'message_text' => '💬 ' . $query
                ]
            ];
        }
        
        $bot->answerInlineQuery($inlineQuery->getId(), $results, [
            'cache_time' => 300
        ]);
    }
    
} catch (\Exception $e) {
    error_log("Bot error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
}