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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1SignDataResponse extends \Google\Model
{
  protected $certificateProvisioningProcessType = GoogleChromeManagementVersionsV1CertificateProvisioningProcess::class;
  protected $certificateProvisioningProcessDataType = '';

  /**
   * Output only. The certificate provisioning process. The signature generated
   * by the client will be available in the `signature` field of
   * `CertificateProvisioningProcess`.
   *
   * @param GoogleChromeManagementVersionsV1CertificateProvisioningProcess $certificateProvisioningProcess
   */
  public function setCertificateProvisioningProcess(GoogleChromeManagementVersionsV1CertificateProvisioningProcess $certificateProvisioningProcess)
  {
    $this->certificateProvisioningProcess = $certificateProvisioningProcess;
  }
  /**
   * @return GoogleChromeManagementVersionsV1CertificateProvisioningProcess
   */
  public function getCertificateProvisioningProcess()
  {
    return $this->certificateProvisioningProcess;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1SignDataResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1SignDataResponse');
