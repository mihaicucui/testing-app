<?php

namespace Tests\Unit;

use Tests\TestCase;
use GuzzleHttp\Client;

class InstitutionTest extends TestCase
{
    protected static $client;
    protected static $bsoNames;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client();
    }

    public function testInstitutionGet()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/institutions");
        self::assertEquals(200, $response->getStatusCode(), "Get without parameters doesn't work");

        $responsePager = json_decode($response->getBody()->getContents(), true)["pager"];
        $totalPages = $responsePager["total_pages"];

        $id = null;
        $country = null;
        $bsoType = null;
        $changed = null;

        //search Italian Trade Agency on each page
        for ($i = 0; $i < $totalPages; $i++) {

            $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/institutions?page=" . $i);
            $responseData = json_decode($response->getBody()->getContents(), true)["data"];

            foreach ($responseData as $resp) {
                if ($resp["title"] == "Italian Trade Agency") {
                    $id = $resp["id"];
                    $country = $resp["country"];
                    $bsoTypes = $resp["bso_types"];
                    $changed = (new \DateTime($resp["changed"]))->format('Y-m-d');
                    break;
                }
            }

            if ($id != null) {
                break;
            }
        }

        self::assertNotEquals(null, $id);

        self::assertEquals(true, $this->checkIfExists($totalPages, "changed", $changed, $id));

        self::assertEquals(true, $this->checkIfExists($totalPages, "country", $country["id"], $id));

        self::assertEquals(true, $this->checkIfExists($totalPages, "country_iso2", $country["iso2"], $id));

        self::assertEquals(true, $this->checkIfExists($totalPages, "country_iso3", $country["iso3"], $id));

        foreach ($bsoTypes as $bsoType) {
            self::assertEquals(true, $this->checkIfExists($totalPages, "bso_type", $bsoType["id"], $id));
        }

        self::assertEquals(true, $this->checkIfExists($totalPages, "id", $id, $id));
    }

    /**
     * Cheks if the searched id exists on at least one page
     * @param $totalPages
     * @param $filterName
     * @param $filterValue
     * @param $id
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function checkIfExists($totalPages, $filterName, $filterValue, $id)
    {
        for ($i = 0; $i < $totalPages; $i++) {

            $responseFiltered = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/institutions?page=" . $i .
                "&" . $filterName . "=" . $filterValue);

            $responseData = json_decode($responseFiltered->getBody()->getContents(), true)["data"];

            foreach ($responseData as $resp) {
                if ($resp["id"] == $id) {
                    return true;
                }
            }
        }
        return false;
    }
}
