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

class GoogleCloudApigeeV1ServiceIssuersMapping extends \Google\Collection
{
  protected $collection_key = 'emailIds';
  /**
   * List of trusted issuer email ids.
   *
   * @var string[]
   */
  public $emailIds;
  /**
   * String indicating the Apigee service name.
   *
   * @var string
   */
  public $service;

  /**
   * List of trusted issuer email ids.
   *
   * @param string[] $emailIds
   */
  public function setEmailIds($emailIds)
  {
    $this->emailIds = $emailIds;
  }
  /**
   * @return string[]
   */
  public function getEmailIds()
  {
    return $this->emailIds;
  }
  /**
   * String indicating the Apigee service name.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ServiceIssuersMapping::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ServiceIssuersMapping');
