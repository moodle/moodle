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

class GoogleCloudApigeeV1EntityMetadata extends \Google\Model
{
  /**
   * Time at which the API proxy was created, in milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Time at which the API proxy was most recently modified, in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * The type of entity described
   *
   * @var string
   */
  public $subType;

  /**
   * Time at which the API proxy was created, in milliseconds since epoch.
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
   * Time at which the API proxy was most recently modified, in milliseconds
   * since epoch.
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
   * The type of entity described
   *
   * @param string $subType
   */
  public function setSubType($subType)
  {
    $this->subType = $subType;
  }
  /**
   * @return string
   */
  public function getSubType()
  {
    return $this->subType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1EntityMetadata::class, 'Google_Service_Apigee_GoogleCloudApigeeV1EntityMetadata');
