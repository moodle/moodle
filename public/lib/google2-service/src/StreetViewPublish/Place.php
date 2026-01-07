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

namespace Google\Service\StreetViewPublish;

class Place extends \Google\Model
{
  /**
   * Output only. The language_code that the name is localized with. This should
   * be the language_code specified in the request, but may be a fallback.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. The name of the place, localized to the language_code.
   *
   * @var string
   */
  public $name;
  /**
   * Place identifier, as described in
   * https://developers.google.com/places/place-id.
   *
   * @var string
   */
  public $placeId;

  /**
   * Output only. The language_code that the name is localized with. This should
   * be the language_code specified in the request, but may be a fallback.
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
   * Output only. The name of the place, localized to the language_code.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Place identifier, as described in
   * https://developers.google.com/places/place-id.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Place::class, 'Google_Service_StreetViewPublish_Place');
