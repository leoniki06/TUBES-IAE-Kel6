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
        'genre',
        'cover',
        'description',
        'stock_total',
        'stock_available',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRecommendations(int $limit = 5): array
    {
        return $this->orderBy('id', 'DESC')
                    ->findAll($limit);
    }


    // Untuk halaman katalog: boleh tampil semua (kalau mau hanya yang stok>0 tinggal uncomment)
    public function getBooks(?string $keyword = null): array
    {
        $builder = $this->builder();

        if ($keyword) {
            $builder->groupStart()
                ->like('title', $keyword)
                ->orLike('author', $keyword)
                ->orLike('genre', $keyword)
                ->groupEnd();
        }

        // kalau kamu mau hanya yang tersedia:
        // $builder->where('stock_available >', 0);

        return $builder->orderBy('title', 'ASC')->get()->getResultArray();
    }

    public function getBookById(int $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function decreaseStock(int $bookId, int $qty = 1): bool
    {
        // stok tidak boleh minus
        $sql = "UPDATE {$this->table}
                SET stock_available = stock_available - ?
                WHERE id = ? AND stock_available >= ?";
        $this->db->query($sql, [$qty, $bookId, $qty]);

        return $this->db->affectedRows() > 0;
    }

    public function increaseStock(int $bookId, int $qty = 1): bool
    {
        $sql = "UPDATE {$this->table}
                SET stock_available = stock_available + ?
                WHERE id = ?";
        $this->db->query($sql, [$qty, $bookId]);

        return $this->db->affectedRows() > 0;
    }

        public function decrementStockAvailable(int $bookId): bool
    {
        return $this->set('stock_available', 'stock_available - 1', false)
            ->where('id', $bookId)
            ->where('stock_available >', 0)
            ->update();
    }

}
