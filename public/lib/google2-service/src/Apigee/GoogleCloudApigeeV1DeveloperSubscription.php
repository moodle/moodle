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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1DeveloperSubscription extends \Google\Model
{
  /**
   * Name of the API product for which the developer is purchasing a
   * subscription.
   *
   * @var string
   */
  public $apiproduct;
  /**
   * Output only. Time when the API product subscription was created in
   * milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Time when the API product subscription ends in milliseconds since epoch.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Time when the API product subscription was last modified in
   * milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Output only. Name of the API product subscription.
   *
   * @var string
   */
  public $name;
  /**
   * Time when the API product subscription starts in milliseconds since epoch.
   *
   * @var string
   */
  public $startTime;

  /**
   * Name of the API product for which the developer is purchasing a
   * subscription.
   *
   * @param string $apiproduct
   */
  public function setApiproduct($apiproduct)
  {
    $this->apiproduct = $apiproduct;
  }
  /**
   * @return string
   */
  public function getApiproduct()
  {
    return $this->apiproduct;
  }
  /**
   * Output only. Time when the API product subscription was created in
   * milliseconds since epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Time when the API product subscription ends in milliseconds since epoch.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Time when the API product subscription was last modified in
   * milliseconds since epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Output only. Name of the API product subscription.
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
   * Time when the API product subscription starts in milliseconds since epoch.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DeveloperSubscription::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeveloperSubscription');
