<?php

namespace Books;

use Books\Database\DB;
use PDO;

class BookRepository {
    public function getAllBooks(): array {
        $db = DB::get();
        $stmt = $db->query('SELECT id, name, author FROM books');

        $books = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $books[] = $row;
        }

        return $books;
    }

    public function getBookById($id): array | null {
        if (!is_numeric($id)) {
            return null;
        }
        $db = DB::get();
        $stmt = $db->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: [];
    }

    public static function createBook($book): array {
        foreach (['name', 'author', 'publisher', 'isbn', 'pages'] as $key) {
            if (!isset($book[$key])) {
                return [];
            }
        }
        $db = DB::get();
        $stmt = $db->prepare('INSERT INTO books (name, author, publisher, isbn, pages) VALUES (:name, :author, :publisher, :isbn, :pages)');
        $stmt->execute($book);
        $book['id'] = $db->lastInsertId();
        return $book;
    }

    public function updateBook($book, $id): array | null {
        foreach (['name', 'author', 'publisher', 'isbn', 'pages'] as $key) {
            if (!isset($book[$key])) {
                return [];
            }
        }
        if (!is_numeric($id)) {
            return [];
        }
        $stmt = $this->getBookById($id);
        if (empty($stmt)) {
            return null;
        }
        $db = DB::get();
        $stmt = $db->prepare('UPDATE books SET name = :name, author = :author, publisher = :publisher, isbn = :isbn, pages = :pages WHERE id = :id');
        $stmt->execute(['id' => $id, 'name' => $book['name'], 'author' => $book['author'], 'publisher' => $book['publisher'], 'isbn' => $book['isbn'], 'pages' => $book['pages']]);
        return $book;
    }

    public function deleteBook($id) {
        $stmt = $this->getBookById($id);
        if (empty($stmt)) {
            return null;
        }
        $db = DB::get();
        $stmt = $db->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt;
    }
}
