<?php

namespace Tests\Unit;

use Tests\TestCase;
use GuzzleHttp\Client;

class CountriesTest extends TestCase
{

    protected static $client;
    protected static $countries;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client();
        self::$countries = [
            "Afghanistan" =>
                [
                    "field_iso2" => "AF",
                    "field_iso3" => "AFG"
                ],
            "Åland Islands" =>
                [
                    "field_iso2" => "AX",
                    "field_iso3" => "ALA"
                ],
            "Albania" =>
                [
                    "field_iso2" => "AL",
                    "field_iso3" => "ALB"
                ],
            "Algeria" =>
                [
                    "field_iso2" => "DZ",
                    "field_iso3" => "DZA"
                ],
            "American Samoa" =>
                [
                    "field_iso2" => "AS",
                    "field_iso3" => "ASM"
                ],
            "Andorra" =>
                [
                    "field_iso2" => "AD",
                    "field_iso3" => "AND"
                ],
            "Angola" =>
                [
                    "field_iso2" => "AO",
                    "field_iso3" => "AGO"
                ],
            "Anguilla" =>
                [
                    "field_iso2" => "AI",
                    "field_iso3" => "AIA"
                ],
            "Antarctica" =>
                [
                    "field_iso2" => "AQ",
                    "field_iso3" => "ATA"
                ],
            "Antigua and Barbuda" =>
                [
                    "field_iso2" => "AG",
                    "field_iso3" => "ATG"
                ]
        ];
    }

    public function testGetStatus()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/countries?page=0");

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCountriesNumber()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/countries?page=0");

        $responseCountries = json_decode($response->getBody()->getContents(), true)["data"];

        self::assertEquals(sizeof($responseCountries), sizeof(self::$countries));
    }

    public function testGetResponse()
    {
        $response = self::$client->get("https://test.itc-benchmarking.edw.ro/api/v1/countries?page=0");

        $responseCountries = json_decode($response->getBody()->getContents(), true)["data"];

        //check if country exists in response, regardless of the order
        foreach (self::$countries as $countryName => $countryIsos) {

            $contains = false;

            foreach ($responseCountries as $responseCountry) {

                if (
                    $responseCountry["name"] == $countryName &&
                    $responseCountry["field_iso2"] == $countryIsos["field_iso2"] &&
                    $responseCountry["field_iso3"] == $countryIsos["field_iso3"]
                ) {
                    $contains = true;
                    break;
                }
            }

            self::assertEquals(true, $contains, $countryName . ' or iso is not contained by response');
        }
    }
}
