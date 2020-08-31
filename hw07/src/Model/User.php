<?php


namespace Books\Model;

use Books\Database;

class User
{

    public static function createTable(): void
    {
        $db = Database::get();
        $db->query("CREATE TABLE IF NOT EXISTS `user`(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR[20],
    password VARCHAR[20]);");
    }

    public static function createUser($username, $password)
    {
        User::createTable();
        $db = Database::get();
        $id = self::findUserByUsername($username);
        if($id != Null) return Null;
        $stmt = $db->prepare("INSERT INTO 'user' (username, password) VALUES (?, ?);");
        $stmt->execute([$username, $password]);
        $id = self::findUserByUsername($username);
        return $id;
    }

    public static function findUserByUsername($username)
    {
        User::createTable();
        $db = Database::get();
        $stmt = $db->prepare("SELECT id FROM user WHERE username = ?;");
        $stmt->execute([$username]);
        $result = $stmt->fetch();
        if($result == false) {
            return Null;
        } else {
            return $result['id'];
        }
    }

    public static function getUsers()
    {
        User::createTable();
        $db = Database::get();
        $stmt = $db->prepare("SELECT username, password FROM user;");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $usersList = [];
        foreach ($result as $row) {
            $usersList[] = [
                'username' => $row['username'],
                'password' => $row['password']
            ];
        }
        return $usersList;
    }
}