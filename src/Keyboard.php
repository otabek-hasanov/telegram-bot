<?php

namespace Stoyishi\Bot;

use Stoyishi\Bot\Exceptions\InvalidKeyboardException;

class Keyboard
{
    private string $type;
    private array $buttons = [];
    private bool $resize = false;
    private bool $oneTime = false;
    private bool $selective = false;
    private ?string $inputFieldPlaceholder = null;
    
    public function __construct(string $type = 'resize')
    {
        $allowedTypes = ['inline', 'resize', 'reply', 'remove'];
        
        if (!in_array($type, $allowedTypes)) {
            throw new InvalidKeyboardException('Invalid keyboard type: ' . $type);
        }
        
        $this->type = $type;
        
        if ($type === 'resize' || $type === 'reply') {
            $this->resize = true;
        }
    }
    
    /**
     * Qatorlar qo'shish
     */
    public function rows(...$rows): self
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
        
        return $this;
    }
    
    /**
     * Bitta qator qo'shish
     */
    public function addRow(array $row): self
    {
        $buttons = [];
        
        foreach ($row as $key => $value) {
            if (is_string($key)) {
                // Assoc array: ["Text" => ["url" => "..."]]
                $buttons[] = $this->createButton($key, $value);
            } elseif (is_string($value)) {
                // Simple array: ["Text 1", "Text 2"]
                $buttons[] = $this->createButton($value);
            } elseif (is_array($value)) {
                // Array ichida array
                $text = array_key_first($value);
                $params = $value[$text];
                $buttons[] = $this->createButton($text, $params);
            }
        }
        
        if (!empty($buttons)) {
            $this->buttons[] = $buttons;
        }
        
        return $this;
    }
    
    /**
     * Tugma yaratish
     */
    private function createButton(string $text, $params = null): array
    {
        $button = ['text' => $text];
        
        if ($this->type === 'inline') {
            return $this->createInlineButton($button, $params);
        } else {
            return $this->createReplyButton($button, $params);
        }
    }
    
    /**
     * Inline tugma yaratish
     */
    private function createInlineButton(array $button, $params): array
    {

if (isset($params['style'])) {
            $allowedStyles = ['danger', 'success', 'primary'];
            if (in_array($params['style'], $allowedStyles)) {
                $button['style'] = $params['style'];
            }
        }
        
        // Icon custom emoji (yangi 2026)
        if (isset($params['icon_custom_emoji_id'])) {
            $button['icon_custom_emoji_id'] = $params['icon_custom_emoji_id'];
        }

        if (is_array($params)) {
            if (isset($params['url'])) {
                $button['url'] = $params['url'];
            } elseif (isset($params['callback_data'])) {
                $button['callback_data'] = $params['callback_data'];
            } elseif (isset($params['switch_inline_query'])) {
                $button['switch_inline_query'] = $params['switch_inline_query'];
            } elseif (isset($params['switch_inline_query_current_chat'])) {
                $button['switch_inline_query_current_chat'] = $params['switch_inline_query_current_chat'];
            } elseif (isset($params['pay'])) {
                $button['pay'] = $params['pay'];
            } elseif (isset($params['login_url'])) {
                $button['login_url'] = $params['login_url'];
            } elseif (isset($params['web_app'])) {
                $button['web_app'] = $params['web_app'];
            }
        }
        
        return $button;
    }
    
    /**
     * Reply tugma yaratish
     */
    private function createReplyButton(array $button, $params): array
    {
        if (is_array($params)) {

            if (isset($params['style'])) {
            $allowedStyles = ['danger', 'success', 'primary'];
            if (in_array($params['style'], $allowedStyles)) {
                $button['style'] = $params['style'];
            }
        }
        
        // Icon custom emoji (yangi 2026)
        if (isset($params['icon_custom_emoji_id'])) {
            $button['icon_custom_emoji_id'] = $params['icon_custom_emoji_id'];
        }



            if (isset($params['request_contact']) || isset($params['send_phone'])) {
                $button['request_contact'] = true;
            }
            
            if (isset($params['request_location']) || isset($params['send_location'])) {
                $button['request_location'] = true;
            }
            
            if (isset($params['request_poll'])) {
                $button['request_poll'] = $params['request_poll'];
            }
            
            if (isset($params['web_app'])) {
                $button['web_app'] = $params['web_app'];
            }
            
            if (isset($params['request_user'])) {
                $button['request_user'] = $params['request_user'];
            }
            
            if (isset($params['request_chat'])) {
                $button['request_chat'] = $params['request_chat'];
            }
        }
        
        return $button;
    }
    
    /**
     * Resize parametrini o'rnatish
     */
    public function setResize(bool $resize): self
    {
        $this->resize = $resize;
        return $this;
    }
    
    /**
     * One time parametrini o'rnatish
     */
    public function setOneTime(bool $oneTime): self
    {
        $this->oneTime = $oneTime;
        return $this;
    }
    
    /**
     * Selective parametrini o'rnatish
     */
    public function setSelective(bool $selective): self
    {
        $this->selective = $selective;
        return $this;
    }
    
    /**
     * Input field placeholder o'rnatish
     */
    public function setInputFieldPlaceholder(string $placeholder): self
    {
        $this->inputFieldPlaceholder = $placeholder;
        return $this;
    }
    
    /**
     * Keyboard ni build qilish
     */
    public function build(): string
    {
        if ($this->type === 'remove') {
            $keyboard = ['remove_keyboard' => true];
            
            if ($this->selective) {
                $keyboard['selective'] = true;
            }
            
            return json_encode($keyboard);
        }
        
        if ($this->type === 'inline') {
            $keyboard = ['inline_keyboard' => $this->buttons];
        } else {
            $keyboard = [
                'keyboard' => $this->buttons,
                'resize_keyboard' => $this->resize
            ];
            
            if ($this->oneTime) {
                $keyboard['one_time_keyboard'] = true;
            }
            
            if ($this->selective) {
                $keyboard['selective'] = true;
            }
            
            if ($this->inputFieldPlaceholder !== null) {
                $keyboard['input_field_placeholder'] = $this->inputFieldPlaceholder;
            }
        }
        
        return json_encode($keyboard);
    }
    
    /**
     * Tugmalarni olish
     */
    public function getButtons(): array
    {
        return $this->buttons;
    }
    
    /**
     * Tipni olish
     */
    public function getType(): string
    {
        return $this->type;
    }
}
