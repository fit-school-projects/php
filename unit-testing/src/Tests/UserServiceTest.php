<?php declare(strict_types=1);

namespace HW\Tests;

use HW\Factory\UserServiceFactory;
use HW\Interfaces\IStorage;
use HW\Lib\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private IStorage $storage;
    private UserService $userService;

    private function getUserService(IStorage $storage) {
        return UserServiceFactory::get($storage);
    }

    protected function setup (): void
    {
        parent::setUp();
        $this->storage = $this->createMock(IStorage::class);
        $this->userService = $this->getUserService($this->storage);
    }

    public function testCreateUserSuccess()
    {
        $username = 'test';
        $email = 'test@test.sk';
        $this->storage->expects($this->once())
            ->method('save')
            ->with(
                $this->isType('string'),
                $this->callback(function ($userJson) use ($username, $email) {
                    $user = json_decode($userJson, true);
                    return $user['username'] === $username && $user['email'] === $email;
                })
            );

        $userService = new UserService($this->storage);
        $id = $userService->createUser($username, $email);

        $this->assertIsString($id);
        $this->assertNotEmpty($id);
    }


    public function testCreateUserWithNonStringUsername() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser(array(), "valid@example.com");
    }

    public function testCreateUserWithNonStringEmail() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("username", array());
    }

    public function testCreateUserWithWhitespaceUsername() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("   ", "valid@example.com");
    }

    public function testCreateUserWithWhitespaceEmail() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("username", "   ");
    }

    public function testCreateUserWithEmptyUsername() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("", "valid@example.com");
    }

    public function testCreateUserWithEmptyEmail() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("username", "");
    }

    public function testCreateUserWithInvalidEmail() {
        $this->expectException(\InvalidArgumentException::class);
        $this->storage->expects($this->never())->method('save');
        $this->userService->createUser("username", "invalid_email");
    }


    public function testGetUsername() {
        $storageMock = $this->createMock(IStorage::class);
        $userService = new UserService($storageMock);

        $this->assertEquals(null, $userService->getUsername('123'));

        $userId = '12345';
        $expectedUsername = 'test';
        $userData = json_encode(['username' => $expectedUsername]);

        $storageMock->method('get')->with($userId)->willReturn($userData);
        $username = $userService->getUsername($userId);
        $this->assertEquals($expectedUsername, $username);

        $this->expectException(\InvalidArgumentException::class);
        $userService->getUsername(123);
    }

    public function testGetEmail()
    {
        $storageMock = $this->createMock(IStorage::class);
        $userService = new UserService($storageMock);

        $this->assertEquals(null, $userService->getEmail('123'));

        $userId = '12345';
        $expectedEmail = 'test@example.com';
        $userData = json_encode(['email' => $expectedEmail]);

        $storageMock->method('get')->with($userId)->willReturn($userData);

        $email = $userService->getEmail($userId);

        $this->assertEquals($expectedEmail, $email);

        $this->expectException(\InvalidArgumentException::class);
        $userService->getEmail(123);
    }
}
