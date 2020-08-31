<?php


namespace HW\Tests;

use HW\Lib\UserService;
use HW\Lib\Storage;
use PHPUnit\Framework\TestCase;


class UserServiceTest extends TestCase
{
    private $userService;
    private $storageMock;
    public function setUp(): void
    {
        parent::setUp();
        $this->storageMock = $this->createStub(Storage::class);
        $this->userService = new UserService($this->storageMock);
    }

    public function testCreateUser() {

        self::assertNotEquals(0, $this->userService->createUser('Illia','bryloill@fit.cvut.cz'));

    }

    public function testGetUsernameNull() {

        $id = 0;
        $this->storageMock->method('get')->willReturn(0);
        self::assertNull($this->userService->getUsername($id));

    }

    public function testGetEmailNull() {

        $id = 0;
        $this->storageMock->method('get')->willReturn(0);
        self::assertNull($this->userService->getEmail($id));

    }

    public function testGetUsername() {

        $id = 0;
        $this->storageMock->method('get')->willReturn(json_encode([
            'username' => 'Illia',
            'email' => 'bryloill@fit.cvut.cz'
        ]));
        self::assertEquals('Illia', $this->userService->getUsername($id));

    }

    public function testGetEmail() {

        $id = 0;
        $this->storageMock->method('get')->willReturn(json_encode([
            'username' => 'Illia',
            'email' => 'bryloill@fit.cvut.cz'
        ]));
        self::assertEquals('bryloill@fit.cvut.cz', $this->userService->getEmail($id));

    }


}