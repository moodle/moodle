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

namespace Google\Service\ChromeManagement\Resource;

use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1CertificateProvisioningProcess;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessRequest;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1SetFailureRequest;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1SetFailureResponse;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1SignDataRequest;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1UploadCertificateRequest;
use Google\Service\ChromeManagement\GoogleChromeManagementVersionsV1UploadCertificateResponse;
use Google\Service\ChromeManagement\GoogleLongrunningOperation;

/**
 * The "certificateProvisioningProcesses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromemanagementService = new Google\Service\ChromeManagement(...);
 *   $certificateProvisioningProcesses = $chromemanagementService->customers_certificateProvisioningProcesses;
 *  </code>
 */
class CustomersCertificateProvisioningProcesses extends \Google\Service\Resource
{
  /**
   * Claims a certificate provisioning process. For each certificate provisioning
   * process, this operation can succeed only for one `caller_instance_id`.
   * (certificateProvisioningProcesses.claim)
   *
   * @param string $name Required. Resource name of the
   * `CertificateProvisioningProcess` to claim. The name pattern is given as `cust
   * omers/{customer}/certificateProvisioningProcesses/{certificate_provisioning_p
   * rocess}` with `{customer}` being the obfuscated customer id and
   * `{certificate_provisioning_process}` being the certificate provisioning
   * process id.
   * @param GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessResponse
   * @throws \Google\Service\Exception
   */
  public function claim($name, GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('claim', [$params], GoogleChromeManagementVersionsV1ClaimCertificateProvisioningProcessResponse::class);
  }
  /**
   * Retrieves a certificate provisioning process.
   * (certificateProvisioningProcesses.get)
   *
   * @param string $name Required. Resource name of the
   * `CertificateProvisioningProcess` to return. The name pattern is given as `cus
   * tomers/{customer}/certificateProvisioningProcesses/{certificate_provisioning_
   * process}` with `{customer}` being the obfuscated customer id and
   * `{certificate_provisioning_process}` being the certificate provisioning
   * process id.
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1CertificateProvisioningProcess
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleChromeManagementVersionsV1CertificateProvisioningProcess::class);
  }
  /**
   * Marks a certificate provisioning process as failed.
   * (certificateProvisioningProcesses.setFailure)
   *
   * @param string $name Required. Resource name of the
   * `CertificateProvisioningProcess` to return. The name pattern is given as `cus
   * tomers/{customer}/certificateProvisioningProcesses/{certificate_provisioning_
   * process}` with `{customer}` being the obfuscated customer id and
   * `{certificate_provisioning_process}` being the certificate provisioning
   * process id.
   * @param GoogleChromeManagementVersionsV1SetFailureRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1SetFailureResponse
   * @throws \Google\Service\Exception
   */
  public function setFailure($name, GoogleChromeManagementVersionsV1SetFailureRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setFailure', [$params], GoogleChromeManagementVersionsV1SetFailureResponse::class);
  }
  /**
   * Requests the client that initiated a certificate provisioning process to sign
   * data. This should only be called after `ClaimCertificateProvisioningProcess`
   * has been successfully executed. (certificateProvisioningProcesses.signData)
   *
   * @param string $name Required. Resource name of the
   * `CertificateProvisioningProcess` to return. The name pattern is given as `cus
   * tomers/{customer}/certificateProvisioningProcesses/{certificate_provisioning_
   * process}` with `{customer}` being the obfuscated customer id and
   * `{certificate_provisioning_process}` being the certificate provisioning
   * process id.
   * @param GoogleChromeManagementVersionsV1SignDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function signData($name, GoogleChromeManagementVersionsV1SignDataRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('signData', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Uploads a successfully issued certificate for a certificate provisioning
   * process. (certificateProvisioningProcesses.uploadCertificate)
   *
   * @param string $name Required. Resource name of the
   * `CertificateProvisioningProcess` to return. The name pattern is given as `cus
   * tomers/{customer}/certificateProvisioningProcesses/{certificate_provisioning_
   * process}` with `{customer}` being the obfuscated customer id and
   * `{certificate_provisioning_process}` being the certificate provisioning
   * process id.
   * @param GoogleChromeManagementVersionsV1UploadCertificateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleChromeManagementVersionsV1UploadCertificateResponse
   * @throws \Google\Service\Exception
   */
  public function uploadCertificate($name, GoogleChromeManagementVersionsV1UploadCertificateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('uploadCertificate', [$params], GoogleChromeManagementVersionsV1UploadCertificateResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomersCertificateProvisioningProcesses::class, 'Google_Service_ChromeManagement_Resource_CustomersCertificateProvisioningProcesses');
