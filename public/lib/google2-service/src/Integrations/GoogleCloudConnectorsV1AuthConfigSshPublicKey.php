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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1AuthConfigSshPublicKey extends \Google\Model
{
  /**
   * Optional. Format of SSH Client cert.
   *
   * @var string
   */
  public $certType;
  protected $sshClientCertType = GoogleCloudConnectorsV1Secret::class;
  protected $sshClientCertDataType = '';
  protected $sshClientCertPassType = GoogleCloudConnectorsV1Secret::class;
  protected $sshClientCertPassDataType = '';
  /**
   * Optional. The user account used to authenticate.
   *
   * @var string
   */
  public $username;

  /**
   * Optional. Format of SSH Client cert.
   *
   * @param string $certType
   */
  public function setCertType($certType)
  {
    $this->certType = $certType;
  }
  /**
   * @return string
   */
  public function getCertType()
  {
    return $this->certType;
  }
  /**
   * Optional. SSH Client Cert. It should contain both public and private key.
   *
   * @param GoogleCloudConnectorsV1Secret $sshClientCert
   */
  public function setSshClientCert(GoogleCloudConnectorsV1Secret $sshClientCert)
  {
    $this->sshClientCert = $sshClientCert;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getSshClientCert()
  {
    return $this->sshClientCert;
  }
  /**
   * Optional. Password (passphrase) for ssh client certificate if it has one.
   *
   * @param GoogleCloudConnectorsV1Secret $sshClientCertPass
   */
  public function setSshClientCertPass(GoogleCloudConnectorsV1Secret $sshClientCertPass)
  {
    $this->sshClientCertPass = $sshClientCertPass;
  }
  /**
   * @return GoogleCloudConnectorsV1Secret
   */
  public function getSshClientCertPass()
  {
    return $this->sshClientCertPass;
  }
  /**
   * Optional. The user account used to authenticate.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1AuthConfigSshPublicKey::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1AuthConfigSshPublicKey');
