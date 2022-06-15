<?php

namespace App\Http\Controllers;

use App\Models\Proxy;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Lumen\Routing\Controller as BaseController;

class CookiesController extends BaseController
{
    /**
     * @throws GuzzleException
     */
    public function __invoke(Client $client): Response
    {
        $proxiesWithoutCookies = Proxy::query()
            ->doesntHave('cookies')
            ->get();

        if ($proxiesWithoutCookies->count() === 0) {
            return response('', '204');
        }

        foreach ($proxiesWithoutCookies as $proxy) {
            $response = $client->get('https://google.com');
            $setCookies = $response->getHeader('Set-Cookie');

            $cookies = $this->getCookies($setCookies);
            $proxy->cookies()->createMany($cookies);
        }

        return response('');
    }

    private function getCookies(array $setCookies): array
    {
        $cookies = [];
        foreach ($setCookies as $cookie) {
            $cookies[] = $this->parseCookie(SetCookie::fromString($cookie));
        }

        return $cookies;
    }

    #[ArrayShape([
        'name' => "string", 'value' => "null|string", 'domain' => "null|string",
        'path' => "string", 'max_age' => "int|null", 'expires' => "int|null|string",
        'secure' => "bool", 'http_only' => "bool"
    ])]
    private function parseCookie(SetCookie $cookie): array
    {
        return [
            'name' => $cookie->getName(),
            'value' => $cookie->getValue(),
            'domain' => $cookie->getDomain(),
            'path' => $cookie->getPath(),
            'max_age' => $cookie->getMaxAge(),
            'expires' => $cookie->getExpires(),
            'secure' => $cookie->getSecure(),
            'http_only' => $cookie->getHttpOnly(),
        ];
    }
}