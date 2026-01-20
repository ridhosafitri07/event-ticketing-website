<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table = 'event_favorites';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'event_id', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Cek apakah user sudah favorite event ini
     */
    public function isFavorited($userId, $eventId)
    {
        return $this->where([
            'user_id' => $userId,
            'event_id' => $eventId
        ])->countAllResults() > 0;
    }

    /**
     * Toggle favorite (add/remove)
     */
    public function toggleFavorite($userId, $eventId)
    {
        // Cek apakah sudah ada
        $existing = $this->where([
            'user_id' => $userId,
            'event_id' => $eventId
        ])->first();

        if ($existing) {
            // Sudah ada, hapus (unfavorite)
            $this->delete($existing['id']);
            return ['action' => 'removed', 'favorited' => false];
        } else {
            // Belum ada, tambahkan (favorite)
            $this->insert([
                'user_id' => $userId,
                'event_id' => $eventId
            ]);
            return ['action' => 'added', 'favorited' => true];
        }
    }

    /**
     * Ambil semua event favorit user dengan detail event
     */
    public function getUserFavorites($userId)
    {
        return $this->select('event_favorites.*, events.*')
            ->join('events', 'events.id = event_favorites.event_id')
            ->where('event_favorites.user_id', $userId)
            ->where('events.is_active', 1)
            ->orderBy('event_favorites.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Count total favorites per event
     */
    public function countEventFavorites($eventId)
    {
        return $this->where('event_id', $eventId)->countAllResults();
    }

    /**
     * Ambil semua event_id yang difavoritkan user (untuk mark di UI)
     */
    public function getUserFavoriteIds($userId)
    {
        $favorites = $this->select('event_id')
            ->where('user_id', $userId)
            ->findAll();
        
        return array_column($favorites, 'event_id');
    }
}