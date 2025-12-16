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

class GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm extends \Google\Model
{
  /**
   * Required. The index of the ip in the header. Positive indices 0, 1, 2, 3
   * chooses indices from the left (first ips) Negative indices -1, -2, -3
   * chooses indices from the right (last ips)
   *
   * @var int
   */
  public $ipHeaderIndex;
  /**
   * Required. The name of the header to extract the client ip from. We are
   * currently only supporting the X-Forwarded-For header.
   *
   * @var string
   */
  public $ipHeaderName;

  /**
   * Required. The index of the ip in the header. Positive indices 0, 1, 2, 3
   * chooses indices from the left (first ips) Negative indices -1, -2, -3
   * chooses indices from the right (last ips)
   *
   * @param int $ipHeaderIndex
   */
  public function setIpHeaderIndex($ipHeaderIndex)
  {
    $this->ipHeaderIndex = $ipHeaderIndex;
  }
  /**
   * @return int
   */
  public function getIpHeaderIndex()
  {
    return $this->ipHeaderIndex;
  }
  /**
   * Required. The name of the header to extract the client ip from. We are
   * currently only supporting the X-Forwarded-For header.
   *
   * @param string $ipHeaderName
   */
  public function setIpHeaderName($ipHeaderName)
  {
    $this->ipHeaderName = $ipHeaderName;
  }
  /**
   * @return string
   */
  public function getIpHeaderName()
  {
    return $this->ipHeaderName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EnvironmentClientIPResolutionConfigHeaderIndexAlgorithm');
