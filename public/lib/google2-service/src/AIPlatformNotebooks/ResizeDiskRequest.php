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

namespace Google\Service\AIPlatformNotebooks;

class ResizeDiskRequest extends \Google\Model
{
  protected $bootDiskType = BootDisk::class;
  protected $bootDiskDataType = '';
  protected $dataDiskType = DataDisk::class;
  protected $dataDiskDataType = '';

  /**
   * Required. The boot disk to be resized. Only disk_size_gb will be used.
   *
   * @param BootDisk $bootDisk
   */
  public function setBootDisk(BootDisk $bootDisk)
  {
    $this->bootDisk = $bootDisk;
  }
  /**
   * @return BootDisk
   */
  public function getBootDisk()
  {
    return $this->bootDisk;
  }
  /**
   * Required. The data disk to be resized. Only disk_size_gb will be used.
   *
   * @param DataDisk $dataDisk
   */
  public function setDataDisk(DataDisk $dataDisk)
  {
    $this->dataDisk = $dataDisk;
  }
  /**
   * @return DataDisk
   */
  public function getDataDisk()
  {
    return $this->dataDisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResizeDiskRequest::class, 'Google_Service_AIPlatformNotebooks_ResizeDiskRequest');
