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

namespace Google\Service\OracleDatabase;

class StorageSizeDetails extends \Google\Model
{
  /**
   * Output only. The data storage size, in gigabytes, that is applicable for
   * virtual machine DBSystem.
   *
   * @var int
   */
  public $dataStorageSizeInGbs;
  /**
   * Output only. The RECO/REDO storage size, in gigabytes, that is applicable
   * for virtual machine DBSystem.
   *
   * @var int
   */
  public $recoStorageSizeInGbs;

  /**
   * Output only. The data storage size, in gigabytes, that is applicable for
   * virtual machine DBSystem.
   *
   * @param int $dataStorageSizeInGbs
   */
  public function setDataStorageSizeInGbs($dataStorageSizeInGbs)
  {
    $this->dataStorageSizeInGbs = $dataStorageSizeInGbs;
  }
  /**
   * @return int
   */
  public function getDataStorageSizeInGbs()
  {
    return $this->dataStorageSizeInGbs;
  }
  /**
   * Output only. The RECO/REDO storage size, in gigabytes, that is applicable
   * for virtual machine DBSystem.
   *
   * @param int $recoStorageSizeInGbs
   */
  public function setRecoStorageSizeInGbs($recoStorageSizeInGbs)
  {
    $this->recoStorageSizeInGbs = $recoStorageSizeInGbs;
  }
  /**
   * @return int
   */
  public function getRecoStorageSizeInGbs()
  {
    return $this->recoStorageSizeInGbs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageSizeDetails::class, 'Google_Service_OracleDatabase_StorageSizeDetails');
