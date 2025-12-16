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

namespace Google\Service\NetworkSecurity;

class ClientTlsPolicy extends \Google\Collection
{
  protected $collection_key = 'serverValidationCa';
  protected $clientCertificateType = GoogleCloudNetworksecurityV1CertificateProvider::class;
  protected $clientCertificateDataType = '';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Set of label tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Name of the ClientTlsPolicy resource. It matches the pattern `pro
   * jects/{project}/locations/{location}/clientTlsPolicies/{client_tls_policy}`
   *
   * @var string
   */
  public $name;
  protected $serverValidationCaType = ValidationCA::class;
  protected $serverValidationCaDataType = 'array';
  /**
   * Optional. Server Name Indication string to present to the server during TLS
   * handshake. E.g: "secure.example.com".
   *
   * @var string
   */
  public $sni;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Defines a mechanism to provision client identity (public and
   * private keys) for peer to peer authentication. The presence of this
   * dictates mTLS.
   *
   * @param GoogleCloudNetworksecurityV1CertificateProvider $clientCertificate
   */
  public function setClientCertificate(GoogleCloudNetworksecurityV1CertificateProvider $clientCertificate)
  {
    $this->clientCertificate = $clientCertificate;
  }
  /**
   * @return GoogleCloudNetworksecurityV1CertificateProvider
   */
  public function getClientCertificate()
  {
    return $this->clientCertificate;
  }
  /**
   * Output only. The timestamp when the resource was created.
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
   * Optional. Free-text description of the resource.
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
   * Optional. Set of label tags associated with the resource.
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
   * Required. Name of the ClientTlsPolicy resource. It matches the pattern `pro
   * jects/{project}/locations/{location}/clientTlsPolicies/{client_tls_policy}`
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
   * Optional. Defines the mechanism to obtain the Certificate Authority
   * certificate to validate the server certificate. If empty, client does not
   * validate the server certificate.
   *
   * @param ValidationCA[] $serverValidationCa
   */
  public function setServerValidationCa($serverValidationCa)
  {
    $this->serverValidationCa = $serverValidationCa;
  }
  /**
   * @return ValidationCA[]
   */
  public function getServerValidationCa()
  {
    return $this->serverValidationCa;
  }
  /**
   * Optional. Server Name Indication string to present to the server during TLS
   * handshake. E.g: "secure.example.com".
   *
   * @param string $sni
   */
  public function setSni($sni)
  {
    $this->sni = $sni;
  }
  /**
   * @return string
   */
  public function getSni()
  {
    return $this->sni;
  }
  /**
   * Output only. The timestamp when the resource was updated.
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
class_alias(ClientTlsPolicy::class, 'Google_Service_NetworkSecurity_ClientTlsPolicy');
