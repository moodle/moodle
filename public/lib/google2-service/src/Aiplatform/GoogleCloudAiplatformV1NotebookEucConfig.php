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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NotebookEucConfig extends \Google\Model
{
  /**
   * Output only. Whether ActAs check is bypassed for service account attached
   * to the VM. If false, we need ActAs check for the default Compute Engine
   * Service account. When a Runtime is created, a VM is allocated using Default
   * Compute Engine Service Account. Any user requesting to use this Runtime
   * requires Service Account User (ActAs) permission over this SA. If true,
   * Runtime owner is using EUC and does not require the above permission as VM
   * no longer use default Compute Engine SA, but a P4SA.
   *
   * @var bool
   */
  public $bypassActasCheck;
  /**
   * Input only. Whether EUC is disabled in this NotebookRuntimeTemplate. In
   * proto3, the default value of a boolean is false. In this way, by default
   * EUC will be enabled for NotebookRuntimeTemplate.
   *
   * @var bool
   */
  public $eucDisabled;

  /**
   * Output only. Whether ActAs check is bypassed for service account attached
   * to the VM. If false, we need ActAs check for the default Compute Engine
   * Service account. When a Runtime is created, a VM is allocated using Default
   * Compute Engine Service Account. Any user requesting to use this Runtime
   * requires Service Account User (ActAs) permission over this SA. If true,
   * Runtime owner is using EUC and does not require the above permission as VM
   * no longer use default Compute Engine SA, but a P4SA.
   *
   * @param bool $bypassActasCheck
   */
  public function setBypassActasCheck($bypassActasCheck)
  {
    $this->bypassActasCheck = $bypassActasCheck;
  }
  /**
   * @return bool
   */
  public function getBypassActasCheck()
  {
    return $this->bypassActasCheck;
  }
  /**
   * Input only. Whether EUC is disabled in this NotebookRuntimeTemplate. In
   * proto3, the default value of a boolean is false. In this way, by default
   * EUC will be enabled for NotebookRuntimeTemplate.
   *
   * @param bool $eucDisabled
   */
  public function setEucDisabled($eucDisabled)
  {
    $this->eucDisabled = $eucDisabled;
  }
  /**
   * @return bool
   */
  public function getEucDisabled()
  {
    return $this->eucDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NotebookEucConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NotebookEucConfig');
