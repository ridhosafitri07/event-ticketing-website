<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingModel extends Model
{
    protected $table = 'event_ratings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_id', 'user_id', 'booking_id', 
        'rating', 'review', 'is_anonymous', 'helpful_count'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get event ratings with user info
     */
    public function getEventRatings($eventId, $limit = 10, $offset = 0)
    {
        return $this->select('event_ratings.*, users.name as user_name')
            ->join('users', 'users.id = event_ratings.user_id', 'left')
            ->where('event_ratings.event_id', $eventId)
            ->orderBy('event_ratings.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }
    
    /**
     * Check if user can rate this event (has completed booking)
     */
    public function canUserRate($userId, $eventId)
    {
        $bookingModel = new \App\Models\BookingModel();
        
        // Check if user has a completed booking for this event
        $hasCompletedBooking = $bookingModel
            ->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->where('status', 'Lunas')
            ->countAllResults() > 0;
        
        if (!$hasCompletedBooking) {
            return ['can_rate' => false, 'reason' => 'Anda harus menyelesaikan booking terlebih dahulu'];
        }
        
        // Check if already rated
        $hasRated = $this->where('user_id', $userId)
            ->where('event_id', $eventId)
            ->countAllResults() > 0;
        
        if ($hasRated) {
            return ['can_rate' => false, 'reason' => 'Anda sudah memberikan rating untuk event ini'];
        }
        
        return ['can_rate' => true];
    }
    
    /**
     * Add or update rating
     */
    public function addRating($data)
    {
        // Validate rating value
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            return false;
        }
        
        // Check if user already rated
        $existing = $this->where('user_id', $data['user_id'])
            ->where('event_id', $data['event_id'])
            ->first();
        
        if ($existing) {
            // Update existing rating
            return $this->update($existing['id'], $data);
        }
        
        // Insert new rating
        return $this->insert($data);
    }
    
    /**
     * Get rating statistics for event
     */
    public function getRatingStats($eventId)
    {
        $ratings = $this->where('event_id', $eventId)->findAll();
        
        if (empty($ratings)) {
            return [
                'average' => 0,
                'total' => 0,
                'distribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                'percentage' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]
            ];
        }
        
        $total = count($ratings);
        $sum = array_sum(array_column($ratings, 'rating'));
        $average = round($sum / $total, 1);
        
        // Calculate distribution
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($ratings as $rating) {
            $distribution[$rating['rating']]++;
        }
        
        // Calculate percentage
        $percentage = [];
        foreach ($distribution as $star => $count) {
            $percentage[$star] = $total > 0 ? round(($count / $total) * 100) : 0;
        }
        
        return [
            'average' => $average,
            'total' => $total,
            'distribution' => $distribution,
            'percentage' => $percentage
        ];
    }
}