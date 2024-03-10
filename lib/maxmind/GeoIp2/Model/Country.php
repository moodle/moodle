<?php

declare(strict_types=1);

namespace GeoIp2\Model;

/**
 * Model class for the data returned by GeoIP2 Country web service and database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more details.
 */
class Country implements \JsonSerializable
{
    /**
     * @var \GeoIp2\Record\Continent continent data for the
     *                               requested IP address
     */
    public readonly \GeoIp2\Record\Continent $continent;

    /**
     * @var \GeoIp2\Record\Country Country data for the requested
     *                             IP address. This object represents the country where MaxMind believes the
     *                             end user is located.
     */
    public readonly \GeoIp2\Record\Country $country;

    /**
     * @var \GeoIp2\Record\MaxMind data related to your MaxMind
     *                             account
     */
    public readonly \GeoIp2\Record\MaxMind $maxmind;

    /**
     * @var \GeoIp2\Record\Country Registered country
     *                             data for the requested IP address. This record represents the country
     *                             where the ISP has registered a given IP block and may differ from the
     *                             user's country.
     */
    public readonly \GeoIp2\Record\Country $registeredCountry;

    /**
     * @var \GeoIp2\Record\RepresentedCountry * Represented country data for the requested IP address. The represented
     *                                        country is used for things like military bases. It is only present when
     *                                        the represented country differs from the country.
     */
    public readonly \GeoIp2\Record\RepresentedCountry $representedCountry;

    /**
     * @var \GeoIp2\Record\Traits data for the traits of the
     *                            requested IP address
     */
    public readonly \GeoIp2\Record\Traits $traits;

    /**
     * @ignore
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        $this->continent = new \GeoIp2\Record\Continent(
            $raw['continent'] ?? [],
            $locales
        );
        $this->country = new \GeoIp2\Record\Country(
            $raw['country'] ?? [],
            $locales
        );
        $this->maxmind = new \GeoIp2\Record\MaxMind($raw['maxmind'] ?? []);
        $this->registeredCountry = new \GeoIp2\Record\Country(
            $raw['registered_country'] ?? [],
            $locales
        );
        $this->representedCountry = new \GeoIp2\Record\RepresentedCountry(
            $raw['represented_country'] ?? [],
            $locales
        );
        $this->traits = new \GeoIp2\Record\Traits($raw['traits'] ?? []);
    }

    public function jsonSerialize(): ?array
    {
        $js = [];
        $continent = $this->continent->jsonSerialize();
        if (!empty($continent)) {
            $js['continent'] = $continent;
        }
        $country = $this->country->jsonSerialize();
        if (!empty($country)) {
            $js['country'] = $country;
        }
        $maxmind = $this->maxmind->jsonSerialize();
        if (!empty($maxmind)) {
            $js['maxmind'] = $maxmind;
        }
        $registeredCountry = $this->registeredCountry->jsonSerialize();
        if (!empty($registeredCountry)) {
            $js['registered_country'] = $registeredCountry;
        }
        $representedCountry = $this->representedCountry->jsonSerialize();
        if (!empty($representedCountry)) {
            $js['represented_country'] = $representedCountry;
        }
        $traits = $this->traits->jsonSerialize();
        if (!empty($traits)) {
            $js['traits'] = $traits;
        }

        return $js;
    }
}
