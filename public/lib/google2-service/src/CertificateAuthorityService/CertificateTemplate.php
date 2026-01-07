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

class CertificateTemplate extends \Google\Model
{
  /**
   * Output only. The time at which this CertificateTemplate was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A human-readable description of scenarios this template is
   * intended for.
   *
   * @var string
   */
  public $description;
  protected $identityConstraintsType = CertificateIdentityConstraints::class;
  protected $identityConstraintsDataType = '';
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The maximum lifetime allowed for issued Certificates that use
   * this template. If the issuing CaPool resource's IssuancePolicy specifies a
   * maximum_lifetime the minimum of the two durations will be the maximum
   * lifetime for issued Certificates. Note that if the issuing
   * CertificateAuthority expires before a Certificate's requested
   * maximum_lifetime, the effective lifetime will be explicitly truncated to
   * match it.
   *
   * @var string
   */
  public $maximumLifetime;
  /**
   * Identifier. The resource name for this CertificateTemplate in the format
   * `projects/locations/certificateTemplates`.
   *
   * @var string
   */
  public $name;
  protected $passthroughExtensionsType = CertificateExtensionConstraints::class;
  protected $passthroughExtensionsDataType = '';
  protected $predefinedValuesType = X509Parameters::class;
  protected $predefinedValuesDataType = '';
  /**
   * Output only. The time at which this CertificateTemplate was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which this CertificateTemplate was created.
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
   * Optional. A human-readable description of scenarios this template is
   * intended for.
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
   * Optional. Describes constraints on identities that may be appear in
   * Certificates issued using this template. If this is omitted, then this
   * template will not add restrictions on a certificate's identity.
   *
   * @param CertificateIdentityConstraints $identityConstraints
   */
  public function setIdentityConstraints(CertificateIdentityConstraints $identityConstraints)
  {
    $this->identityConstraints = $identityConstraints;
  }
  /**
   * @return CertificateIdentityConstraints
   */
  public function getIdentityConstraints()
  {
    return $this->identityConstraints;
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
   * Optional. The maximum lifetime allowed for issued Certificates that use
   * this template. If the issuing CaPool resource's IssuancePolicy specifies a
   * maximum_lifetime the minimum of the two durations will be the maximum
   * lifetime for issued Certificates. Note that if the issuing
   * CertificateAuthority expires before a Certificate's requested
   * maximum_lifetime, the effective lifetime will be explicitly truncated to
   * match it.
   *
   * @param string $maximumLifetime
   */
  public function setMaximumLifetime($maximumLifetime)
  {
    $this->maximumLifetime = $maximumLifetime;
  }
  /**
   * @return string
   */
  public function getMaximumLifetime()
  {
    return $this->maximumLifetime;
  }
  /**
   * Identifier. The resource name for this CertificateTemplate in the format
   * `projects/locations/certificateTemplates`.
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
   * Optional. Describes the set of X.509 extensions that may appear in a
   * Certificate issued using this CertificateTemplate. If a certificate request
   * sets extensions that don't appear in the passthrough_extensions, those
   * extensions will be dropped. If the issuing CaPool's IssuancePolicy defines
   * baseline_values that don't appear here, the certificate issuance request
   * will fail. If this is omitted, then this template will not add restrictions
   * on a certificate's X.509 extensions. These constraints do not apply to
   * X.509 extensions set in this CertificateTemplate's predefined_values.
   *
   * @param CertificateExtensionConstraints $passthroughExtensions
   */
  public function setPassthroughExtensions(CertificateExtensionConstraints $passthroughExtensions)
  {
    $this->passthroughExtensions = $passthroughExtensions;
  }
  /**
   * @return CertificateExtensionConstraints
   */
  public function getPassthroughExtensions()
  {
    return $this->passthroughExtensions;
  }
  /**
   * Optional. A set of X.509 values that will be applied to all issued
   * certificates that use this template. If the certificate request includes
   * conflicting values for the same properties, they will be overwritten by the
   * values defined here. If the issuing CaPool's IssuancePolicy defines
   * conflicting baseline_values for the same properties, the certificate
   * issuance request will fail.
   *
   * @param X509Parameters $predefinedValues
   */
  public function setPredefinedValues(X509Parameters $predefinedValues)
  {
    $this->predefinedValues = $predefinedValues;
  }
  /**
   * @return X509Parameters
   */
  public function getPredefinedValues()
  {
    return $this->predefinedValues;
  }
  /**
   * Output only. The time at which this CertificateTemplate was updated.
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
class_alias(CertificateTemplate::class, 'Google_Service_CertificateAuthorityService_CertificateTemplate');
