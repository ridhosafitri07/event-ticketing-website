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
     * Get all active events (only upcoming events)
     */
    public function getActiveEvents()
    {
        return $this->where('is_active', true)
                    ->where('date >=', date('Y-m-d'))
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get all upcoming events (belum lewat)
     */
    public function getUpcomingEvents()
    {
        return $this->where('date >=', date('Y-m-d'))
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get past events (sudah lewat)
     */
    public function getPastEvents()
    {
        return $this->where('date <', date('Y-m-d'))
                    ->orderBy('date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get events by category (only upcoming)
     */
    public function getByCategory($category)
    {
        return $this->where('is_active', true)
                    ->where('category', $category)
                    ->where('date >=', date('Y-m-d'))
                    ->orderBy('date', 'ASC')
                    ->findAll();
    }
    
    /**
     * Search events (only upcoming)
     */
    public function searchEvents($keyword)
    {
        return $this->where('is_active', true)
                    ->where('date >=', date('Y-m-d'))
                    ->groupStart()
                        ->like('title', $keyword)
                        ->orLike('location', $keyword)
                        ->orLike('category', $keyword)
                    ->groupEnd()
                    ->findAll();
    }
    
    /**
     * Auto-archive events yang sudah lewat (set is_active = 0)
     */
    public function autoArchivePastEvents()
    {
        // Hitung dulu berapa event yang akan di-archive
        $count = $this->where('date <', date('Y-m-d'))
                      ->where('is_active', true)
                      ->countAllResults(false);
        
        // Update semua event yang sudah lewat
        if ($count > 0) {
            $this->where('date <', date('Y-m-d'))
                 ->where('is_active', true)
                 ->set(['is_active' => 0])
                 ->update();
        }
        
        return $count;
    }
}
