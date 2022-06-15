<?php

namespace App\Http\Controllers;

use App\Models\Proxy;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * @throws GuzzleException
     */
    public function __invoke(Request $request)
    {
        $proxies = Proxy::all();
        $headers = $request->get('headers');
        $url = $request->get('url');
        $body = json_encode($request->get('body'));
        $http = new Client();

        $responses  = [];
        foreach ($proxies as $proxy) {
            $responses[] = $http->requestAsync($request->method(), $url, [
                'proxy' => sprintf(
                    '%s://%s:%s@%s:%s',
                    $proxy->scheme,
                    $proxy->login,
                    $proxy->password,
                    $proxy->url,
                    $proxy->port,
                ),
                'headers' => [
                    'Content-Type' => 'application/json',
                    ...$headers
                ],
                'body' => $body
            ]);
        }

        Utils::settle($responses)->wait();

        return response('');
    }
}
