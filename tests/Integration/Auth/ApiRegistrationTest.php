<?php

declare(strict_types=1);

namespace App\Tests\Integration\Auth;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ApiRegistrationTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = false;

    protected function tearDown(): void
    {
        parent::tearDown();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->getConnection()->executeStatement('DELETE FROM "user"');
    }

    public function testRegisterCreatesUser(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJsonContains(['status' => 'User created successfully']);
    }

    public function testRegisterReturnsBadRequestWhenFieldsMissing(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testRegisterReturnsConflictWhenEmailAlreadyUsed(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [
            'json' => [
                'email' => 'dup@example.com',
                'password' => 'password123',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/register', [
            'json' => [
                'email' => 'dup@example.com',
                'password' => 'password123',
            ],
        ]);
        $this->assertResponseStatusCodeSame(409);
    }

    public function testLoginReturnsJwtToken(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [
                'json' => [
                    'email' => 'login@example.com',
                    'password' => 'password123',
                ],
            ]
        );

        $client->request(
            'POST',
            '/api/login_check',
            [
                'json' => [
                    'username' => 'login@example.com',
                    'password' => 'password123',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $response = $client->getResponse();
        $this->assertNotNull($response);
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testProtectedRouteRequiresJwt(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api');

        $this->assertResponseStatusCodeSame(401);
    }
}
