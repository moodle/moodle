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

namespace Google\Service\Dataflow;

class DataDiskAssignment extends \Google\Collection
{
  protected $collection_key = 'dataDisks';
  /**
   * Mounted data disks. The order is important a data disk's 0-based index in
   * this list defines which persistent directory the disk is mounted to, for
   * example the list of { "myproject-1014-104817-4c2-harness-0-disk-0" }, {
   * "myproject-1014-104817-4c2-harness-0-disk-1" }.
   *
   * @var string[]
   */
  public $dataDisks;
  /**
   * VM instance name the data disks mounted to, for example
   * "myproject-1014-104817-4c2-harness-0".
   *
   * @var string
   */
  public $vmInstance;

  /**
   * Mounted data disks. The order is important a data disk's 0-based index in
   * this list defines which persistent directory the disk is mounted to, for
   * example the list of { "myproject-1014-104817-4c2-harness-0-disk-0" }, {
   * "myproject-1014-104817-4c2-harness-0-disk-1" }.
   *
   * @param string[] $dataDisks
   */
  public function setDataDisks($dataDisks)
  {
    $this->dataDisks = $dataDisks;
  }
  /**
   * @return string[]
   */
  public function getDataDisks()
  {
    return $this->dataDisks;
  }
  /**
   * VM instance name the data disks mounted to, for example
   * "myproject-1014-104817-4c2-harness-0".
   *
   * @param string $vmInstance
   */
  public function setVmInstance($vmInstance)
  {
    $this->vmInstance = $vmInstance;
  }
  /**
   * @return string
   */
  public function getVmInstance()
  {
    return $this->vmInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataDiskAssignment::class, 'Google_Service_Dataflow_DataDiskAssignment');
