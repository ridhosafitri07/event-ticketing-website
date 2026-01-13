<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'date', 'location', 'price', 'category', 
        'icon', 'image', 'description', 'available_tickets', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get all active events
     */
    public function getActiveEvents()
    {
        return $this->where('is_active', true)
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get events by category
     */
    public function getByCategory($category)
    {
        return $this->where('is_active', true)
                    ->where('category', $category)
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Search events
     */
    public function searchEvents($keyword)
    {
        return $this->where('is_active', true)
                    ->groupStart()
                        ->like('title', $keyword)
                        ->orLike('location', $keyword)
                        ->orLike('category', $keyword)
                    ->groupEnd()
                    ->findAll();
    }
}
