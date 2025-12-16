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

namespace Google\Service\Datastream;

class OracleSslConfig extends \Google\Model
{
  /**
   * Input only. PEM-encoded certificate of the CA that signed the source
   * database server's certificate.
   *
   * @var string
   */
  public $caCertificate;
  /**
   * Output only. Indicates whether the ca_certificate field has been set for
   * this Connection-Profile.
   *
   * @var bool
   */
  public $caCertificateSet;
  /**
   * Optional. The distinguished name (DN) mentioned in the server certificate.
   * This corresponds to SSL_SERVER_CERT_DN sqlnet parameter. Refer
   * https://docs.oracle.com/en/database/oracle/oracle-database/19/netrf/local-
   * naming-parameters-in-tns-ora-
   * file.html#GUID-70AB0695-A9AA-4A94-B141-4C605236EEB7 If this field is not
   * provided, the DN matching is not enforced.
   *
   * @var string
   */
  public $serverCertificateDistinguishedName;

  /**
   * Input only. PEM-encoded certificate of the CA that signed the source
   * database server's certificate.
   *
   * @param string $caCertificate
   */
  public function setCaCertificate($caCertificate)
  {
    $this->caCertificate = $caCertificate;
  }
  /**
   * @return string
   */
  public function getCaCertificate()
  {
    return $this->caCertificate;
  }
  /**
   * Output only. Indicates whether the ca_certificate field has been set for
   * this Connection-Profile.
   *
   * @param bool $caCertificateSet
   */
  public function setCaCertificateSet($caCertificateSet)
  {
    $this->caCertificateSet = $caCertificateSet;
  }
  /**
   * @return bool
   */
  public function getCaCertificateSet()
  {
    return $this->caCertificateSet;
  }
  /**
   * Optional. The distinguished name (DN) mentioned in the server certificate.
   * This corresponds to SSL_SERVER_CERT_DN sqlnet parameter. Refer
   * https://docs.oracle.com/en/database/oracle/oracle-database/19/netrf/local-
   * naming-parameters-in-tns-ora-
   * file.html#GUID-70AB0695-A9AA-4A94-B141-4C605236EEB7 If this field is not
   * provided, the DN matching is not enforced.
   *
   * @param string $serverCertificateDistinguishedName
   */
  public function setServerCertificateDistinguishedName($serverCertificateDistinguishedName)
  {
    $this->serverCertificateDistinguishedName = $serverCertificateDistinguishedName;
  }
  /**
   * @return string
   */
  public function getServerCertificateDistinguishedName()
  {
    return $this->serverCertificateDistinguishedName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OracleSslConfig::class, 'Google_Service_Datastream_OracleSslConfig');
