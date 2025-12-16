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

namespace Google\Service\CloudRedis;

class FailoverInstanceRequest extends \Google\Model
{
  /**
   * Defaults to LIMITED_DATA_LOSS if a data protection mode is not specified.
   */
  public const DATA_PROTECTION_MODE_DATA_PROTECTION_MODE_UNSPECIFIED = 'DATA_PROTECTION_MODE_UNSPECIFIED';
  /**
   * Instance failover will be protected with data loss control. More
   * specifically, the failover will only be performed if the current
   * replication offset diff between primary and replica is under a certain
   * threshold.
   */
  public const DATA_PROTECTION_MODE_LIMITED_DATA_LOSS = 'LIMITED_DATA_LOSS';
  /**
   * Instance failover will be performed without data loss control.
   */
  public const DATA_PROTECTION_MODE_FORCE_DATA_LOSS = 'FORCE_DATA_LOSS';
  /**
   * Optional. Available data protection modes that the user can choose. If it's
   * unspecified, data protection mode will be LIMITED_DATA_LOSS by default.
   *
   * @var string
   */
  public $dataProtectionMode;

  /**
   * Optional. Available data protection modes that the user can choose. If it's
   * unspecified, data protection mode will be LIMITED_DATA_LOSS by default.
   *
   * Accepted values: DATA_PROTECTION_MODE_UNSPECIFIED, LIMITED_DATA_LOSS,
   * FORCE_DATA_LOSS
   *
   * @param self::DATA_PROTECTION_MODE_* $dataProtectionMode
   */
  public function setDataProtectionMode($dataProtectionMode)
  {
    $this->dataProtectionMode = $dataProtectionMode;
  }
  /**
   * @return self::DATA_PROTECTION_MODE_*
   */
  public function getDataProtectionMode()
  {
    return $this->dataProtectionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FailoverInstanceRequest::class, 'Google_Service_CloudRedis_FailoverInstanceRequest');
