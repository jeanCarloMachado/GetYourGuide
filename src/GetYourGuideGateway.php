<?php

namespace GetYourGuide;

/**
 * @author Jean Carlo Machado <contato@jeancarlomachado.com.br>
 */
class GetYourGuideGateway
{
    private $endpoint;

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function fetchRemoteList() : array
    {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Content-type: application/json',
                ],
            ],
        ];
        $context = stream_context_create($opts);
        return json_decode(file_get_contents($this->endpoint, false, $context), true)['product_availabilities'];
    }
}

