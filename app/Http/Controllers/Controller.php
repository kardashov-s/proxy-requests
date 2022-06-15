<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    const PROXIES = [
        ["82.117.252.136:12717:arturdavtyan:MZdwBdXLAZ", 'EjfRu2W2TfoPkCZ5'],
        ["82.117.252.136:12718:arturdavtyan:MZdwBdXLAZ", 'n8J9hSumA5nFx61G',],
        ["82.117.252.136:12719:arturdavtyan:MZdwBdXLAZ", 'ml8JsjEh1lAqkmHc',],
        ["82.117.252.136:12720:arturdavtyan:MZdwBdXLAZ", 'nnaOU5VBsCEgyUWq',],
        ["82.117.252.136:12721:arturdavtyan:MZdwBdXLAZ", '4OwabFL0E7lNsiha',],
        ["82.117.252.136:12722:arturdavtyan:MZdwBdXLAZ", '4eYZs5Rz5Fe9CU5X',],
        ["82.117.252.136:12723:arturdavtyan:MZdwBdXLAZ", 'jUL7KuKpeMJLVPRe',],
        ["82.117.252.136:12724:arturdavtyan:MZdwBdXLAZ", 'DyR98diPkDNYNsfd',],
        ["82.117.252.136:12725:arturdavtyan:MZdwBdXLAZ", 'XvGJOFml2z9sehM1',],
        ["82.117.252.136:12726:arturdavtyan:MZdwBdXLAZ", 'MOKFQpmFnYTBxtj9',],
        ["82.117.252.136:12727:arturdavtyan:MZdwBdXLAZ", 'aPQbaVfqi95vePnf',],
    ];


    /**
     * @throws GuzzleException
     */
    public function __invoke(Request $request)
    {

        //'Ew2QIF2Mwqk' => 'bla',
        //'54.194.2.114' => 'test',
        //'CgtFdzJRSUYyTXdxayjD-aGVBg%3D%3D' => 'test',
        $headers = collect($request->get('headers'));
        $headers  = $headers->groupBy('0');
        $headersnew = [];
        foreach($headers as $key => $value) {
            $headersnew[$key] = collect($value)->pluck('1')->toArray();
        }

        $method = $request->get('method');


        $headersnew['cookie'] = implode(';', $headersnew['cookie'] ?? []);

        $url = $request->get('url');
        //dd($headersnew);
        //$url = 'https://c5cc-54-194-2-114.eu.ngrok.io';
        $body = $request->get('content');
        $http = new Client();


        $responses  = [];
        foreach (self::PROXIES as $proxy) {

            $proxy2 = explode(':', $proxy[0]);

            try {
                $parts = parse_url($url);
                parse_str($parts['query'], $query);
                if(isset($query['cpn'])) {
                    $url = str_replace($query['cpn'], $proxy[1],$url);
                }
            } catch(\Exception $e) {

                Log::info($url);
            }

            /*
                        foreach($headersnew as $key => $values) {
                            if($key != 'cookie') {
                                foreach ($values as $key2 => $v) {
                                    $headersnew[$key][$key2] = Str::replace('54.194.2.114', $proxy[1], $v);
                                       $headersnew[$key][$key2] = Str::replace('Ew2QIF2Mwqk', $proxy[2], $v);
                                         $headersnew[$key][$key2] = Str::replace('CgtFdzJRSUYyTXdxayjD-aGVBg%3D%3D', $proxy[3], $v);

                                }
                            } else {
                                            $headersnew[$key] = Str::replace('54.194.2.114', $proxy[1], $values);
                                       $headersnew[$key]= Str::replace('Ew2QIF2Mwqk', $proxy[2], $values);
                                         $headersnew[$key] = Str::replace('CgtFdzJRSUYyTXdxayjD-aGVBg%3D%3D', $proxy[3],  $values);
                            }
                        }*/

            /*
                        $body = Str::replace('54.194.2.114', $proxy[1], $body);
                        $body = Str::replace('Ew2QIF2Mwqk', $proxy[2], $body);
                        $body = Str::replace('CgtFdzJRSUYyTXdxayjD-aGVBg%3D%3D', $proxy[3], $body);
                  */

            $responses[] = $http->requestAsync($method, $url, [
                'proxy' => sprintf(
                    'http://%s:%s@%s:%s',
                    $proxy2[2],
                    $proxy2[3],
                    $proxy2[0],
                    $proxy2[1],
                ),
                'headers' => $headersnew,
                'body'    => $body
            ]);
        }

        $resp = Utils::settle($responses)->wait();



        return response('');
    }
}
