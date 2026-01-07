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

class SearchGoogleLocationsRequest extends \Google\Model
{
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * The number of matches to return. The default value is 3, with a maximum of
   * 10. Note that latency may increase if more are requested. There is no
   * pagination.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Text query to search for. The search results from a query string will be
   * less accurate than if providing an exact location, but can provide more
   * inexact matches.
   *
   * @var string
   */
  public $query;

  /**
   * Location to search for. If provided, will find locations which match the
   * provided location details, which must include a value for the title.
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
   * The number of matches to return. The default value is 3, with a maximum of
   * 10. Note that latency may increase if more are requested. There is no
   * pagination.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Text query to search for. The search results from a query string will be
   * less accurate than if providing an exact location, but can provide more
   * inexact matches.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchGoogleLocationsRequest::class, 'Google_Service_MyBusinessBusinessInformation_SearchGoogleLocationsRequest');
