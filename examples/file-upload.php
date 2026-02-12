<?php
/**
 * File Upload Example - Fayllar yuklash misoli
 * Bu faylda turli xil fayllarni yuklash usullari ko'rsatilgan
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;
use Stoyishi\Bot\Helpers\FileUploader;

$bot = new Client("YOUR_BOT_TOKEN");
$chatId = "YOUR_CHAT_ID"; // Yoki webhook dan oling

try {
    echo "<h1>Telegram Bot - Fayllar Yuklash Misoli</h1>";
    echo "<hr>";
    
    // ==================== 1. RASM YUKLASH ====================
    
    echo "<h2>1. Rasm Yuklash</h2>";
    
    // URL dan rasm yuklash
    try {
        $bot->sendPhoto($chatId, "https://picsum.photos/800/600", [
            'caption' => '📸 Bu rasm URL dan yuklandi'
        ]);
        echo "<p style='color: green;'>✅ Rasm URL dan yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // Mahalliy fayldan rasm yuklash (agar fayl mavjud bo'lsa)
    $localImagePath = __DIR__ . '/test-image.jpg';
    if (file_exists($localImagePath)) {
        try {
            $photo = FileUploader::createFromPath($localImagePath);
            $bot->sendPhoto($chatId, $photo, [
                'caption' => '📁 Bu rasm mahalliy fayldan yuklandi'
            ]);
            echo "<p style='color: green;'>✅ Mahalliy fayldan rasm yuklandi</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ test-image.jpg fayli topilmadi</p>";
    }
    
    // Base64 dan rasm yaratish
    try {
        // 1x1 piksel qizil rasm (demo)
        $base64Image = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFBQIAX8jx0gAAAABJRU5ErkJggg==";
        $photo = FileUploader::createFromBase64($base64Image, 'pixel.png', 'image/png');
        
        $bot->sendPhoto($chatId, $photo, [
            'caption' => '🔢 Bu rasm Base64 dan yaratildi'
        ]);
        echo "<p style='color: green;'>✅ Base64 dan rasm yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // ==================== 2. VIDEO YUKLASH ====================
    
    echo "<h2>2. Video Yuklash</h2>";
    
    // URL dan video yuklash (kichik fayl tavsiya etiladi)
    try {
        $bot->sendVideo($chatId, "https://www.w3schools.com/html/mov_bbb.mp4", [
            'caption' => '🎥 Bu video URL dan yuklandi',
            'supports_streaming' => true
        ]);
        echo "<p style='color: green;'>✅ Video URL dan yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // Mahalliy video fayl
    $localVideoPath = __DIR__ . '/test-video.mp4';
    if (file_exists($localVideoPath)) {
        try {
            $video = FileUploader::createFromPath($localVideoPath);
            $bot->sendVideo($chatId, $video, [
                'caption' => '📁 Bu video mahalliy fayldan yuklandi',
                'supports_streaming' => true
            ]);
            echo "<p style='color: green;'>✅ Mahalliy video yuklandi</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ test-video.mp4 fayli topilmadi</p>";
    }
    
    // ==================== 3. AUDIO YUKLASH ====================
    
    echo "<h2>3. Audio Yuklash</h2>";
    
    $localAudioPath = __DIR__ . '/test-audio.mp3';
    if (file_exists($localAudioPath)) {
        try {
            $audio = FileUploader::createFromPath($localAudioPath);
            $bot->sendAudio($chatId, $audio, [
                'caption' => '🎵 Bu audio fayl',
                'performer' => 'Artist Name',
                'title' => 'Song Title',
                'duration' => 180
            ]);
            echo "<p style='color: green;'>✅ Audio fayl yuklandi</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ test-audio.mp3 fayli topilmadi</p>";
    }
    
    // ==================== 4. HUJJAT YUKLASH ====================
    
    echo "<h2>4. Hujjat (Document) Yuklash</h2>";
    
    // PDF fayl yaratish va yuklash
    try {
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n200\n%%EOF";
        
        $tempPdfPath = sys_get_temp_dir() . '/test-document.pdf';
        file_put_contents($tempPdfPath, $pdfContent);
        
        $document = FileUploader::createFromPath($tempPdfPath);
        $bot->sendDocument($chatId, $document, [
            'caption' => '📄 Bu PDF hujjat'
        ]);
        
        unlink($tempPdfPath);
        echo "<p style='color: green;'>✅ PDF hujjat yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // TXT fayl yuklash
    try {
        $txtContent = "Bu oddiy matn fayli.\n\nTelegram Bot SDK orqali yuklandi.\n\nSana: " . date('Y-m-d H:i:s');
        
        $tempTxtPath = sys_get_temp_dir() . '/test-document.txt';
        file_put_contents($tempTxtPath, $txtContent);
        
        $document = FileUploader::createFromPath($tempTxtPath);
        $bot->sendDocument($chatId, $document, [
            'caption' => '📝 Bu TXT fayl'
        ]);
        
        unlink($tempTxtPath);
        echo "<p style='color: green;'>✅ TXT fayl yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // ==================== 5. VOICE MESSAGE ====================
    
    echo "<h2>5. Voice Message (Ovozli Xabar)</h2>";
    
    $localVoicePath = __DIR__ . '/test-voice.ogg';
    if (file_exists($localVoicePath)) {
        try {
            $voice = FileUploader::createFromPath($localVoicePath);
            $bot->sendData('sendVoice', [
                'chat_id' => $chatId,
                'voice' => $voice,
                'caption' => '🎤 Bu ovozli xabar'
            ]);
            echo "<p style='color: green;'>✅ Ovozli xabar yuklandi</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ test-voice.ogg fayli topilmadi</p>";
    }
    
    // ==================== 6. STICKER ====================
    
    echo "<h2>6. Sticker</h2>";
    
    // WebP format sticker yuklash
    $localStickerPath = __DIR__ . '/test-sticker.webp';
    if (file_exists($localStickerPath)) {
        try {
            $sticker = FileUploader::createFromPath($localStickerPath);
            $bot->sendData('sendSticker', [
                'chat_id' => $chatId,
                'sticker' => $sticker
            ]);
            echo "<p style='color: green;'>✅ Sticker yuklandi</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ test-sticker.webp fayli topilmadi</p>";
    }
    
    // ==================== 7. MEDIA GROUP (Albom) ====================
    
    echo "<h2>7. Media Group (Albom)</h2>";
    
    try {
        $media = [
            [
                'type' => 'photo',
                'media' => 'https://picsum.photos/800/600?random=1',
                'caption' => 'Birinchi rasm'
            ],
            [
                'type' => 'photo',
                'media' => 'https://picsum.photos/800/600?random=2',
                'caption' => 'Ikkinchi rasm'
            ],
            [
                'type' => 'photo',
                'media' => 'https://picsum.photos/800/600?random=3',
                'caption' => 'Uchinchi rasm'
            ]
        ];
        
        $bot->sendData('sendMediaGroup', [
            'chat_id' => $chatId,
            'media' => json_encode($media)
        ]);
        
        echo "<p style='color: green;'>✅ Media group (albom) yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // ==================== 8. ANIMATION (GIF) ====================
    
    echo "<h2>8. Animation (GIF)</h2>";
    
    try {
        $bot->sendData('sendAnimation', [
            'chat_id' => $chatId,
            'animation' => 'https://media.giphy.com/media/3o7btPCcdNniyf0ArS/giphy.gif',
            'caption' => '🎞️ Bu animatsiya (GIF)'
        ]);
        echo "<p style='color: green;'>✅ GIF animatsiya yuklandi</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red;'>❌ Xatolik: " . $e->getMessage() . "</p>";
    }
    
    // ==================== YAKUNIY XABAR ====================
    
    echo "<h2>✅ Barcha fayllar yuklash testlari tugadi!</h2>";
    
    $keyboard = new Keyboard('inline');
    $keyboard->rows(
        ["📸 Yana rasm" => ["callback_data" => "send_photo"]],
        ["🎥 Yana video" => ["callback_data" => "send_video"]],
        ["📄 Yana hujjat" => ["callback_data" => "send_document"]]
    );
    
    $bot->sendMessage($chatId, 
        "✅ Barcha fayllar yuklash testlari muvaffaqiyatli yakunlandi!\n\n" .
        "Quyidagi tugmalar orqali yana fayllar yuklashingiz mumkin:",
        ['reply_markup' => $keyboard]
    );
    
    echo "<p style='color: green; font-size: 20px;'>🎉 Hammasi muvaffaqiyatli!</p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'><strong>XATOLIK:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}