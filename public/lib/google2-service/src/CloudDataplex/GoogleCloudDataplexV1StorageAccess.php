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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1StorageAccess extends \Google\Model
{
  /**
   * Access mode unspecified.
   */
  public const READ_ACCESS_MODE_UNSPECIFIED = 'ACCESS_MODE_UNSPECIFIED';
  /**
   * Default. Data is accessed directly using storage APIs.
   */
  public const READ_DIRECT = 'DIRECT';
  /**
   * Data is accessed through a managed interface using BigQuery APIs.
   */
  public const READ_MANAGED = 'MANAGED';
  /**
   * Output only. Describes the read access mechanism of the data. Not user
   * settable.
   *
   * @var string
   */
  public $read;

  /**
   * Output only. Describes the read access mechanism of the data. Not user
   * settable.
   *
   * Accepted values: ACCESS_MODE_UNSPECIFIED, DIRECT, MANAGED
   *
   * @param self::READ_* $read
   */
  public function setRead($read)
  {
    $this->read = $read;
  }
  /**
   * @return self::READ_*
   */
  public function getRead()
  {
    return $this->read;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1StorageAccess::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1StorageAccess');
