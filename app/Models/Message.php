<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Scope pour les messages non lus
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope pour les messages lus
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Marquer le message comme lu
     */
    public function markAsRead()
    {
        $this->update(['read' => true]);
    }
}
