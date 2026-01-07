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

namespace Google\Service\AndroidManagement;

class DpcMigrationInfo extends \Google\Model
{
  /**
   * Output only. If this device was migrated from another DPC, the
   * additionalData field of the migration token is populated here.
   *
   * @var string
   */
  public $additionalData;
  /**
   * Output only. If this device was migrated from another DPC, this is its
   * package name. Not populated otherwise.
   *
   * @var string
   */
  public $previousDpc;

  /**
   * Output only. If this device was migrated from another DPC, the
   * additionalData field of the migration token is populated here.
   *
   * @param string $additionalData
   */
  public function setAdditionalData($additionalData)
  {
    $this->additionalData = $additionalData;
  }
  /**
   * @return string
   */
  public function getAdditionalData()
  {
    return $this->additionalData;
  }
  /**
   * Output only. If this device was migrated from another DPC, this is its
   * package name. Not populated otherwise.
   *
   * @param string $previousDpc
   */
  public function setPreviousDpc($previousDpc)
  {
    $this->previousDpc = $previousDpc;
  }
  /**
   * @return string
   */
  public function getPreviousDpc()
  {
    return $this->previousDpc;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DpcMigrationInfo::class, 'Google_Service_AndroidManagement_DpcMigrationInfo');
