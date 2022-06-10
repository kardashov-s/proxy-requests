<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use function GuzzleHttp\Promise\settle;

class Controller extends BaseController
{
    const PROXIES = [
        [
            'scheme' => 'http',
            'url' => 'geo.iproyal.com',
            'port' => '12323',
            'login' => 'wg93',
            'password' => 'wg93wg93'
        ],
    ];

    /**
     * @throws GuzzleException
     */
    public function __invoke(Request $request)
    {
        $headers = $request->get('headers');
        $url = $request->get('url');
        $body = json_encode($request->get('body'));
        $http = new Client();

        $responses  = [];
        foreach (self::PROXIES as $proxy) {
            $responses[] = $http->requestAsync($request->method(), $url, [
                'proxy' => sprintf(
                    '%s://%s:%s@%s:%s',
                    $proxy['scheme'],
                    $proxy['login'],
                    $proxy['password'],
                    $proxy['url'],
                    $proxy['port'],
                ),
                'headers' => [
                    'Content-Type' => 'application/json',
                    ...$headers
                ],
                'body'    => $body
            ]);
        }

        Utils::settle($responses)->wait();

        return response($responses);
    }
}
