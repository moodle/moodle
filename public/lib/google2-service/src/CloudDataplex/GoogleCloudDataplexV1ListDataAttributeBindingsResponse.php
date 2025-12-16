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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ListDataAttributeBindingsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachableLocations';
  protected $dataAttributeBindingsType = GoogleCloudDataplexV1DataAttributeBinding::class;
  protected $dataAttributeBindingsDataType = 'array';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachableLocations;

  /**
   * DataAttributeBindings under the given parent Location.
   *
   * @param GoogleCloudDataplexV1DataAttributeBinding[] $dataAttributeBindings
   */
  public function setDataAttributeBindings($dataAttributeBindings)
  {
    $this->dataAttributeBindings = $dataAttributeBindings;
  }
  /**
   * @return GoogleCloudDataplexV1DataAttributeBinding[]
   */
  public function getDataAttributeBindings()
  {
    return $this->dataAttributeBindings;
  }
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Locations that could not be reached.
   *
   * @param string[] $unreachableLocations
   */
  public function setUnreachableLocations($unreachableLocations)
  {
    $this->unreachableLocations = $unreachableLocations;
  }
  /**
   * @return string[]
   */
  public function getUnreachableLocations()
  {
    return $this->unreachableLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ListDataAttributeBindingsResponse::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ListDataAttributeBindingsResponse');
