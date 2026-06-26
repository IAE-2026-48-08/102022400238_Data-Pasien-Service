<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    private const API_KEY = '102022400238';

    public function test_rest_contract_matches_grader_expectations(): void
    {
        $this->getJson('/api/v1')
            ->assertStatus(401)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', 'salah')
            ->getJson('/api/v1')
            ->assertForbidden()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->getJson('/api/v1')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->getJson('/api/v1/')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonMissingPath('meta')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->getJson('/api/v1/999999')
            ->assertNotFound()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->postJson('/api/v1', [])
            ->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->getJson('/api/v1/path-ngawur')
            ->assertNotFound()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);

        $this->withHeader('X-IAE-KEY', self::API_KEY)
            ->putJson('/api/v1', [])
            ->assertStatus(405)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'data', 'errors']);
    }

    public function test_swagger_documents_rest_endpoints(): void
    {
        $this->get('/api/documentation')
            ->assertOk();

        $response = $this->getJson('/docs')
            ->assertOk();

        $openApiAlias = $this->getJson('/openapi.json')
            ->assertOk();

        $paths = $response->json('paths');

        $this->assertSame('http://localhost:8001', $response->json('servers.0.url'));
        $this->assertSame('http://localhost:8001', $openApiAlias->json('servers.0.url'));
        $this->assertArrayHasKey('/api/v1', $paths);
        $this->assertArrayHasKey('get', $paths['/api/v1']);
        $this->assertArrayHasKey('post', $paths['/api/v1']);
        $this->assertArrayHasKey('/api/v1/{id}', $paths);
        $this->assertArrayHasKey('get', $paths['/api/v1/{id}']);
        $this->assertArrayHasKey('/api/v1/patients', $paths);
        $this->assertArrayHasKey('get', $paths['/api/v1/patients']);
        $this->assertArrayHasKey('post', $paths['/api/v1/patients']);
        $this->assertArrayHasKey('/api/v1/patients/{id}', $paths);
        $this->assertArrayHasKey('get', $paths['/api/v1/patients/{id}']);
    }

    public function test_graphql_patients_query_works(): void
    {
        $this->get('/graphql-playground')
            ->assertOk();

        $response = $this->postJson('/graphql', [
            'query' => '{ patients { id nik name birth_date gender } }',
        ])->assertOk();

        $this->assertArrayNotHasKey('errors', $response->json());
        $this->assertSame([], $response->json('data.patients'));

        $introspection = $this->postJson('/graphql', [
            'query' => '{ __schema { queryType { name } } }',
        ])->assertOk();

        $this->assertSame('Query', $introspection->json('data.__schema.queryType.name'));
    }
}
