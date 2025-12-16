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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceTenantServiceAccountIdentity extends \Google\Model
{
  /**
   * Output only. The email address of the generated service account.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Input/Output [Required]. The service that the service account belongs to.
   * (e.g. cloudbuild.googleapis.com for GCB service accounts)
   *
   * @var string
   */
  public $serviceName;

  /**
   * Output only. The email address of the generated service account.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Input/Output [Required]. The service that the service account belongs to.
   * (e.g. cloudbuild.googleapis.com for GCB service accounts)
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceTenantServiceAccountIdentity::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceTenantServiceAccountIdentity');
