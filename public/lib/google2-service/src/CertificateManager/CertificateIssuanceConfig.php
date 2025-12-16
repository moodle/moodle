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

namespace Google\Service\CertificateManager;

class CertificateIssuanceConfig extends \Google\Model
{
  /**
   * Unspecified key algorithm.
   */
  public const KEY_ALGORITHM_KEY_ALGORITHM_UNSPECIFIED = 'KEY_ALGORITHM_UNSPECIFIED';
  /**
   * Specifies RSA with a 2048-bit modulus.
   */
  public const KEY_ALGORITHM_RSA_2048 = 'RSA_2048';
  /**
   * Specifies ECDSA with curve P256.
   */
  public const KEY_ALGORITHM_ECDSA_P256 = 'ECDSA_P256';
  protected $certificateAuthorityConfigType = CertificateAuthorityConfig::class;
  protected $certificateAuthorityConfigDataType = '';
  /**
   * Output only. The creation timestamp of a CertificateIssuanceConfig.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a
   * CertificateIssuanceConfig.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The key algorithm to use when generating the private key.
   *
   * @var string
   */
  public $keyAlgorithm;
  /**
   * Optional. Set of labels associated with a CertificateIssuanceConfig.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Workload certificate lifetime requested.
   *
   * @var string
   */
  public $lifetime;
  /**
   * Identifier. A user-defined name of the certificate issuance config.
   * CertificateIssuanceConfig names must be unique globally and match pattern
   * `projects/locations/certificateIssuanceConfigs`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Specifies the percentage of elapsed time of the certificate
   * lifetime to wait before renewing the certificate. Must be a number between
   * 1-99, inclusive.
   *
   * @var int
   */
  public $rotationWindowPercentage;
  /**
   * Output only. The last update timestamp of a CertificateIssuanceConfig.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The CA that issues the workload certificate. It includes the CA
   * address, type, authentication to CA service, etc.
   *
   * @param CertificateAuthorityConfig $certificateAuthorityConfig
   */
  public function setCertificateAuthorityConfig(CertificateAuthorityConfig $certificateAuthorityConfig)
  {
    $this->certificateAuthorityConfig = $certificateAuthorityConfig;
  }
  /**
   * @return CertificateAuthorityConfig
   */
  public function getCertificateAuthorityConfig()
  {
    return $this->certificateAuthorityConfig;
  }
  /**
   * Output only. The creation timestamp of a CertificateIssuanceConfig.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. One or more paragraphs of text description of a
   * CertificateIssuanceConfig.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The key algorithm to use when generating the private key.
   *
   * Accepted values: KEY_ALGORITHM_UNSPECIFIED, RSA_2048, ECDSA_P256
   *
   * @param self::KEY_ALGORITHM_* $keyAlgorithm
   */
  public function setKeyAlgorithm($keyAlgorithm)
  {
    $this->keyAlgorithm = $keyAlgorithm;
  }
  /**
   * @return self::KEY_ALGORITHM_*
   */
  public function getKeyAlgorithm()
  {
    return $this->keyAlgorithm;
  }
  /**
   * Optional. Set of labels associated with a CertificateIssuanceConfig.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. Workload certificate lifetime requested.
   *
   * @param string $lifetime
   */
  public function setLifetime($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  /**
   * @return string
   */
  public function getLifetime()
  {
    return $this->lifetime;
  }
  /**
   * Identifier. A user-defined name of the certificate issuance config.
   * CertificateIssuanceConfig names must be unique globally and match pattern
   * `projects/locations/certificateIssuanceConfigs`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Specifies the percentage of elapsed time of the certificate
   * lifetime to wait before renewing the certificate. Must be a number between
   * 1-99, inclusive.
   *
   * @param int $rotationWindowPercentage
   */
  public function setRotationWindowPercentage($rotationWindowPercentage)
  {
    $this->rotationWindowPercentage = $rotationWindowPercentage;
  }
  /**
   * @return int
   */
  public function getRotationWindowPercentage()
  {
    return $this->rotationWindowPercentage;
  }
  /**
   * Output only. The last update timestamp of a CertificateIssuanceConfig.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateIssuanceConfig::class, 'Google_Service_CertificateManager_CertificateIssuanceConfig');
