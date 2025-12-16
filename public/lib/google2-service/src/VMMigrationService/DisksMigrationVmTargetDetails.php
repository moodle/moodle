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

class DisksMigrationVmTargetDetails extends \Google\Model
{
  /**
   * Output only. The URI of the Compute Engine VM.
   *
   * @var string
   */
  public $vmUri;

  /**
   * Output only. The URI of the Compute Engine VM.
   *
   * @param string $vmUri
   */
  public function setVmUri($vmUri)
  {
    $this->vmUri = $vmUri;
  }
  /**
   * @return string
   */
  public function getVmUri()
  {
    return $this->vmUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DisksMigrationVmTargetDetails::class, 'Google_Service_VMMigrationService_DisksMigrationVmTargetDetails');
