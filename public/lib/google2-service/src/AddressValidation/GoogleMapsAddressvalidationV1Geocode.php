<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\AddressValidation;

class GoogleMapsAddressvalidationV1Geocode extends \Google\Collection
{
  protected $collection_key = 'placeTypes';
  protected $boundsType = GoogleGeoTypeViewport::class;
  protected $boundsDataType = '';
  /**
   * The size of the geocoded place, in meters. This is another measure of the
   * coarseness of the geocoded location, but in physical size rather than in
   * semantic meaning.
   *
   * @var float
   */
  public $featureSizeMeters;
  protected $locationType = GoogleTypeLatLng::class;
  protected $locationDataType = '';
  /**
   * The PlaceID of the place this input geocodes to. For more information about
   * Place IDs see
   * [here](https://developers.google.com/maps/documentation/places/web-
   * service/place-id).
   *
   * @var string
   */
  public $placeId;
  /**
   * The type(s) of place that the input geocoded to. For example, `['locality',
   * 'political']`. The full list of types can be found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types).
   *
   * @var string[]
   */
  public $placeTypes;
  protected $plusCodeType = GoogleMapsAddressvalidationV1PlusCode::class;
  protected $plusCodeDataType = '';

  /**
   * The bounds of the geocoded place.
   *
   * @param GoogleGeoTypeViewport $bounds
   */
  public function setBounds(GoogleGeoTypeViewport $bounds)
  {
    $this->bounds = $bounds;
  }
  /**
   * @return GoogleGeoTypeViewport
   */
  public function getBounds()
  {
    return $this->bounds;
  }
  /**
   * The size of the geocoded place, in meters. This is another measure of the
   * coarseness of the geocoded location, but in physical size rather than in
   * semantic meaning.
   *
   * @param float $featureSizeMeters
   */
  public function setFeatureSizeMeters($featureSizeMeters)
  {
    $this->featureSizeMeters = $featureSizeMeters;
  }
  /**
   * @return float
   */
  public function getFeatureSizeMeters()
  {
    return $this->featureSizeMeters;
  }
  /**
   * The geocoded location of the input. Using place IDs is preferred over using
   * addresses, latitude/longitude coordinates, or plus codes. Using coordinates
   * when routing or calculating driving directions will always result in the
   * point being snapped to the road nearest to those coordinates. This may not
   * be a road that will quickly or safely lead to the destination and may not
   * be near an access point to the property. Additionally, when a location is
   * reverse geocoded, there is no guarantee that the returned address will
   * match the original.
   *
   * @param GoogleTypeLatLng $location
   */
  public function setLocation(GoogleTypeLatLng $location)
  {
    $this->location = $location;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The PlaceID of the place this input geocodes to. For more information about
   * Place IDs see
   * [here](https://developers.google.com/maps/documentation/places/web-
   * service/place-id).
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
  /**
   * The type(s) of place that the input geocoded to. For example, `['locality',
   * 'political']`. The full list of types can be found
   * [here](https://developers.google.com/maps/documentation/geocoding/requests-
   * geocoding#Types).
   *
   * @param string[] $placeTypes
   */
  public function setPlaceTypes($placeTypes)
  {
    $this->placeTypes = $placeTypes;
  }
  /**
   * @return string[]
   */
  public function getPlaceTypes()
  {
    return $this->placeTypes;
  }
  /**
   * The plus code corresponding to the `location`.
   *
   * @param GoogleMapsAddressvalidationV1PlusCode $plusCode
   */
  public function setPlusCode(GoogleMapsAddressvalidationV1PlusCode $plusCode)
  {
    $this->plusCode = $plusCode;
  }
  /**
   * @return GoogleMapsAddressvalidationV1PlusCode
   */
  public function getPlusCode()
  {
    return $this->plusCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsAddressvalidationV1Geocode::class, 'Google_Service_AddressValidation_GoogleMapsAddressvalidationV1Geocode');
