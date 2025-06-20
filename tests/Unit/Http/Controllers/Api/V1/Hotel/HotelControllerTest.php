<?php

namespace Tests\Unit\Http\Controllers\Api\V1\Hotel;

use Mockery;
use Tests\TestCase;
use App\Models\Hotel;
use App\Repositories\Hotel\HotelRepository;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Api\V1\Hotel\HotelCollection;
use App\Http\Requests\Api\V1\Hotel\StoreHotelRequest;
use App\Http\Controllers\Api\V1\Hotel\HotelController;
use App\Http\Requests\Api\V1\Paginator\SamplePaginatorRequest;

class HotelControllerTest extends TestCase
{
    protected $hotelRepositoryMock;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->hotelRepositoryMock = Mockery::mock(HotelRepository::class);

        // $this->controller = new HotelController($this->hotelRepositoryMock);

        // $this->controller = Mockery::mock(HotelController::class, [$this->hotelRepositoryMock])->makePartial();

        // $this->controller->shouldAllowMockingProtectedMethods();

        // $this->controller->shouldReceive('printLog')->andReturn(null);

        // $this->controller->shouldReceive('responseErrorByException')->andReturn(response()->json(['message' => 'Error'], 500));

        // $this->controller->shouldReceive('responseWithoutData')->andReturnUsing(function ($data, $status) {
        //     return response()->json($data, $status);
        // });

        // $this->controller->shouldReceive('responseWithData')->andReturnUsing(function ($message, $status) {
        //     return response()->json(['message' => $message], $status);
        // });
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndexReturnsHotelCollection()
    {
        $request = new SamplePaginatorRequest(['page' => 1, 'per_page' => 10]);
        $hotels = Hotel::factory()->count(3)->make();
        $paginatedHotels = new LengthAwarePaginator($hotels->toBase(), $hotels->count(), 10, 1);
        $repositoryMock = Mockery::mock(HotelRepository::class);

        $repositoryMock->shouldReceive('samplePaginator')
            ->once()
            ->with(1, 10)
            ->andReturn($paginatedHotels);

        $controllerMock = Mockery::mock(HotelController::class, [$repositoryMock])->makePartial();
        $response = $controllerMock->index($request);
        $this->assertInstanceOf(HotelCollection::class, $response);
        $this->assertCount(3, $response->resource);
    }

    public function testStoreCreatesHotelSuccessfully()
    {

        $requestData = [
            'name' => 'Hotel de Prueba',
            'address' => '123 Calle Falsa',
            'quantity_rooms' => 10,
            'city_id' => 12
        ];
        $request = new StoreHotelRequest($requestData);
        $hotel = new Hotel($requestData);
        $hotel->id = 1;
        $repositoryMock = Mockery::mock(HotelRepository::class);
        $repositoryMock->shouldReceive('create')
            ->once()
            ->with($request)
            ->andReturn($hotel);

        $controllerMock = Mockery::mock(HotelController::class, [$repositoryMock])->makePartial();
        $response = $controllerMock->store($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hotel', $responseData);
        $this->assertEquals($hotel->name, $responseData['hotel']['name']);
        $this->assertEquals($hotel->address, $responseData['hotel']['address']);
    }
}
