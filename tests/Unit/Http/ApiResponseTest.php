<?php

namespace Tests\Unit\Http;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response()
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $response = ApiResponse::success($data, 'Success message', 201);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Success message', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    public function test_success_response_with_defaults()
    {
        $response = ApiResponse::success();

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Success', $responseData['message']);
        $this->assertNull($responseData['data']);
    }

    public function test_error_response()
    {
        $errors = ['field' => ['error message']];
        $response = ApiResponse::error('Error message', 400, $errors, 'CUSTOM_ERROR');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Error message', $responseData['message']);
        $this->assertEquals(400, $responseData['code']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertEquals('CUSTOM_ERROR', $responseData['error']);
    }

    public function test_error_response_with_defaults()
    {
        $response = ApiResponse::error();

        $this->assertEquals(400, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Error', $responseData['message']);
        $this->assertEquals(400, $responseData['code']);
        $this->assertArrayNotHasKey('errors', $responseData);
        $this->assertArrayNotHasKey('error', $responseData);
    }

    public function test_validation_error_response()
    {
        $errors = [
            'email' => ['The email field is required.'],
            'password' => ['The password must be at least 6 characters.']
        ];
        $response = ApiResponse::validationError($errors, 'Custom validation message');

        $this->assertEquals(422, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Custom validation message', $responseData['message']);
        $this->assertEquals(422, $responseData['code']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertEquals('VALIDATION_ERROR', $responseData['error']);
    }

    public function test_validation_error_response_with_default_message()
    {
        $errors = ['field' => ['error']];
        $response = ApiResponse::validationError($errors);

        $responseData = $response->getData(true);
        $this->assertEquals('Validation failed', $responseData['message']);
    }

    public function test_unauthorized_response()
    {
        $response = ApiResponse::unauthorized('Custom unauthorized message');

        $this->assertEquals(401, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Custom unauthorized message', $responseData['message']);
        $this->assertEquals(401, $responseData['code']);
        $this->assertEquals('UNAUTHORIZED', $responseData['error']);
    }

    public function test_unauthorized_response_with_default_message()
    {
        $response = ApiResponse::unauthorized();

        $responseData = $response->getData(true);
        $this->assertEquals('Unauthorized', $responseData['message']);
    }

    public function test_not_found_response()
    {
        $response = ApiResponse::notFound('Custom not found message');

        $this->assertEquals(404, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Custom not found message', $responseData['message']);
        $this->assertEquals(404, $responseData['code']);
        $this->assertEquals('NOT_FOUND', $responseData['error']);
    }

    public function test_not_found_response_with_default_message()
    {
        $response = ApiResponse::notFound();

        $responseData = $response->getData(true);
        $this->assertEquals('Resource not found', $responseData['message']);
    }

    public function test_forbidden_response()
    {
        $response = ApiResponse::forbidden('Custom forbidden message');

        $this->assertEquals(403, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Custom forbidden message', $responseData['message']);
        $this->assertEquals(403, $responseData['code']);
        $this->assertEquals('FORBIDDEN', $responseData['error']);
    }

    public function test_forbidden_response_with_default_message()
    {
        $response = ApiResponse::forbidden();

        $responseData = $response->getData(true);
        $this->assertEquals('Forbidden', $responseData['message']);
    }

    public function test_server_error_response()
    {
        $response = ApiResponse::serverError('Custom server error message');

        $this->assertEquals(500, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Custom server error message', $responseData['message']);
        $this->assertEquals(500, $responseData['code']);
        $this->assertEquals('SERVER_ERROR', $responseData['error']);
    }

    public function test_server_error_response_with_default_message()
    {
        $response = ApiResponse::serverError();

        $responseData = $response->getData(true);
        $this->assertEquals('Internal server error', $responseData['message']);
    }

    public function test_paginated_response()
    {
        // Create mock paginated data
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
            ['id' => 3, 'name' => 'Item 3'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items->forPage(1, 2), // Current page items
            $items->count(), // Total items
            2, // Per page
            1, // Current page
            ['path' => '/api/test']
        );

        $response = ApiResponse::paginated($paginator, 'Custom paginated message');

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Custom paginated message', $responseData['message']);
        $this->assertCount(2, $responseData['data']); // Current page items

        // Check pagination metadata
        $this->assertArrayHasKey('pagination', $responseData);
        $this->assertEquals(1, $responseData['pagination']['current_page']);
        $this->assertEquals(2, $responseData['pagination']['last_page']);
        $this->assertEquals(2, $responseData['pagination']['per_page']);
        $this->assertEquals(3, $responseData['pagination']['total']);
        $this->assertEquals(1, $responseData['pagination']['from']);
        $this->assertEquals(2, $responseData['pagination']['to']);
    }

    public function test_paginated_response_with_default_message()
    {
        $items = collect([['id' => 1]]);
        $paginator = new LengthAwarePaginator(
            $items,
            $items->count(),
            10,
            1
        );

        $response = ApiResponse::paginated($paginator);

        $responseData = $response->getData(true);
        $this->assertEquals('Data retrieved successfully', $responseData['message']);
    }

    public function test_response_data_types()
    {
        // Test with different data types
        $stringData = "test string";
        $response = ApiResponse::success($stringData);
        $responseData = $response->getData(true);
        $this->assertEquals($stringData, $responseData['data']);

        $arrayData = [1, 2, 3];
        $response = ApiResponse::success($arrayData);
        $responseData = $response->getData(true);
        $this->assertEquals($arrayData, $responseData['data']);

        $numericData = 42;
        $response = ApiResponse::success($numericData);
        $responseData = $response->getData(true);
        $this->assertEquals($numericData, $responseData['data']);

        $booleanData = true;
        $response = ApiResponse::success($booleanData);
        $responseData = $response->getData(true);
        $this->assertEquals($booleanData, $responseData['data']);
    }

    public function test_response_content_type_is_json()
    {
        $response = ApiResponse::success(['test' => 'data']);

        $this->assertTrue($response->headers->contains('content-type', 'application/json'));
    }
}
