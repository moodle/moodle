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

namespace Google\Service\NetAppFiles;

class ValidateDirectoryServiceRequest extends \Google\Model
{
  /**
   * Directory service type is not specified.
   */
  public const DIRECTORY_SERVICE_TYPE_DIRECTORY_SERVICE_TYPE_UNSPECIFIED = 'DIRECTORY_SERVICE_TYPE_UNSPECIFIED';
  /**
   * Active directory policy attached to the storage pool.
   */
  public const DIRECTORY_SERVICE_TYPE_ACTIVE_DIRECTORY = 'ACTIVE_DIRECTORY';
  /**
   * Type of directory service policy attached to the storage pool.
   *
   * @var string
   */
  public $directoryServiceType;

  /**
   * Type of directory service policy attached to the storage pool.
   *
   * Accepted values: DIRECTORY_SERVICE_TYPE_UNSPECIFIED, ACTIVE_DIRECTORY
   *
   * @param self::DIRECTORY_SERVICE_TYPE_* $directoryServiceType
   */
  public function setDirectoryServiceType($directoryServiceType)
  {
    $this->directoryServiceType = $directoryServiceType;
  }
  /**
   * @return self::DIRECTORY_SERVICE_TYPE_*
   */
  public function getDirectoryServiceType()
  {
    return $this->directoryServiceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValidateDirectoryServiceRequest::class, 'Google_Service_NetAppFiles_ValidateDirectoryServiceRequest');
