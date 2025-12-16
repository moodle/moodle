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

namespace Google\Service\Container;

class MasterAuth extends \Google\Model
{
  /**
   * Output only. Base64-encoded public certificate used by clients to
   * authenticate to the cluster endpoint. Issued only if
   * client_certificate_config is set.
   *
   * @var string
   */
  public $clientCertificate;
  protected $clientCertificateConfigType = ClientCertificateConfig::class;
  protected $clientCertificateConfigDataType = '';
  /**
   * Output only. Base64-encoded private key used by clients to authenticate to
   * the cluster endpoint.
   *
   * @var string
   */
  public $clientKey;
  /**
   * Output only. Base64-encoded public certificate that is the root of trust
   * for the cluster.
   *
   * @var string
   */
  public $clusterCaCertificate;
  /**
   * The password to use for HTTP basic authentication to the master endpoint.
   * Because the master endpoint is open to the Internet, you should create a
   * strong password. If a password is provided for cluster creation, username
   * must be non-empty. Warning: basic authentication is deprecated, and will be
   * removed in GKE control plane versions 1.19 and newer. For a list of
   * recommended authentication methods, see:
   * https://cloud.google.com/kubernetes-engine/docs/how-to/api-server-
   * authentication
   *
   * @deprecated
   * @var string
   */
  public $password;
  /**
   * The username to use for HTTP basic authentication to the master endpoint.
   * For clusters v1.6.0 and later, basic authentication can be disabled by
   * leaving username unspecified (or setting it to the empty string). Warning:
   * basic authentication is deprecated, and will be removed in GKE control
   * plane versions 1.19 and newer. For a list of recommended authentication
   * methods, see: https://cloud.google.com/kubernetes-engine/docs/how-to/api-
   * server-authentication
   *
   * @deprecated
   * @var string
   */
  public $username;

  /**
   * Output only. Base64-encoded public certificate used by clients to
   * authenticate to the cluster endpoint. Issued only if
   * client_certificate_config is set.
   *
   * @param string $clientCertificate
   */
  public function setClientCertificate($clientCertificate)
  {
    $this->clientCertificate = $clientCertificate;
  }
  /**
   * @return string
   */
  public function getClientCertificate()
  {
    return $this->clientCertificate;
  }
  /**
   * Configuration for client certificate authentication on the cluster. For
   * clusters before v1.12, if no configuration is specified, a client
   * certificate is issued.
   *
   * @param ClientCertificateConfig $clientCertificateConfig
   */
  public function setClientCertificateConfig(ClientCertificateConfig $clientCertificateConfig)
  {
    $this->clientCertificateConfig = $clientCertificateConfig;
  }
  /**
   * @return ClientCertificateConfig
   */
  public function getClientCertificateConfig()
  {
    return $this->clientCertificateConfig;
  }
  /**
   * Output only. Base64-encoded private key used by clients to authenticate to
   * the cluster endpoint.
   *
   * @param string $clientKey
   */
  public function setClientKey($clientKey)
  {
    $this->clientKey = $clientKey;
  }
  /**
   * @return string
   */
  public function getClientKey()
  {
    return $this->clientKey;
  }
  /**
   * Output only. Base64-encoded public certificate that is the root of trust
   * for the cluster.
   *
   * @param string $clusterCaCertificate
   */
  public function setClusterCaCertificate($clusterCaCertificate)
  {
    $this->clusterCaCertificate = $clusterCaCertificate;
  }
  /**
   * @return string
   */
  public function getClusterCaCertificate()
  {
    return $this->clusterCaCertificate;
  }
  /**
   * The password to use for HTTP basic authentication to the master endpoint.
   * Because the master endpoint is open to the Internet, you should create a
   * strong password. If a password is provided for cluster creation, username
   * must be non-empty. Warning: basic authentication is deprecated, and will be
   * removed in GKE control plane versions 1.19 and newer. For a list of
   * recommended authentication methods, see:
   * https://cloud.google.com/kubernetes-engine/docs/how-to/api-server-
   * authentication
   *
   * @deprecated
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * The username to use for HTTP basic authentication to the master endpoint.
   * For clusters v1.6.0 and later, basic authentication can be disabled by
   * leaving username unspecified (or setting it to the empty string). Warning:
   * basic authentication is deprecated, and will be removed in GKE control
   * plane versions 1.19 and newer. For a list of recommended authentication
   * methods, see: https://cloud.google.com/kubernetes-engine/docs/how-to/api-
   * server-authentication
   *
   * @deprecated
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MasterAuth::class, 'Google_Service_Container_MasterAuth');
