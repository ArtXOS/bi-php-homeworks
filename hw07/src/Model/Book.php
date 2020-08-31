<?php

namespace Books\Model;

use Books\Database;

class Book
{

    public static function createTable(): void
    {
        $db = Database::get();
        $db->query("CREATE TABLE IF NOT EXISTS `book`(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR[30],
    author VARCHAR[30],
    publisher VARCHAR[30],
    isbn VARCHAR[30],
    pages INTEGER);");
    }

    public static function addBook($name, $author, $publisher, $isbn, $pages)
    {
        self::createTable();
        $db = Database::get();
        $id = self::findBookByISBN($isbn);
        if ($id != Null) return Null;
        $stmt = $db->prepare("INSERT INTO 'book' (name, author, publisher, isbn, pages) VALUES (?, ?, ?, ?, ?);");
        $stmt->execute([$name, $author, $publisher, $isbn, $pages]);
        $id = self::findBookByISBN($isbn);
        return $id;
    }

    public static function findBookByISBN($isbn)
    {
        self::createTable();
        $db = Database::get();
        $stmt = $db->prepare("SELECT id FROM book WHERE isbn = ?;");
        $stmt->execute([$isbn]);
        $result = $stmt->fetch();
        if ($result == false) {
            return Null;
        } else {
            return $result['id'];
        }
    }

    public static function findBookById($id)
    {
        self::createTable();
        $db = Database::get();
        $stmt = $db->prepare("SELECT * FROM book WHERE id = ?;");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        if ($result == false) {
            return Null;
        } else {
            return [
                'id' => intval($result['id']),
                'name' => $result['name'],
                'author' => $result['author'],
                'publisher' => $result['publisher'],
                'isbn' => $result['isbn'],
                'pages' => intval($result['pages'])
            ];
        }
    }

    public static function updateBookWithId($id, $name, $author, $publisher, $isbn, $pages)
    {
        self::createTable();
        $db = Database::get();
        $stmt = $db->prepare("UPDATE book SET name=?, author=?, publisher=?, isbn=?, pages=? WHERE id=?;");
        $stmt->execute([$name, $author, $publisher, $isbn, $pages, $id]);
    }

    public static function getAllBooks()
    {
        self::createTable();
        $db = Database::get();
        $stmt = $db->prepare("SELECT * FROM book;");
        $stmt->execute();
        $data = $stmt->fetchAll();
        if(!$data) return [];

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id' => intval($row['id']),
                'name' => $row['name'],
                'author' => $row['author']
            ];
        }

        return $result;
    }

    public static function deleteBookWithId($id)
    {
        $db = Database::get();
        $stmt = $db->prepare("DELETE FROM book WHERE id = ?;");
        $stmt->execute([$id]);
    }
}
