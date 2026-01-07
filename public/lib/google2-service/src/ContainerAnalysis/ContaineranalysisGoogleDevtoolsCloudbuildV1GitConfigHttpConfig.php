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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1GitConfigHttpConfig extends \Google\Model
{
  /**
   * SecretVersion resource of the HTTP proxy URL. The Service Account used in
   * the build (either the default Service Account or user-specified Service
   * Account) should have `secretmanager.versions.access` permissions on this
   * secret. The proxy URL should be in format `protocol://@]proxyhost[:port]`.
   *
   * @var string
   */
  public $proxySecretVersionName;

  /**
   * SecretVersion resource of the HTTP proxy URL. The Service Account used in
   * the build (either the default Service Account or user-specified Service
   * Account) should have `secretmanager.versions.access` permissions on this
   * secret. The proxy URL should be in format `protocol://@]proxyhost[:port]`.
   *
   * @param string $proxySecretVersionName
   */
  public function setProxySecretVersionName($proxySecretVersionName)
  {
    $this->proxySecretVersionName = $proxySecretVersionName;
  }
  /**
   * @return string
   */
  public function getProxySecretVersionName()
  {
    return $this->proxySecretVersionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1GitConfigHttpConfig::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1GitConfigHttpConfig');
