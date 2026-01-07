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

namespace Google\Service\MyBusinessPlaceActions;

class ListPlaceActionTypeMetadataResponse extends \Google\Collection
{
  protected $collection_key = 'placeActionTypeMetadata';
  /**
   * If the number of action types exceeded the requested page size, this field
   * will be populated with a token to fetch the next page on a subsequent call
   * to `placeActionTypeMetadata.list`. If there are no more results, this field
   * will not be present in the response.
   *
   * @var string
   */
  public $nextPageToken;
  protected $placeActionTypeMetadataType = PlaceActionTypeMetadata::class;
  protected $placeActionTypeMetadataDataType = 'array';

  /**
   * If the number of action types exceeded the requested page size, this field
   * will be populated with a token to fetch the next page on a subsequent call
   * to `placeActionTypeMetadata.list`. If there are no more results, this field
   * will not be present in the response.
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
   * A collection of metadata for the available place action types.
   *
   * @param PlaceActionTypeMetadata[] $placeActionTypeMetadata
   */
  public function setPlaceActionTypeMetadata($placeActionTypeMetadata)
  {
    $this->placeActionTypeMetadata = $placeActionTypeMetadata;
  }
  /**
   * @return PlaceActionTypeMetadata[]
   */
  public function getPlaceActionTypeMetadata()
  {
    return $this->placeActionTypeMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPlaceActionTypeMetadataResponse::class, 'Google_Service_MyBusinessPlaceActions_ListPlaceActionTypeMetadataResponse');
