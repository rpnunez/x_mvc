<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use XMVC\Service\Validator;

class ValidatorTest extends TestCase
{
    public function test_required_rule()
    {
        $v = new Validator([]);
        $v->validate(['name' => 'required']);
        $this->assertTrue($v->fails());
        
        $v = new Validator(['name' => 'John']);
        $v->validate(['name' => 'required']);
        $this->assertFalse($v->fails());
    }

    public function test_email_rule()
    {
        $v = new Validator(['email' => 'not-an-email']);
        $v->validate(['email' => 'email']);
        $this->assertTrue($v->fails());
        
        $v = new Validator(['email' => 'test@example.com']);
        $v->validate(['email' => 'email']);
        $this->assertFalse($v->fails());
    }

    public function test_min_max_rule()
    {
        $v = new Validator(['password' => '123']);
        $v->validate(['password' => 'min:5']);
        $this->assertTrue($v->fails());
        
        $v = new Validator(['password' => '123456']);
        $v->validate(['password' => 'min:5']);
        $this->assertFalse($v->fails());
        
        $v = new Validator(['username' => 'toolongname']);
        $v->validate(['username' => 'max:5']);
        $this->assertTrue($v->fails());
    }
}
