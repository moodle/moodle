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

namespace Google\Service\VMMigrationService;

class VmAttachmentDetails extends \Google\Model
{
  /**
   * Optional. Specifies a unique device name of your choice that is reflected
   * into the /dev/disk/by-id/google-* tree of a Linux operating system running
   * within the instance. If not specified, the server chooses a default device
   * name to apply to this disk, in the form persistent-disk-x, where x is a
   * number assigned by Google Compute Engine. This field is only applicable for
   * persistent disks.
   *
   * @var string
   */
  public $deviceName;

  /**
   * Optional. Specifies a unique device name of your choice that is reflected
   * into the /dev/disk/by-id/google-* tree of a Linux operating system running
   * within the instance. If not specified, the server chooses a default device
   * name to apply to this disk, in the form persistent-disk-x, where x is a
   * number assigned by Google Compute Engine. This field is only applicable for
   * persistent disks.
   *
   * @param string $deviceName
   */
  public function setDeviceName($deviceName)
  {
    $this->deviceName = $deviceName;
  }
  /**
   * @return string
   */
  public function getDeviceName()
  {
    return $this->deviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmAttachmentDetails::class, 'Google_Service_VMMigrationService_VmAttachmentDetails');
