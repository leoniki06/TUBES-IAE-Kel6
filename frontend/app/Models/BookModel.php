<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'isbn',
        'title',
        'author',
        'publisher',
        'year',
        'stock_total',
        'stock_available',
    ];

    protected $useTimestamps = true; // otomatis isi created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAvailableBooks($keyword = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('stock_available >', 0);

        if ($keyword) {
            $builder->groupStart()
                ->like('title', $keyword)
                ->orLike('author', $keyword)
                ->orLike('genre', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

}
