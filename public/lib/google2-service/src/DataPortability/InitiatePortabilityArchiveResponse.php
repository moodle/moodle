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

namespace Google\Service\DataPortability;

class InitiatePortabilityArchiveResponse extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const ACCESS_TYPE_ACCESS_TYPE_UNSPECIFIED = 'ACCESS_TYPE_UNSPECIFIED';
  /**
   * One-time access to the requested scopes.
   */
  public const ACCESS_TYPE_ACCESS_TYPE_ONE_TIME = 'ACCESS_TYPE_ONE_TIME';
  /**
   * Multiple exports allowed over 30 days. Enum value subject to change before
   * launch.
   */
  public const ACCESS_TYPE_ACCESS_TYPE_TIME_BASED = 'ACCESS_TYPE_TIME_BASED';
  /**
   * The access type of the Archive job initiated by the API.
   *
   * @var string
   */
  public $accessType;
  /**
   * The archive job ID that is initiated in the API. This can be used to get
   * the state of the job.
   *
   * @var string
   */
  public $archiveJobId;

  /**
   * The access type of the Archive job initiated by the API.
   *
   * Accepted values: ACCESS_TYPE_UNSPECIFIED, ACCESS_TYPE_ONE_TIME,
   * ACCESS_TYPE_TIME_BASED
   *
   * @param self::ACCESS_TYPE_* $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return self::ACCESS_TYPE_*
   */
  public function getAccessType()
  {
    return $this->accessType;
  }
  /**
   * The archive job ID that is initiated in the API. This can be used to get
   * the state of the job.
   *
   * @param string $archiveJobId
   */
  public function setArchiveJobId($archiveJobId)
  {
    $this->archiveJobId = $archiveJobId;
  }
  /**
   * @return string
   */
  public function getArchiveJobId()
  {
    return $this->archiveJobId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InitiatePortabilityArchiveResponse::class, 'Google_Service_DataPortability_InitiatePortabilityArchiveResponse');
