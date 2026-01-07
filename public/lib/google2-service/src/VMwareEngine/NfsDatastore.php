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

namespace Google\Service\VMwareEngine;

class NfsDatastore extends \Google\Model
{
  protected $googleFileServiceType = GoogleFileService::class;
  protected $googleFileServiceDataType = '';
  protected $googleVmwareFileServiceType = GoogleVmwareFileService::class;
  protected $googleVmwareFileServiceDataType = '';
  protected $thirdPartyFileServiceType = ThirdPartyFileService::class;
  protected $thirdPartyFileServiceDataType = '';

  /**
   * Google service file service configuration
   *
   * @param GoogleFileService $googleFileService
   */
  public function setGoogleFileService(GoogleFileService $googleFileService)
  {
    $this->googleFileService = $googleFileService;
  }
  /**
   * @return GoogleFileService
   */
  public function getGoogleFileService()
  {
    return $this->googleFileService;
  }
  /**
   * GCVE file service configuration
   *
   * @param GoogleVmwareFileService $googleVmwareFileService
   */
  public function setGoogleVmwareFileService(GoogleVmwareFileService $googleVmwareFileService)
  {
    $this->googleVmwareFileService = $googleVmwareFileService;
  }
  /**
   * @return GoogleVmwareFileService
   */
  public function getGoogleVmwareFileService()
  {
    return $this->googleVmwareFileService;
  }
  /**
   * Third party file service configuration
   *
   * @param ThirdPartyFileService $thirdPartyFileService
   */
  public function setThirdPartyFileService(ThirdPartyFileService $thirdPartyFileService)
  {
    $this->thirdPartyFileService = $thirdPartyFileService;
  }
  /**
   * @return ThirdPartyFileService
   */
  public function getThirdPartyFileService()
  {
    return $this->thirdPartyFileService;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NfsDatastore::class, 'Google_Service_VMwareEngine_NfsDatastore');
