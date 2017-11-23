<?php

declare(strict_types=1);

namespace GetYourGuide;

/**
 * @author Jean Carlo Machado <contato@jeancarlomachado.com.br>
 */
class GetYourGuideTest extends \PHPUnit\Framework\TestCase
{
    public function testGetList_shouldFilterByPassengers()
    {
        $mock = $this->prophesize(GetYourGuideGateway::class);
        $mock->fetchRemoteList()->willReturn([
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2017-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'5',
            ],
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2017-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'3',
            ]
        ]);
        $service = new GetYourGuide($mock->reveal());
        $result = $service->getList('2017-01-01T19:30', '2020-12-31T22:30', 4);
        $this->assertCount(1, $result);
    }

    public function testGetList_shouldFilterByStartDate()
    {
        $mock = $this->prophesize(GetYourGuideGateway::class);
        $mock->fetchRemoteList()->willReturn([
            [
                'product_id'=>666,
                'activity_start_datetime'=>'1990-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'3',
            ],
        ]);
        $service = new GetYourGuide($mock->reveal());
        $result = $service->getList('2017-01-01T19:30', '2020-12-31T22:30', 4);
        $this->assertCount(0, $result);
    }

    public function testGetList_shouldFilterByEndDate()
    {
        $mock = $this->prophesize(GetYourGuideGateway::class);
        $mock->fetchRemoteList()->willReturn([
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2999-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'3',
            ],
        ]);
        $service = new GetYourGuide($mock->reveal());
        $result = $service->getList('2017-01-01T19:30', '2020-12-31T22:30', 4);
        $this->assertCount(0, $result);
    }

    public function testGetList_shouldOrderByProductId()
    {
        $mock = $this->prophesize(GetYourGuideGateway::class);
        $mock->fetchRemoteList()->willReturn([
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2017-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'3',
            ],
            [
                'product_id'=>555,
                'activity_start_datetime'=>'2017-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'123',
            ]
        ]);
        $service = new GetYourGuide($mock->reveal());
        $result = $service->getList('2017-01-01T19:30', '2020-12-31T22:30', 4);
        $this->assertEquals(555, $result[0]['product_id']);
    }

    public function testGetList_shouldMergeProductsWithSameId()
    {
        $mock = $this->prophesize(GetYourGuideGateway::class);
        $mock->fetchRemoteList()->willReturn([
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2017-11-01T19:30',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'123',
            ],
            [
                'product_id'=>666,
                'activity_start_datetime'=>'2017-10-01T19:31',
                'activity_duration_in_minutes'=>'60',
                'places_available'=>'123',
            ]
        ]);
        $service = new GetYourGuide($mock->reveal());
        $result = $service->getList('2017-01-01T19:30', '2020-12-31T22:30', 4);
        $this->assertCount(1, $result);
        $this->assertCount(2, $result[0]['available_starttimes']);
        $this->assertEquals(666, $result[0]['product_id']);
    }
}
