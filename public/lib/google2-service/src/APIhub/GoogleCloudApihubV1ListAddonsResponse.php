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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ListAddonsResponse extends \Google\Collection
{
  protected $collection_key = 'addons';
  protected $addonsType = GoogleCloudApihubV1Addon::class;
  protected $addonsDataType = 'array';
  /**
   * A token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of addons.
   *
   * @param GoogleCloudApihubV1Addon[] $addons
   */
  public function setAddons($addons)
  {
    $this->addons = $addons;
  }
  /**
   * @return GoogleCloudApihubV1Addon[]
   */
  public function getAddons()
  {
    return $this->addons;
  }
  /**
   * A token to retrieve the next page of results, or empty if there are no more
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ListAddonsResponse::class, 'Google_Service_APIhub_GoogleCloudApihubV1ListAddonsResponse');
