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

namespace Google\Service\CertificateAuthorityService;

class CaPool extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TIER_TIER_UNSPECIFIED = 'TIER_UNSPECIFIED';
  /**
   * Enterprise tier.
   */
  public const TIER_ENTERPRISE = 'ENTERPRISE';
  /**
   * DevOps tier.
   */
  public const TIER_DEVOPS = 'DEVOPS';
  protected $encryptionSpecType = EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  protected $issuancePolicyType = IssuancePolicy::class;
  protected $issuancePolicyDataType = '';
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name for this CaPool in the format
   * `projects/locations/caPools`.
   *
   * @var string
   */
  public $name;
  protected $publishingOptionsType = PublishingOptions::class;
  protected $publishingOptionsDataType = '';
  /**
   * Required. Immutable. The Tier of this CaPool.
   *
   * @var string
   */
  public $tier;

  /**
   * Optional. When EncryptionSpec is provided, the Subject, SubjectAltNames,
   * and the PEM-encoded certificate fields will be encrypted at rest.
   *
   * @param EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Optional. The IssuancePolicy to control how Certificates will be issued
   * from this CaPool.
   *
   * @param IssuancePolicy $issuancePolicy
   */
  public function setIssuancePolicy(IssuancePolicy $issuancePolicy)
  {
    $this->issuancePolicy = $issuancePolicy;
  }
  /**
   * @return IssuancePolicy
   */
  public function getIssuancePolicy()
  {
    return $this->issuancePolicy;
  }
  /**
   * Optional. Labels with user-defined metadata.
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
   * Identifier. The resource name for this CaPool in the format
   * `projects/locations/caPools`.
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
   * Optional. The PublishingOptions to follow when issuing Certificates from
   * any CertificateAuthority in this CaPool.
   *
   * @param PublishingOptions $publishingOptions
   */
  public function setPublishingOptions(PublishingOptions $publishingOptions)
  {
    $this->publishingOptions = $publishingOptions;
  }
  /**
   * @return PublishingOptions
   */
  public function getPublishingOptions()
  {
    return $this->publishingOptions;
  }
  /**
   * Required. Immutable. The Tier of this CaPool.
   *
   * Accepted values: TIER_UNSPECIFIED, ENTERPRISE, DEVOPS
   *
   * @param self::TIER_* $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return self::TIER_*
   */
  public function getTier()
  {
    return $this->tier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CaPool::class, 'Google_Service_CertificateAuthorityService_CaPool');
