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

class CloudAiPlatformTenantresourceInfraSpannerConfig extends \Google\Model
{
  protected $createDatabaseOptionsType = CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions::class;
  protected $createDatabaseOptionsDataType = '';
  /**
   * Input [Optional]. The KMS key name or the KMS grant name used for CMEK
   * encryption. Only set this field when provisioning new Infra Spanner
   * databases. For existing Infra Spanner databases, this field will be ignored
   * because CMEK re-encryption is not supported. For example,
   * projects//locations//keyRings//cryptoKeys/
   *
   * @var string
   */
  public $kmsKeyReference;
  /**
   * Input [Required]. The file path to the spanner SDL bundle.
   *
   * @var string
   */
  public $sdlBundlePath;
  /**
   * Input [Optional]. The spanner borg service account for delegating the kms
   * key to. For example, spanner-infra-cmek-nonprod@system.gserviceaccount.com,
   * for the nonprod universe.
   *
   * @var string
   */
  public $spannerBorgServiceAccount;
  /**
   * @var string
   */
  public $spannerLocalNamePrefix;
  /**
   * @var string
   */
  public $spannerNamespace;
  /**
   * Input [Required]. Every database in Spanner can be identified by the
   * following path name: /span//:
   *
   * @var string
   */
  public $spannerUniverse;

  /**
   * Input [Optional]. The options to create a spanner database. Note: give the
   * right options to ensure the right KMS key access audit logging and AxT
   * logging in expected logging category.
   *
   * @param CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions $createDatabaseOptions
   */
  public function setCreateDatabaseOptions(CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions $createDatabaseOptions)
  {
    $this->createDatabaseOptions = $createDatabaseOptions;
  }
  /**
   * @return CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions
   */
  public function getCreateDatabaseOptions()
  {
    return $this->createDatabaseOptions;
  }
  /**
   * Input [Optional]. The KMS key name or the KMS grant name used for CMEK
   * encryption. Only set this field when provisioning new Infra Spanner
   * databases. For existing Infra Spanner databases, this field will be ignored
   * because CMEK re-encryption is not supported. For example,
   * projects//locations//keyRings//cryptoKeys/
   *
   * @param string $kmsKeyReference
   */
  public function setKmsKeyReference($kmsKeyReference)
  {
    $this->kmsKeyReference = $kmsKeyReference;
  }
  /**
   * @return string
   */
  public function getKmsKeyReference()
  {
    return $this->kmsKeyReference;
  }
  /**
   * Input [Required]. The file path to the spanner SDL bundle.
   *
   * @param string $sdlBundlePath
   */
  public function setSdlBundlePath($sdlBundlePath)
  {
    $this->sdlBundlePath = $sdlBundlePath;
  }
  /**
   * @return string
   */
  public function getSdlBundlePath()
  {
    return $this->sdlBundlePath;
  }
  /**
   * Input [Optional]. The spanner borg service account for delegating the kms
   * key to. For example, spanner-infra-cmek-nonprod@system.gserviceaccount.com,
   * for the nonprod universe.
   *
   * @param string $spannerBorgServiceAccount
   */
  public function setSpannerBorgServiceAccount($spannerBorgServiceAccount)
  {
    $this->spannerBorgServiceAccount = $spannerBorgServiceAccount;
  }
  /**
   * @return string
   */
  public function getSpannerBorgServiceAccount()
  {
    return $this->spannerBorgServiceAccount;
  }
  /**
   * @param string $spannerLocalNamePrefix
   */
  public function setSpannerLocalNamePrefix($spannerLocalNamePrefix)
  {
    $this->spannerLocalNamePrefix = $spannerLocalNamePrefix;
  }
  /**
   * @return string
   */
  public function getSpannerLocalNamePrefix()
  {
    return $this->spannerLocalNamePrefix;
  }
  /**
   * @param string $spannerNamespace
   */
  public function setSpannerNamespace($spannerNamespace)
  {
    $this->spannerNamespace = $spannerNamespace;
  }
  /**
   * @return string
   */
  public function getSpannerNamespace()
  {
    return $this->spannerNamespace;
  }
  /**
   * Input [Required]. Every database in Spanner can be identified by the
   * following path name: /span//:
   *
   * @param string $spannerUniverse
   */
  public function setSpannerUniverse($spannerUniverse)
  {
    $this->spannerUniverse = $spannerUniverse;
  }
  /**
   * @return string
   */
  public function getSpannerUniverse()
  {
    return $this->spannerUniverse;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceInfraSpannerConfig::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceInfraSpannerConfig');
