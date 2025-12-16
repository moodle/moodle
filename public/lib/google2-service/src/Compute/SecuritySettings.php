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

namespace Google\Service\Compute;

class SecuritySettings extends \Google\Collection
{
  protected $collection_key = 'subjectAltNames';
  protected $awsV4AuthenticationType = AWSV4Signature::class;
  protected $awsV4AuthenticationDataType = '';
  /**
   * Optional. A URL referring to a networksecurity.ClientTlsPolicy resource
   * that describes how clients should authenticate with this service's
   * backends.
   *
   *  clientTlsPolicy only applies to a globalBackendService with the
   * loadBalancingScheme set to INTERNAL_SELF_MANAGED.
   *
   *  If left blank, communications are not encrypted.
   *
   * @var string
   */
  public $clientTlsPolicy;
  /**
   * Optional. A list of Subject Alternative Names (SANs) that the client
   * verifies during a mutual TLS handshake with an server/endpoint for
   * thisBackendService. When the server presents its X.509 certificate to the
   * client, the client inspects the certificate'ssubjectAltName field. If the
   * field contains one of the specified values, the communication continues.
   * Otherwise, it fails. This additional check enables the client to verify
   * that the server is authorized to run the requested service.
   *
   *  Note that the contents of the server certificate's subjectAltName field
   * are configured by the Public Key Infrastructure which provisions server
   * identities.
   *
   *  Only applies to a global BackendService withloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED. Only applies when BackendService has an
   * attachedclientTlsPolicy with clientCertificate (mTLS mode).
   *
   * @var string[]
   */
  public $subjectAltNames;

  /**
   * The configuration needed to generate a signature for access to private
   * storage buckets that support AWS's Signature Version 4 for authentication.
   * Allowed only for INTERNET_IP_PORT and INTERNET_FQDN_PORT NEG backends.
   *
   * @param AWSV4Signature $awsV4Authentication
   */
  public function setAwsV4Authentication(AWSV4Signature $awsV4Authentication)
  {
    $this->awsV4Authentication = $awsV4Authentication;
  }
  /**
   * @return AWSV4Signature
   */
  public function getAwsV4Authentication()
  {
    return $this->awsV4Authentication;
  }
  /**
   * Optional. A URL referring to a networksecurity.ClientTlsPolicy resource
   * that describes how clients should authenticate with this service's
   * backends.
   *
   *  clientTlsPolicy only applies to a globalBackendService with the
   * loadBalancingScheme set to INTERNAL_SELF_MANAGED.
   *
   *  If left blank, communications are not encrypted.
   *
   * @param string $clientTlsPolicy
   */
  public function setClientTlsPolicy($clientTlsPolicy)
  {
    $this->clientTlsPolicy = $clientTlsPolicy;
  }
  /**
   * @return string
   */
  public function getClientTlsPolicy()
  {
    return $this->clientTlsPolicy;
  }
  /**
   * Optional. A list of Subject Alternative Names (SANs) that the client
   * verifies during a mutual TLS handshake with an server/endpoint for
   * thisBackendService. When the server presents its X.509 certificate to the
   * client, the client inspects the certificate'ssubjectAltName field. If the
   * field contains one of the specified values, the communication continues.
   * Otherwise, it fails. This additional check enables the client to verify
   * that the server is authorized to run the requested service.
   *
   *  Note that the contents of the server certificate's subjectAltName field
   * are configured by the Public Key Infrastructure which provisions server
   * identities.
   *
   *  Only applies to a global BackendService withloadBalancingScheme set to
   * INTERNAL_SELF_MANAGED. Only applies when BackendService has an
   * attachedclientTlsPolicy with clientCertificate (mTLS mode).
   *
   * @param string[] $subjectAltNames
   */
  public function setSubjectAltNames($subjectAltNames)
  {
    $this->subjectAltNames = $subjectAltNames;
  }
  /**
   * @return string[]
   */
  public function getSubjectAltNames()
  {
    return $this->subjectAltNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecuritySettings::class, 'Google_Service_Compute_SecuritySettings');
