<?php

namespace App\Http\Models;

use XMVC\Service\Db;
use XMVC\Event\HasEvents;
use PDO;

abstract class Model
{
    use HasEvents;

    protected static $table;

    public static function getTable()
    {
        if (static::$table) {
            return static::$table;
        }

        $className = (new \ReflectionClass(static::class))->getShortName();
        return strtolower($className) . 's';
    }

    public static function all()
    {
        $table = static::getTable();
        $stmt = Db::pdo()->query("SELECT * FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function find($id)
    {
        $table = static::getTable();
        $stmt = Db::pdo()->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        return $stmt->fetch();
    }

    public static function where($column, $value)
    {
        $table = static::getTable();
        $stmt = Db::pdo()->prepare("SELECT * FROM {$table} WHERE {$column} = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
        return $stmt->fetch();
    }

    public static function create(array $data)
    {
        static::fireModelEvent('creating', $data);

        $table = static::getTable();
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = Db::pdo()->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);

        $model = static::find(Db::pdo()->lastInsertId());
        
        static::fireModelEvent('created', $model);

        return $model;
    }
}