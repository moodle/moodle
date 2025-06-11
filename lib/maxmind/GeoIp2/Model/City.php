<?php

declare(strict_types=1);

namespace GeoIp2\Model;

/**
 * Model class for the data returned by City Plus web service and City
 * database.
 *
 * See https://dev.maxmind.com/geoip/docs/web-services?lang=en for more
 * details.
 */
class City extends Country
{
    /**
     * @var \GeoIp2\Record\City city data for the requested IP
     *                          address
     */
    public readonly \GeoIp2\Record\City $city;

    /**
     * @var \GeoIp2\Record\Location location data for the
     *                              requested IP address
     */
    public readonly \GeoIp2\Record\Location $location;

    /**
     * @var \GeoIp2\Record\Subdivision An object
     *                                 representing the most specific subdivision returned. If the response
     *                                 did not contain any subdivisions, this method returns an empty
     *                                 \GeoIp2\Record\Subdivision object.
     */
    public readonly \GeoIp2\Record\Subdivision $mostSpecificSubdivision;

    /**
     * @var \GeoIp2\Record\Postal postal data for the
     *                            requested IP address
     */
    public readonly \GeoIp2\Record\Postal $postal;

    /**
     * @var array<\GeoIp2\Record\Subdivision> An array of \GeoIp2\Record\Subdivision
     *                                        objects representing the country subdivisions for the requested IP
     *                                        address. The number and type of subdivisions varies by country, but a
     *                                        subdivision is typically a state, province, county, etc. Subdivisions
     *                                        are ordered from most general (largest) to most specific (smallest).
     *                                        If the response did not contain any subdivisions, this method returns
     *                                        an empty array.
     */
    public readonly array $subdivisions;

    /**
     * @ignore
     */
    public function __construct(array $raw, array $locales = ['en'])
    {
        parent::__construct($raw, $locales);

        $this->city = new \GeoIp2\Record\City($raw['city'] ?? [], $locales);
        $this->location = new \GeoIp2\Record\Location($raw['location'] ?? []);
        $this->postal = new \GeoIp2\Record\Postal($raw['postal'] ?? []);

        if (!isset($raw['subdivisions'])) {
            $this->subdivisions = [];
            $this->mostSpecificSubdivision =
                    new \GeoIp2\Record\Subdivision([], $locales);

            return;
        }

        $subdivisions = [];
        foreach ($raw['subdivisions'] as $sub) {
            $subdivisions[] =
                new \GeoIp2\Record\Subdivision($sub, $locales)
            ;
        }

        // Not using end as we don't want to modify internal pointer.
        $this->mostSpecificSubdivision =
            $subdivisions[\count($subdivisions) - 1];
        $this->subdivisions = $subdivisions;
    }

    public function jsonSerialize(): ?array
    {
        $js = parent::jsonSerialize();

        $city = $this->city->jsonSerialize();
        if (!empty($city)) {
            $js['city'] = $city;
        }

        $location = $this->location->jsonSerialize();
        if (!empty($location)) {
            $js['location'] = $location;
        }

        $postal =
         $this->postal->jsonSerialize();
        if (!empty($postal)) {
            $js['postal'] = $postal;
        }

        $subdivisions = [];
        foreach ($this->subdivisions as $sub) {
            $subdivisions[] = $sub->jsonSerialize();
        }
        if (!empty($subdivisions)) {
            $js['subdivisions'] = $subdivisions;
        }

        return $js;
    }
}
