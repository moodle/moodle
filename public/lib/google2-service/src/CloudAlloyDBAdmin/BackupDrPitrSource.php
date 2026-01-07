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

namespace Google\Service\CloudAlloyDBAdmin;

class BackupDrPitrSource extends \Google\Model
{
  /**
   * Required. The name of the backup resource with the format: * projects/{proj
   * ect}/locations/{location}/backupVaults/{backupvault_id}/dataSources/{dataso
   * urce_id}
   *
   * @var string
   */
  public $dataSource;
  /**
   * Required. The point in time to restore to.
   *
   * @var string
   */
  public $pointInTime;

  /**
   * Required. The name of the backup resource with the format: * projects/{proj
   * ect}/locations/{location}/backupVaults/{backupvault_id}/dataSources/{dataso
   * urce_id}
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Required. The point in time to restore to.
   *
   * @param string $pointInTime
   */
  public function setPointInTime($pointInTime)
  {
    $this->pointInTime = $pointInTime;
  }
  /**
   * @return string
   */
  public function getPointInTime()
  {
    return $this->pointInTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDrPitrSource::class, 'Google_Service_CloudAlloyDBAdmin_BackupDrPitrSource');
