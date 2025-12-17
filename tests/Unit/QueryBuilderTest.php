<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use XMVC\Database\QueryBuilder;
use XMVC\Database\Connection;

class QueryBuilderTest extends TestCase
{
    public function test_to_sql_select()
    {
        $connection = $this->createMock(Connection::class);
        $qb = new QueryBuilder($connection);
        
        $sql = $qb->table('users')->select('id', 'name')->toSql();
        $this->assertEquals("SELECT id, name FROM users", $sql);
    }
    
    public function test_to_sql_where()
    {
        $connection = $this->createMock(Connection::class);
        $qb = new QueryBuilder($connection);
        
        $sql = $qb->table('users')->where('id', 1)->toSql();
        $this->assertEquals("SELECT * FROM users WHERE id = ?", $sql);
    }

    public function test_to_sql_complex()
    {
        $connection = $this->createMock(Connection::class);
        $qb = new QueryBuilder($connection);
        
        $sql = $qb->table('users')
                  ->where('age', '>', 18)
                  ->orderBy('created_at', 'DESC')
                  ->limit(10)
                  ->toSql();
                  
        $this->assertEquals("SELECT * FROM users WHERE age > ? ORDER BY created_at DESC LIMIT 10", $sql);
    }
}
