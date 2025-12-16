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

class X509Parameters extends \Google\Collection
{
  protected $collection_key = 'policyIds';
  protected $additionalExtensionsType = X509Extension::class;
  protected $additionalExtensionsDataType = 'array';
  /**
   * Optional. Describes Online Certificate Status Protocol (OCSP) endpoint
   * addresses that appear in the "Authority Information Access" extension in
   * the certificate.
   *
   * @var string[]
   */
  public $aiaOcspServers;
  protected $caOptionsType = CaOptions::class;
  protected $caOptionsDataType = '';
  protected $keyUsageType = KeyUsage::class;
  protected $keyUsageDataType = '';
  protected $nameConstraintsType = NameConstraints::class;
  protected $nameConstraintsDataType = '';
  protected $policyIdsType = ObjectId::class;
  protected $policyIdsDataType = 'array';

  /**
   * Optional. Describes custom X.509 extensions.
   *
   * @param X509Extension[] $additionalExtensions
   */
  public function setAdditionalExtensions($additionalExtensions)
  {
    $this->additionalExtensions = $additionalExtensions;
  }
  /**
   * @return X509Extension[]
   */
  public function getAdditionalExtensions()
  {
    return $this->additionalExtensions;
  }
  /**
   * Optional. Describes Online Certificate Status Protocol (OCSP) endpoint
   * addresses that appear in the "Authority Information Access" extension in
   * the certificate.
   *
   * @param string[] $aiaOcspServers
   */
  public function setAiaOcspServers($aiaOcspServers)
  {
    $this->aiaOcspServers = $aiaOcspServers;
  }
  /**
   * @return string[]
   */
  public function getAiaOcspServers()
  {
    return $this->aiaOcspServers;
  }
  /**
   * Optional. Describes options in this X509Parameters that are relevant in a
   * CA certificate. If not specified, a default basic constraints extension
   * with `is_ca=false` will be added for leaf certificates.
   *
   * @param CaOptions $caOptions
   */
  public function setCaOptions(CaOptions $caOptions)
  {
    $this->caOptions = $caOptions;
  }
  /**
   * @return CaOptions
   */
  public function getCaOptions()
  {
    return $this->caOptions;
  }
  /**
   * Optional. Indicates the intended use for keys that correspond to a
   * certificate.
   *
   * @param KeyUsage $keyUsage
   */
  public function setKeyUsage(KeyUsage $keyUsage)
  {
    $this->keyUsage = $keyUsage;
  }
  /**
   * @return KeyUsage
   */
  public function getKeyUsage()
  {
    return $this->keyUsage;
  }
  /**
   * Optional. Describes the X.509 name constraints extension.
   *
   * @param NameConstraints $nameConstraints
   */
  public function setNameConstraints(NameConstraints $nameConstraints)
  {
    $this->nameConstraints = $nameConstraints;
  }
  /**
   * @return NameConstraints
   */
  public function getNameConstraints()
  {
    return $this->nameConstraints;
  }
  /**
   * Optional. Describes the X.509 certificate policy object identifiers, per
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.4.
   *
   * @param ObjectId[] $policyIds
   */
  public function setPolicyIds($policyIds)
  {
    $this->policyIds = $policyIds;
  }
  /**
   * @return ObjectId[]
   */
  public function getPolicyIds()
  {
    return $this->policyIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(X509Parameters::class, 'Google_Service_CertificateAuthorityService_X509Parameters');
