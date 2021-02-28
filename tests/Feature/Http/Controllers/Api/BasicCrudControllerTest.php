<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        /**@var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testeStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'test_description']);
        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
        );
    }

    public function testeIfFindOrFailFetchModel()
    {
        /**@var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        
        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testeIfFindOrFailThrowExceptionWhenIdInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        
        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testShow()
    {
        /**@var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);
        $result = $this->controller->show($category->id);
        $this->assertEquals($result->toArray(), CategoryStub::find(1)->toArray());
    }

    public function testUpdate()
    {
        /**@var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name_updated', 'description' => 'test_description_updated']);
        $result = $this->controller->update($request, $category->id);
        $this->assertEquals($result->toArray(),CategoryStub::find(1)->toArray());
    }

    public function testDestroy()
    {
        /**@var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);
        $response = $this->controller->destroy($category->id);
        $this->createTestResponse($response)->assertStatus(204);
        $this->assertNull(CategoryStub::find($category->id));
    }
}
