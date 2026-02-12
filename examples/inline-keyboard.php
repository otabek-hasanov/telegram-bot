<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;

$bot = new Client("YOUR_BOT_TOKEN");

// 1. Oddiy inline klaviatura
$keyboard1 = new Keyboard('inline');
$keyboard1->rows(
    ["Google" => ["url" => "https://google.com"]],
    ["GitHub" => ["url" => "https://github.com"]]
);

// 2. Callback data bilan
$keyboard2 = new Keyboard('inline');
$keyboard2->rows(
    [
        "Like ❤️" => ["callback_data" => "like"],
        "Dislike 👎" => ["callback_data" => "dislike"]
    ],
    ["Ko'proq" => ["callback_data" => "more"]]
);

// 3. Aralash
$keyboard3 = new Keyboard('inline');
$keyboard3->rows(
    ["Website" => ["url" => "https://example.com"]],
    [
        "Yes" => ["callback_data" => "yes"],
        "No" => ["callback_data" => "no"]
    ],
    ["Share" => ["switch_inline_query" => "Check this out!"]]
);

// 4. Reply klaviatura (resize)
$keyboard4 = new Keyboard('resize');
$keyboard4->setOneTime(true);
$keyboard4->rows(
    ["Tugma 1", "Tugma 2"],
    ["Tugma 3"],
    ["Raqamni yuborish" => ["request_contact" => true]],
    ["Joylashuvni yuborish" => ["request_location" => true]]
);

// 5. Klaviaturani o'chirish
$keyboard5 = new Keyboard('remove');

// Foydalanish
$chatId = "YOUR_CHAT_ID";

$bot->sendMessage($chatId, "Inline klaviatura 1:", [
    'reply_markup' => $keyboard1
]);

$bot->sendMessage($chatId, "Inline klaviatura 2:", [
    'reply_markup' => $keyboard2
]);

$bot->sendMessage($chatId, "Reply klaviatura:", [
    'reply_markup' => $keyboard4
]);