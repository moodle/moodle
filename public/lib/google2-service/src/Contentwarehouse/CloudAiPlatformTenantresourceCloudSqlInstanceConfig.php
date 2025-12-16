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

class CloudAiPlatformTenantresourceCloudSqlInstanceConfig extends \Google\Collection
{
  protected $collection_key = 'mdbRolesForCorpAccess';
  /**
   * Output only. The CloudSQL instance connection name.
   *
   * @var string
   */
  public $cloudSqlInstanceConnectionName;
  /**
   * Input/Output [Optional]. The CloudSQL instance name within SLM instance. If
   * not set, a random UUIC will be generated as instance name.
   *
   * @var string
   */
  public $cloudSqlInstanceName;
  /**
   * Input [Optional]. The KMS key name or the KMS grant name used for CMEK
   * encryption. Only set this field when provisioning new CloudSQL instances.
   * For existing CloudSQL instances, this field will be ignored because CMEK
   * re-encryption is not supported.
   *
   * @var string
   */
  public $kmsKeyReference;
  /**
   * Input [Optional]. MDB roles for corp access to CloudSQL instance.
   *
   * @var string[]
   */
  public $mdbRolesForCorpAccess;
  /**
   * Output only. The SLM instance's full resource name.
   *
   * @var string
   */
  public $slmInstanceName;
  /**
   * Input [Required]. The SLM instance template to provision CloudSQL.
   *
   * @var string
   */
  public $slmInstanceTemplate;
  /**
   * Input [Required]. The SLM instance type to provision CloudSQL.
   *
   * @var string
   */
  public $slmInstanceType;

  /**
   * Output only. The CloudSQL instance connection name.
   *
   * @param string $cloudSqlInstanceConnectionName
   */
  public function setCloudSqlInstanceConnectionName($cloudSqlInstanceConnectionName)
  {
    $this->cloudSqlInstanceConnectionName = $cloudSqlInstanceConnectionName;
  }
  /**
   * @return string
   */
  public function getCloudSqlInstanceConnectionName()
  {
    return $this->cloudSqlInstanceConnectionName;
  }
  /**
   * Input/Output [Optional]. The CloudSQL instance name within SLM instance. If
   * not set, a random UUIC will be generated as instance name.
   *
   * @param string $cloudSqlInstanceName
   */
  public function setCloudSqlInstanceName($cloudSqlInstanceName)
  {
    $this->cloudSqlInstanceName = $cloudSqlInstanceName;
  }
  /**
   * @return string
   */
  public function getCloudSqlInstanceName()
  {
    return $this->cloudSqlInstanceName;
  }
  /**
   * Input [Optional]. The KMS key name or the KMS grant name used for CMEK
   * encryption. Only set this field when provisioning new CloudSQL instances.
   * For existing CloudSQL instances, this field will be ignored because CMEK
   * re-encryption is not supported.
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
   * Input [Optional]. MDB roles for corp access to CloudSQL instance.
   *
   * @param string[] $mdbRolesForCorpAccess
   */
  public function setMdbRolesForCorpAccess($mdbRolesForCorpAccess)
  {
    $this->mdbRolesForCorpAccess = $mdbRolesForCorpAccess;
  }
  /**
   * @return string[]
   */
  public function getMdbRolesForCorpAccess()
  {
    return $this->mdbRolesForCorpAccess;
  }
  /**
   * Output only. The SLM instance's full resource name.
   *
   * @param string $slmInstanceName
   */
  public function setSlmInstanceName($slmInstanceName)
  {
    $this->slmInstanceName = $slmInstanceName;
  }
  /**
   * @return string
   */
  public function getSlmInstanceName()
  {
    return $this->slmInstanceName;
  }
  /**
   * Input [Required]. The SLM instance template to provision CloudSQL.
   *
   * @param string $slmInstanceTemplate
   */
  public function setSlmInstanceTemplate($slmInstanceTemplate)
  {
    $this->slmInstanceTemplate = $slmInstanceTemplate;
  }
  /**
   * @return string
   */
  public function getSlmInstanceTemplate()
  {
    return $this->slmInstanceTemplate;
  }
  /**
   * Input [Required]. The SLM instance type to provision CloudSQL.
   *
   * @param string $slmInstanceType
   */
  public function setSlmInstanceType($slmInstanceType)
  {
    $this->slmInstanceType = $slmInstanceType;
  }
  /**
   * @return string
   */
  public function getSlmInstanceType()
  {
    return $this->slmInstanceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceCloudSqlInstanceConfig::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceCloudSqlInstanceConfig');
