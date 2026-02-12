<?php

namespace Stoyishi\Bot\Tests;

use PHPUnit\Framework\TestCase;
use Stoyishi\Bot\Client;
use Stoyishi\Bot\Keyboard;
use Stoyishi\Bot\Update;
use Stoyishi\Bot\Message;
use Stoyishi\Bot\Exceptions\TelegramException;
use Stoyishi\Bot\Exceptions\InvalidKeyboardException;

/**
 * Client Test - Asosiy funksiyalar uchun unit testlar
 */
class ClientTest extends TestCase
{
    private Client $bot;
    private string $testToken = 'test_token_123456:ABCdefGHIjklMNOpqrsTUVwxyz';
    
    protected function setUp(): void
    {
        $this->bot = new Client($this->testToken);
    }
    
    /**
     * Test: Client obyekti yaratilishi
     */
    public function testClientCreation(): void
    {
        $this->assertInstanceOf(Client::class, $this->bot);
    }
    
    /**
     * Test: Timeout o'rnatish
     */
    public function testSetTimeout(): void
    {
        $result = $this->bot->setTimeout(60);
        $this->assertInstanceOf(Client::class, $result);
    }
    
    /**
     * Test: Inline keyboard yaratish
     */
    public function testInlineKeyboardCreation(): void
    {
        $keyboard = new Keyboard('inline');
        $this->assertInstanceOf(Keyboard::class, $keyboard);
        $this->assertEquals('inline', $keyboard->getType());
    }
    
    /**
     * Test: Resize keyboard yaratish
     */
    public function testResizeKeyboardCreation(): void
    {
        $keyboard = new Keyboard('resize');
        $this->assertInstanceOf(Keyboard::class, $keyboard);
        $this->assertEquals('resize', $keyboard->getType());
    }
    
    /**
     * Test: Remove keyboard yaratish
     */
    public function testRemoveKeyboardCreation(): void
    {
        $keyboard = new Keyboard('remove');
        $this->assertInstanceOf(Keyboard::class, $keyboard);
        $this->assertEquals('remove', $keyboard->getType());
    }
    
    /**
     * Test: Noto'g'ri keyboard turi
     */
    public function testInvalidKeyboardType(): void
    {
        $this->expectException(InvalidKeyboardException::class);
        new Keyboard('invalid_type');
    }
    
    /**
     * Test: Inline keyboard build
     */
    public function testInlineKeyboardBuild(): void
    {
        $keyboard = new Keyboard('inline');
        $keyboard->rows(
            ["Test Button" => ["url" => "https://example.com"]],
            [
                "Button 1" => ["callback_data" => "data1"],
                "Button 2" => ["callback_data" => "data2"]
            ]
        );
        
        $result = json_decode($keyboard->build(), true);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('inline_keyboard', $result);
        $this->assertCount(2, $result['inline_keyboard']);
        
        // Birinchi qator
        $this->assertEquals('Test Button', $result['inline_keyboard'][0][0]['text']);
        $this->assertEquals('https://example.com', $result['inline_keyboard'][0][0]['url']);
        
        // Ikkinchi qator
        $this->assertEquals('Button 1', $result['inline_keyboard'][1][0]['text']);
        $this->assertEquals('data1', $result['inline_keyboard'][1][0]['callback_data']);
        $this->assertEquals('Button 2', $result['inline_keyboard'][1][1]['text']);
        $this->assertEquals('data2', $result['inline_keyboard'][1][1]['callback_data']);
    }
    
    /**
     * Test: Reply keyboard build
     */
    public function testReplyKeyboardBuild(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->rows(
            ["Button 1", "Button 2"],
            ["Button 3"]
        );
        
        $result = json_decode($keyboard->build(), true);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('keyboard', $result);
        $this->assertArrayHasKey('resize_keyboard', $result);
        $this->assertTrue($result['resize_keyboard']);
        $this->assertCount(2, $result['keyboard']);
    }
    
    /**
     * Test: Reply keyboard with contact request
     */
    public function testReplyKeyboardWithContactRequest(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->rows(
            ["Send Contact" => ["request_contact" => true]],
            ["Send Location" => ["request_location" => true]]
        );
        
        $result = json_decode($keyboard->build(), true);
        
        $this->assertTrue($result['keyboard'][0][0]['request_contact']);
        $this->assertTrue($result['keyboard'][1][0]['request_location']);
    }
    
    /**
     * Test: Remove keyboard build
     */
    public function testRemoveKeyboardBuild(): void
    {
        $keyboard = new Keyboard('remove');
        $result = json_decode($keyboard->build(), true);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('remove_keyboard', $result);
        $this->assertTrue($result['remove_keyboard']);
    }
    
    /**
     * Test: Keyboard setResize method
     */
    public function testKeyboardSetResize(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->setResize(false);
        $keyboard->rows(["Button"]);
        
        $result = json_decode($keyboard->build(), true);
        $this->assertFalse($result['resize_keyboard']);
    }
    
    /**
     * Test: Keyboard setOneTime method
     */
    public function testKeyboardSetOneTime(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->setOneTime(true);
        $keyboard->rows(["Button"]);
        
        $result = json_decode($keyboard->build(), true);
        $this->assertTrue($result['one_time_keyboard']);
    }
    
    /**
     * Test: Keyboard setSelective method
     */
    public function testKeyboardSetSelective(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->setSelective(true);
        $keyboard->rows(["Button"]);
        
        $result = json_decode($keyboard->build(), true);
        $this->assertTrue($result['selective']);
    }
    
    /**
     * Test: Keyboard setInputFieldPlaceholder method
     */
    public function testKeyboardSetInputFieldPlaceholder(): void
    {
        $keyboard = new Keyboard('resize');
        $keyboard->setInputFieldPlaceholder('Type here...');
        $keyboard->rows(["Button"]);
        
        $result = json_decode($keyboard->build(), true);
        $this->assertEquals('Type here...', $result['input_field_placeholder']);
    }
    
    /**
     * Test: Update obyekti yaratish
     */
    public function testUpdateCreation(): void
    {
        $updateData = [
            'update_id' => 123456,
            'message' => [
                'message_id' => 1,
                'date' => time(),
                'text' => 'Hello',
                'chat' => [
                    'id' => 123,
                    'type' => 'private'
                ],
                'from' => [
                    'id' => 123,
                    'first_name' => 'John'
                ]
            ]
        ];
        
        $update = new Update($updateData);
        
        $this->assertInstanceOf(Update::class, $update);
        $this->assertEquals(123456, $update->getUpdateId());
        $this->assertTrue($update->hasMessage());
        $this->assertInstanceOf(Message::class, $update->getMessage());
    }
    
    /**
     * Test: Message obyekti
     */
    public function testMessageObject(): void
    {
        $messageData = [
            'message_id' => 1,
            'date' => time(),
            'text' => 'Test message',
            'chat' => [
                'id' => 123,
                'type' => 'private'
            ],
            'from' => [
                'id' => 123,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]
        ];
        
        $message = new Message($messageData);
        
        $this->assertEquals('Test message', $message->getText());
        $this->assertTrue($message->hasText());
        $this->assertEquals(123, $message->chat->id);
        $this->assertEquals('John', $message->from->firstName);
    }
    
    /**
     * Test: Tugmalarni olish
     */
    public function testGetButtons(): void
    {
        $keyboard = new Keyboard('inline');
        $keyboard->rows(
            ["Button 1" => ["callback_data" => "data1"]]
        );
        
        $buttons = $keyboard->getButtons();
        $this->assertIsArray($buttons);
        $this->assertCount(1, $buttons);
    }
    
    /**
     * Test: Bo'sh keyboard
     */
    public function testEmptyKeyboard(): void
    {
        $keyboard = new Keyboard('inline');
        $buttons = $keyboard->getButtons();
        
        $this->assertIsArray($buttons);
        $this->assertEmpty($buttons);
    }
    
    /**
     * Test: Multiple rows
     */
    public function testMultipleRows(): void
    {
        $keyboard = new Keyboard('inline');
        $keyboard->rows(
            ["Button 1" => ["callback_data" => "data1"]],
            ["Button 2" => ["callback_data" => "data2"]],
            ["Button 3" => ["callback_data" => "data3"]]
        );
        
        $buttons = $keyboard->getButtons();
        $this->assertCount(3, $buttons);
    }
    
    /**
     * Test: Aralash keyboard (inline va reply)
     */
    public function testMixedButtonTypes(): void
    {
        $keyboard = new Keyboard('inline');
        $keyboard->rows(
            ["URL Button" => ["url" => "https://example.com"]],
            ["Callback Button" => ["callback_data" => "data"]],
            ["Switch Inline" => ["switch_inline_query" => "query"]]
        );
        
        $result = json_decode($keyboard->build(), true);
        
        $this->assertArrayHasKey('url', $result['inline_keyboard'][0][0]);
        $this->assertArrayHasKey('callback_data', $result['inline_keyboard'][1][0]);
        $this->assertArrayHasKey('switch_inline_query', $result['inline_keyboard'][2][0]);
    }
}