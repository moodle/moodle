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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2CollectUserEventRequest extends \Google\Model
{
  /**
   * The event timestamp in milliseconds. This prevents browser caching of
   * otherwise identical get requests. The name is abbreviated to reduce the
   * payload bytes.
   *
   * @var string
   */
  public $ets;
  /**
   * The prebuilt rule name that can convert a specific type of raw_json. For
   * example: "ga4_bq" rule for the GA4 user event schema.
   *
   * @var string
   */
  public $prebuiltRule;
  /**
   * An arbitrary serialized JSON string that contains necessary information
   * that can comprise a user event. When this field is specified, the
   * user_event field will be ignored. Note: line-delimited JSON is not
   * supported, a single JSON only.
   *
   * @var string
   */
  public $rawJson;
  /**
   * The URL including cgi-parameters but excluding the hash fragment with a
   * length limit of 5,000 characters. This is often more useful than the
   * referer URL, because many browsers only send the domain for 3rd party
   * requests.
   *
   * @var string
   */
  public $uri;
  /**
   * Required. URL encoded UserEvent proto with a length limit of 2,000,000
   * characters.
   *
   * @var string
   */
  public $userEvent;

  /**
   * The event timestamp in milliseconds. This prevents browser caching of
   * otherwise identical get requests. The name is abbreviated to reduce the
   * payload bytes.
   *
   * @param string $ets
   */
  public function setEts($ets)
  {
    $this->ets = $ets;
  }
  /**
   * @return string
   */
  public function getEts()
  {
    return $this->ets;
  }
  /**
   * The prebuilt rule name that can convert a specific type of raw_json. For
   * example: "ga4_bq" rule for the GA4 user event schema.
   *
   * @param string $prebuiltRule
   */
  public function setPrebuiltRule($prebuiltRule)
  {
    $this->prebuiltRule = $prebuiltRule;
  }
  /**
   * @return string
   */
  public function getPrebuiltRule()
  {
    return $this->prebuiltRule;
  }
  /**
   * An arbitrary serialized JSON string that contains necessary information
   * that can comprise a user event. When this field is specified, the
   * user_event field will be ignored. Note: line-delimited JSON is not
   * supported, a single JSON only.
   *
   * @param string $rawJson
   */
  public function setRawJson($rawJson)
  {
    $this->rawJson = $rawJson;
  }
  /**
   * @return string
   */
  public function getRawJson()
  {
    return $this->rawJson;
  }
  /**
   * The URL including cgi-parameters but excluding the hash fragment with a
   * length limit of 5,000 characters. This is often more useful than the
   * referer URL, because many browsers only send the domain for 3rd party
   * requests.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Required. URL encoded UserEvent proto with a length limit of 2,000,000
   * characters.
   *
   * @param string $userEvent
   */
  public function setUserEvent($userEvent)
  {
    $this->userEvent = $userEvent;
  }
  /**
   * @return string
   */
  public function getUserEvent()
  {
    return $this->userEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2CollectUserEventRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2CollectUserEventRequest');
