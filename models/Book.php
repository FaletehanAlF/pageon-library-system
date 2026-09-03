<?php

class Book
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getLatestBooks(int $limit = 5): array
    {
        $sql = "
            SELECT 
                books.*,
                categories.name AS category_name
            FROM books
            INNER JOIN categories 
                ON books.category_id = categories.id
            ORDER BY books.created_at DESC
            LIMIT $limit
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalBooks(): int
    {
        $sql = "SELECT COUNT(*) FROM books";

        return (int) $this->db->query($sql)->fetchColumn();
    }
}