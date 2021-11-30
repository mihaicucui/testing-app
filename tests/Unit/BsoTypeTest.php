<?php

namespace Tests\Unit;

use Tests\TestCase;
use GuzzleHttp\Client;

class BsoTypeTest extends TestCase
{
    protected static $client;
    protected static $bsoNames;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client();
        self::$bsoNames = [
            "Arbitration and mediation centre",
            "Business women organization",
            "Chamber of Commerce",
            "Cooperative",
            "Economic development agency",
            "Employers body",
            "Incubator/Accelerator",
            "Industry or sector-specific institution (textile, agricultural)",
            "International purchasing and supply chain management organization",
            "Investment promotion agency"
        ];
    }

    public function testGetStatus()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/bso-types?page=0");

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBsoTypesNumber()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/bso-types?page=0");

        $responseBsoTypes = json_decode($response->getBody()->getContents(), true)["data"];

        self::assertEquals(sizeof($responseBsoTypes), sizeof(self::$bsoNames));
    }

    public function testGetResponse()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/bso-types?page=0");

        $responseBsos = json_decode($response->getBody()->getContents(), true)["data"];

        foreach (self::$bsoNames as $bsoName) {

            $contains = false;

            foreach ($responseBsos as $responseBso) {

                if ($responseBso["name"] == $bsoName) {
                    $contains = true;
                    break;
                }
            }

            self::assertEquals(true, $contains, $bsoName . " is not contained by the response");
        }
    }

}
