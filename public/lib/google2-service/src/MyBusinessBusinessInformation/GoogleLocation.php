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

namespace Google\Service\MyBusinessBusinessInformation;

class GoogleLocation extends \Google\Model
{
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * Resource name of this GoogleLocation, in the format
   * `googleLocations/{googleLocationId}`.
   *
   * @var string
   */
  public $name;
  /**
   * A URL that will redirect the user to the request admin rights UI. This
   * field is only present if the location has already been claimed by any user,
   * including the current user.
   *
   * @var string
   */
  public $requestAdminRightsUri;

  /**
   * The sparsely populated Location information. This field can be re-used in
   * CreateLocation if it is not currently claimed by a user.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Resource name of this GoogleLocation, in the format
   * `googleLocations/{googleLocationId}`.
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
   * A URL that will redirect the user to the request admin rights UI. This
   * field is only present if the location has already been claimed by any user,
   * including the current user.
   *
   * @param string $requestAdminRightsUri
   */
  public function setRequestAdminRightsUri($requestAdminRightsUri)
  {
    $this->requestAdminRightsUri = $requestAdminRightsUri;
  }
  /**
   * @return string
   */
  public function getRequestAdminRightsUri()
  {
    return $this->requestAdminRightsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleLocation::class, 'Google_Service_MyBusinessBusinessInformation_GoogleLocation');
