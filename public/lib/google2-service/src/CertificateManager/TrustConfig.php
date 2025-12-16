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

class TrustConfig extends \Google\Collection
{
  protected $collection_key = 'trustStores';
  protected $allowlistedCertificatesType = AllowlistedCertificate::class;
  protected $allowlistedCertificatesDataType = 'array';
  /**
   * Output only. The creation timestamp of a TrustConfig.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. One or more paragraphs of text description of a TrustConfig.
   *
   * @var string
   */
  public $description;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Set of labels associated with a TrustConfig.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. A user-defined name of the trust config. TrustConfig names must
   * be unique globally and match pattern `projects/locations/trustConfigs`.
   *
   * @var string
   */
  public $name;
  protected $spiffeTrustStoresType = TrustStore::class;
  protected $spiffeTrustStoresDataType = 'map';
  protected $trustStoresType = TrustStore::class;
  protected $trustStoresDataType = 'array';
  /**
   * Output only. The last update timestamp of a TrustConfig.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. A certificate matching an allowlisted certificate is always
   * considered valid as long as the certificate is parseable, proof of private
   * key possession is established, and constraints on the certificate's SAN
   * field are met.
   *
   * @param AllowlistedCertificate[] $allowlistedCertificates
   */
  public function setAllowlistedCertificates($allowlistedCertificates)
  {
    $this->allowlistedCertificates = $allowlistedCertificates;
  }
  /**
   * @return AllowlistedCertificate[]
   */
  public function getAllowlistedCertificates()
  {
    return $this->allowlistedCertificates;
  }
  /**
   * Output only. The creation timestamp of a TrustConfig.
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
   * Optional. One or more paragraphs of text description of a TrustConfig.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Set of labels associated with a TrustConfig.
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
   * Identifier. A user-defined name of the trust config. TrustConfig names must
   * be unique globally and match pattern `projects/locations/trustConfigs`.
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
   * Optional. Defines a mapping from a trust domain to a TrustStore. This is
   * used for SPIFFE certificate validation.
   *
   * @param TrustStore[] $spiffeTrustStores
   */
  public function setSpiffeTrustStores($spiffeTrustStores)
  {
    $this->spiffeTrustStores = $spiffeTrustStores;
  }
  /**
   * @return TrustStore[]
   */
  public function getSpiffeTrustStores()
  {
    return $this->spiffeTrustStores;
  }
  /**
   * Optional. Set of trust stores to perform validation against. This field is
   * supported when TrustConfig is configured with Load Balancers, currently not
   * supported for SPIFFE certificate validation. Only one TrustStore specified
   * is currently allowed.
   *
   * @param TrustStore[] $trustStores
   */
  public function setTrustStores($trustStores)
  {
    $this->trustStores = $trustStores;
  }
  /**
   * @return TrustStore[]
   */
  public function getTrustStores()
  {
    return $this->trustStores;
  }
  /**
   * Output only. The last update timestamp of a TrustConfig.
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
class_alias(TrustConfig::class, 'Google_Service_CertificateManager_TrustConfig');
