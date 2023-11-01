<?php

// Homemade testing! Temporary solution
// In fact, testing libraries are very buggy, please learn to write code

require 'vendor/autoload.php';

use Teletone\Bot;
use Teletone\Types\ReplyKeyboardMarkup;
use Teletone\Types\InlineKeyboardMarkup;
use Teletone\Types\ReplyKeyboardRemove;
use Teletone\Types\ForceReply;
use Teletone\Types\InputFile;

if (($bot_token = getenv('BOT_TOKEN')) === false)
    exit("BOT_TOKEN environment variable not set!\n");

if (($bot_admin_id = getenv('BOT_ADMIN_ID')) === false)
    exit("BOT_ADMIN_ID environment variable not set!\n");

echo "Run testing...\n\n";

$bot = new Bot($bot_token);

try {
    $bot->sendMessage([
        'chat_id' => 1234567890,
        'text' => 'test'
    ]);
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    $d = json_decode($e->getResponse()->getBody()->getContents());
    if ($d->error_code == 400)
        echo "Test: Sending a message to a non-existent user - ✅ PASSED\n";
    else
        echo "Test: Sending a message to a non-existent user - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test'
    ]);
    echo "Test: Sending a message to admin - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message to admin - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test',
        'reply_markup' => new ReplyKeyboardMarkup([
            [ '1', '2', '3' ],
            [ '4', '5', '6' ],
            [ '7', '8', '9' ]
        ])
    ]);
    echo "Test: Sending a message with the reply keyboard - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with the reply keyboard - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test',
        'reply_markup' => new ReplyKeyboardMarkup([
            [ '1', '2', '3' ],
            [ '4', '5', '6' ],
            [ '7', '8', '9' ]
        ], [
            'resize_keyboard' => true
        ])
    ]);
    echo "Test: Sending a message with the reply keyboard (resize_keyboard) - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with the reply keyboard (resize_keyboard) - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test',
        'reply_markup' => new InlineKeyboardMarkup([
            [
                [ 'text' => '1', 'callback_data' => '1' ],
                [ 'text' => '2', 'callback_data' => '2' ],
                [ 'text' => '3', 'callback_data' => '3' ]
            ],
            [
                [ 'text' => '4', 'callback_data' => '4' ],
                [ 'text' => '5', 'callback_data' => '5' ],
                [ 'text' => '6', 'callback_data' => '6' ]
            ],
            [
                [ 'text' => '7', 'callback_data' => '7' ],
                [ 'text' => '8', 'callback_data' => '8' ],
                [ 'text' => '9', 'callback_data' => '9' ]
            ],
        ])
    ]);
    echo "Test: Sending a message with the inline keyboard  - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with the inline keyboard - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test',
        'reply_markup' => new ForceReply([
            'input_field_placeholder' => 'test'
        ])
    ]);
    echo "Test: Sending a message with force reply - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with force reply - ❌ FAILED\n";
}

try {
    $bot->sendMessage([
        'chat_id' => $bot_admin_id,
        'text' => 'test',
        'reply_markup' => new ReplyKeyboardRemove()
    ]);
    echo "Test: Sending a message with keyboard remove - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with keyboard remove - ❌ FAILED\n";
}

try {
    $bot->sendPhoto([
        'chat_id' => $bot_admin_id,
        'photo' => new InputFile('tests/test.jpg')
    ]);
    echo "Test: Sending a message with image - ✅ PASSED\n";
}
catch(\GuzzleHttp\Exception\ClientException $e) {
    echo "Test: Sending a message with image - ❌ FAILED\n";
}

echo "\nTesting ended\n";