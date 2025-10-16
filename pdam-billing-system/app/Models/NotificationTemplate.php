<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'template_name',
        'template_type',
        'subject',
        'message_content',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function generateMessage(array $data): string
    {
        $message = $this->message_content;
        
        foreach ($data as $key => $value) {
            $message = str_replace("{{{$key}}}", $value, $message);
        }
        
        return $message;
    }
}
