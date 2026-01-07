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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1PlaceAddressComponent extends \Google\Collection
{
  protected $collection_key = 'types';
  /**
   * The language used to format this components, in CLDR notation.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The full text description or name of the address component. For example, an
   * address component for the country Australia may have a long_name of
   * "Australia".
   *
   * @var string
   */
  public $longText;
  /**
   * An abbreviated textual name for the address component, if available. For
   * example, an address component for the country of Australia may have a
   * short_name of "AU".
   *
   * @var string
   */
  public $shortText;
  /**
   * An array indicating the type(s) of the address component.
   *
   * @var string[]
   */
  public $types;

  /**
   * The language used to format this components, in CLDR notation.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The full text description or name of the address component. For example, an
   * address component for the country Australia may have a long_name of
   * "Australia".
   *
   * @param string $longText
   */
  public function setLongText($longText)
  {
    $this->longText = $longText;
  }
  /**
   * @return string
   */
  public function getLongText()
  {
    return $this->longText;
  }
  /**
   * An abbreviated textual name for the address component, if available. For
   * example, an address component for the country of Australia may have a
   * short_name of "AU".
   *
   * @param string $shortText
   */
  public function setShortText($shortText)
  {
    $this->shortText = $shortText;
  }
  /**
   * @return string
   */
  public function getShortText()
  {
    return $this->shortText;
  }
  /**
   * An array indicating the type(s) of the address component.
   *
   * @param string[] $types
   */
  public function setTypes($types)
  {
    $this->types = $types;
  }
  /**
   * @return string[]
   */
  public function getTypes()
  {
    return $this->types;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceAddressComponent::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceAddressComponent');
