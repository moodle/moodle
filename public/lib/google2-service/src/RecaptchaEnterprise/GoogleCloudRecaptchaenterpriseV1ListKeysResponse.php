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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1ListKeysResponse extends \Google\Collection
{
  protected $collection_key = 'keys';
  protected $keysType = GoogleCloudRecaptchaenterpriseV1Key::class;
  protected $keysDataType = 'array';
  /**
   * Token to retrieve the next page of results. It is set to empty if no keys
   * remain in results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Key details.
   *
   * @param GoogleCloudRecaptchaenterpriseV1Key[] $keys
   */
  public function setKeys($keys)
  {
    $this->keys = $keys;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1Key[]
   */
  public function getKeys()
  {
    return $this->keys;
  }
  /**
   * Token to retrieve the next page of results. It is set to empty if no keys
   * remain in results.
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
class_alias(GoogleCloudRecaptchaenterpriseV1ListKeysResponse::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1ListKeysResponse');
