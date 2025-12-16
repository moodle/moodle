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

namespace Google\Service\Networkconnectivity;

class ListHubSpokesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  /**
   * The token for the next page of the response. To see more results, use this
   * value as the page_token for your next request. If this value is empty,
   * there are no more results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $spokesType = Spoke::class;
  protected $spokesDataType = 'array';
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * The token for the next page of the response. To see more results, use this
   * value as the page_token for your next request. If this value is empty,
   * there are no more results.
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
   * The requested spokes. The spoke fields can be partially populated based on
   * the `view` field in the request message.
   *
   * @param Spoke[] $spokes
   */
  public function setSpokes($spokes)
  {
    $this->spokes = $spokes;
  }
  /**
   * @return Spoke[]
   */
  public function getSpokes()
  {
    return $this->spokes;
  }
  /**
   * Locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListHubSpokesResponse::class, 'Google_Service_Networkconnectivity_ListHubSpokesResponse');
