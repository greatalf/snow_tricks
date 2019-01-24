<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function fullName_should_return_firstname_and_lastname()
    {
        $user = new User;
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $this->assertSame($user->fullName(), 'John Doe');

    }

    /**
     * @test
     */
    public function setEmail_should_return_valid_value() 
    {
        $user = new User;
        $value = 'user@gmail.com';
        $user->setEmail($value);
        $this->assertSame($value, $user->getEmail());
    }

    /**
     * @test
     */
    public function setUsername_should_return_valid_value() 
    {
        $user = new User;
        $value = 'Greatalf';
        $user->setUsername($value);
        $this->assertSame($value, $user->getUsername());
    }

    /**
     * @test
     */
    public function setPassword_should_return_valid_value() 
    {
        $user = new User;
        $value = 'password';
        $user->setPassword($value);
        $this->assertSame($value, $user->getPassword());
    }

    /**
     * @test
     */
    public function setSlug_should_return_valid_value() 
    {
        $user = new User;
        $value = 'slug';
        $user->setSlug($value);
        $this->assertSame($value, $user->getSlug());
    }

    /**
     * @test
     */
    public function setDescription_should_return_valid_value() 
    {
        $user = new User;
        $value = 'My description';
        $user->setDescription($value);
        $this->assertSame($value, $user->getDescription());
    }

    /**
     * @test
     */
    public function setToken_should_return_valid_value() 
    {
        $user = new User;
        $value = 'Token0123546%-enkoT';
        $user->setToken($value);
        $this->assertSame($value, $user->getToken());
    }

    /**
     * @test
     */
    public function setConfirmed_should_return_valid_value() 
    {
        $user = new User;
        $value = 0;
        $user->setConfirmed($value);
        $this->assertEquals($value, $user->getConfirmed());
    }
}
