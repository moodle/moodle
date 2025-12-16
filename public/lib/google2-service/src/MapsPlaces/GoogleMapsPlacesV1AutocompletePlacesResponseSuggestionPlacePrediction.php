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

class GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction extends \Google\Collection
{
  protected $collection_key = 'types';
  /**
   * The length of the geodesic in meters from `origin` if `origin` is
   * specified. Certain predictions such as routes may not populate this field.
   *
   * @var int
   */
  public $distanceMeters;
  /**
   * The resource name of the suggested Place. This name can be used in other
   * APIs that accept Place names.
   *
   * @var string
   */
  public $place;
  /**
   * The unique identifier of the suggested Place. This identifier can be used
   * in other APIs that accept Place IDs.
   *
   * @var string
   */
  public $placeId;
  protected $structuredFormatType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat::class;
  protected $structuredFormatDataType = '';
  protected $textType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText::class;
  protected $textDataType = '';
  /**
   * List of types that apply to this Place from Table A or Table B in
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. A type is a categorization of a Place. Places with shared types will
   * share similar characteristics.
   *
   * @var string[]
   */
  public $types;

  /**
   * The length of the geodesic in meters from `origin` if `origin` is
   * specified. Certain predictions such as routes may not populate this field.
   *
   * @param int $distanceMeters
   */
  public function setDistanceMeters($distanceMeters)
  {
    $this->distanceMeters = $distanceMeters;
  }
  /**
   * @return int
   */
  public function getDistanceMeters()
  {
    return $this->distanceMeters;
  }
  /**
   * The resource name of the suggested Place. This name can be used in other
   * APIs that accept Place names.
   *
   * @param string $place
   */
  public function setPlace($place)
  {
    $this->place = $place;
  }
  /**
   * @return string
   */
  public function getPlace()
  {
    return $this->place;
  }
  /**
   * The unique identifier of the suggested Place. This identifier can be used
   * in other APIs that accept Place IDs.
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
   * A breakdown of the Place prediction into main text containing the name of
   * the Place and secondary text containing additional disambiguating features
   * (such as a city or region). `structured_format` is recommended for
   * developers who wish to show two separate, but related, UI elements.
   * Developers who wish to show a single UI element may want to use `text`
   * instead. They are two different ways to represent a Place prediction. Users
   * should not try to parse `structured_format` into `text` or vice versa.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat $structuredFormat
   */
  public function setStructuredFormat(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat $structuredFormat)
  {
    $this->structuredFormat = $structuredFormat;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat
   */
  public function getStructuredFormat()
  {
    return $this->structuredFormat;
  }
  /**
   * Contains the human-readable name for the returned result. For establishment
   * results, this is usually the business name and address. `text` is
   * recommended for developers who wish to show a single UI element. Developers
   * who wish to show two separate, but related, UI elements may want to use
   * `structured_format` instead. They are two different ways to represent a
   * Place prediction. Users should not try to parse `structured_format` into
   * `text` or vice versa. This text may be different from the `display_name`
   * returned by GetPlace. May be in mixed languages if the request `input` and
   * `language_code` are in different languages or if the Place does not have a
   * translation from the local language to `language_code`.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $text
   */
  public function setText(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $text)
  {
    $this->text = $text;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * List of types that apply to this Place from Table A or Table B in
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. A type is a categorization of a Place. Places with shared types will
   * share similar characteristics.
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
class_alias(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction');
