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

class IssuancePolicy extends \Google\Collection
{
  protected $collection_key = 'allowedKeyTypes';
  protected $allowedIssuanceModesType = IssuanceModes::class;
  protected $allowedIssuanceModesDataType = '';
  protected $allowedKeyTypesType = AllowedKeyType::class;
  protected $allowedKeyTypesDataType = 'array';
  /**
   * Optional. The duration to backdate all certificates issued from this
   * CaPool. If not set, the certificates will be issued with a not_before_time
   * of the issuance time (i.e. the current time). If set, the certificates will
   * be issued with a not_before_time of the issuance time minus the
   * backdate_duration. The not_after_time will be adjusted to preserve the
   * requested lifetime. The backdate_duration must be less than or equal to 48
   * hours.
   *
   * @var string
   */
  public $backdateDuration;
  protected $baselineValuesType = X509Parameters::class;
  protected $baselineValuesDataType = '';
  protected $identityConstraintsType = CertificateIdentityConstraints::class;
  protected $identityConstraintsDataType = '';
  /**
   * Optional. The maximum lifetime allowed for issued Certificates. Note that
   * if the issuing CertificateAuthority expires before a Certificate resource's
   * requested maximum_lifetime, the effective lifetime will be explicitly
   * truncated to match it.
   *
   * @var string
   */
  public $maximumLifetime;
  protected $passthroughExtensionsType = CertificateExtensionConstraints::class;
  protected $passthroughExtensionsDataType = '';

  /**
   * Optional. If specified, then only methods allowed in the IssuanceModes may
   * be used to issue Certificates.
   *
   * @param IssuanceModes $allowedIssuanceModes
   */
  public function setAllowedIssuanceModes(IssuanceModes $allowedIssuanceModes)
  {
    $this->allowedIssuanceModes = $allowedIssuanceModes;
  }
  /**
   * @return IssuanceModes
   */
  public function getAllowedIssuanceModes()
  {
    return $this->allowedIssuanceModes;
  }
  /**
   * Optional. If any AllowedKeyType is specified, then the certificate
   * request's public key must match one of the key types listed here.
   * Otherwise, any key may be used.
   *
   * @param AllowedKeyType[] $allowedKeyTypes
   */
  public function setAllowedKeyTypes($allowedKeyTypes)
  {
    $this->allowedKeyTypes = $allowedKeyTypes;
  }
  /**
   * @return AllowedKeyType[]
   */
  public function getAllowedKeyTypes()
  {
    return $this->allowedKeyTypes;
  }
  /**
   * Optional. The duration to backdate all certificates issued from this
   * CaPool. If not set, the certificates will be issued with a not_before_time
   * of the issuance time (i.e. the current time). If set, the certificates will
   * be issued with a not_before_time of the issuance time minus the
   * backdate_duration. The not_after_time will be adjusted to preserve the
   * requested lifetime. The backdate_duration must be less than or equal to 48
   * hours.
   *
   * @param string $backdateDuration
   */
  public function setBackdateDuration($backdateDuration)
  {
    $this->backdateDuration = $backdateDuration;
  }
  /**
   * @return string
   */
  public function getBackdateDuration()
  {
    return $this->backdateDuration;
  }
  /**
   * Optional. A set of X.509 values that will be applied to all certificates
   * issued through this CaPool. If a certificate request includes conflicting
   * values for the same properties, they will be overwritten by the values
   * defined here. If a certificate request uses a CertificateTemplate that
   * defines conflicting predefined_values for the same properties, the
   * certificate issuance request will fail.
   *
   * @param X509Parameters $baselineValues
   */
  public function setBaselineValues(X509Parameters $baselineValues)
  {
    $this->baselineValues = $baselineValues;
  }
  /**
   * @return X509Parameters
   */
  public function getBaselineValues()
  {
    return $this->baselineValues;
  }
  /**
   * Optional. Describes constraints on identities that may appear in
   * Certificates issued through this CaPool. If this is omitted, then this
   * CaPool will not add restrictions on a certificate's identity.
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
   * Optional. The maximum lifetime allowed for issued Certificates. Note that
   * if the issuing CertificateAuthority expires before a Certificate resource's
   * requested maximum_lifetime, the effective lifetime will be explicitly
   * truncated to match it.
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
   * Optional. Describes the set of X.509 extensions that may appear in a
   * Certificate issued through this CaPool. If a certificate request sets
   * extensions that don't appear in the passthrough_extensions, those
   * extensions will be dropped. If a certificate request uses a
   * CertificateTemplate with predefined_values that don't appear here, the
   * certificate issuance request will fail. If this is omitted, then this
   * CaPool will not add restrictions on a certificate's X.509 extensions. These
   * constraints do not apply to X.509 extensions set in this CaPool's
   * baseline_values.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IssuancePolicy::class, 'Google_Service_CertificateAuthorityService_IssuancePolicy');
