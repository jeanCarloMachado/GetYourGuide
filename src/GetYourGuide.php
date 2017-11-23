<?php
declare(strict_types=1);
namespace GetYourGuide;

/**
 * @author Jean Carlo Machado <contato@jeancarlomachado.com.br>
 */
class GetYourGuide
{
    private $gateway;

    public static function factoryWithEndpoint($endpoint)
    {
        return new self(
            new GetYourGuideGateway($endpoint)
        );
    }

    public function __construct(GetYourGuideGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function getList(string $startTime, string $endTime, int $numberOfTravelers) : array
    {
        $startTimestamp = \DateTime::createFromFormat('Y-m-d\TH:i', $startTime)->getTimestamp();
        $endTimestamp = \DateTime::createFromFormat('Y-m-d\TH:i', $endTime)->getTimestamp();

        $result = $this->gateway->fetchRemoteList();

        $result = array_filter($result, function($item) use ($startTimestamp, $endTimestamp) {
            $itemDate = \DateTime::createFromFormat('Y-m-d\TH:i', $item['activity_start_datetime']);
            $itemTimestamp = $itemDate->getTimestamp();

            $itemEndDate = clone $itemDate;
            $itemEndDate->add(new \DateInterval('PT' . $item['activity_duration_in_minutes'] . 'M'));
            $itemEndTimestamp = $itemEndDate->getTimestamp();

            return ($itemTimestamp >= $startTimestamp && $itemEndTimestamp < $endTimestamp) ;
        });

        $result = array_filter($result, function($item) use ($numberOfTravelers) {
            return ($item['places_available'] >= $numberOfTravelers);
        });

        $combineProducts = array_reduce($result, function($carry, $item){
            if (isset($carry[$item['product_id']])) {
                $carry[$item['product_id']]['available_starttimes'][] = $item['activity_start_datetime'];
                return $carry;
            }

            $carry[$item['product_id']] = [
                'product_id' => $item['product_id'],
                'available_starttimes' => [
                     $item['activity_start_datetime']
                ]
            ];
            return $carry;

        }, []);

        ksort($combineProducts);
        $withoutIndices = array_values($combineProducts);

        return $withoutIndices;
    }
}

