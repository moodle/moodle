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

namespace Google\Service\GKEHub;

class IdentityServiceServerConfig extends \Google\Model
{
  /**
   * Optional. Contains a Base64 encoded, PEM formatted certificate authority
   * certificate for the LDAP server. This must be provided for the "ldaps" and
   * "startTLS" connections.
   *
   * @var string
   */
  public $certificateAuthorityData;
  /**
   * Optional. Defines the connection type to communicate with the LDAP server.
   * If `starttls` or `ldaps` is specified, the certificate_authority_data
   * should not be empty.
   *
   * @var string
   */
  public $connectionType;
  /**
   * Required. Defines the hostname or IP of the LDAP server. Port is optional
   * and will default to 389, if unspecified. For example, "ldap.server.example"
   * or "10.10.10.10:389".
   *
   * @var string
   */
  public $host;

  /**
   * Optional. Contains a Base64 encoded, PEM formatted certificate authority
   * certificate for the LDAP server. This must be provided for the "ldaps" and
   * "startTLS" connections.
   *
   * @param string $certificateAuthorityData
   */
  public function setCertificateAuthorityData($certificateAuthorityData)
  {
    $this->certificateAuthorityData = $certificateAuthorityData;
  }
  /**
   * @return string
   */
  public function getCertificateAuthorityData()
  {
    return $this->certificateAuthorityData;
  }
  /**
   * Optional. Defines the connection type to communicate with the LDAP server.
   * If `starttls` or `ldaps` is specified, the certificate_authority_data
   * should not be empty.
   *
   * @param string $connectionType
   */
  public function setConnectionType($connectionType)
  {
    $this->connectionType = $connectionType;
  }
  /**
   * @return string
   */
  public function getConnectionType()
  {
    return $this->connectionType;
  }
  /**
   * Required. Defines the hostname or IP of the LDAP server. Port is optional
   * and will default to 389, if unspecified. For example, "ldap.server.example"
   * or "10.10.10.10:389".
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceServerConfig::class, 'Google_Service_GKEHub_IdentityServiceServerConfig');
