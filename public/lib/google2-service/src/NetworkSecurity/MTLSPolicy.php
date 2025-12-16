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

class MTLSPolicy extends \Google\Collection
{
  /**
   * Not allowed.
   */
  public const CLIENT_VALIDATION_MODE_CLIENT_VALIDATION_MODE_UNSPECIFIED = 'CLIENT_VALIDATION_MODE_UNSPECIFIED';
  /**
   * Allow connection even if certificate chain validation of the client
   * certificate failed or no client certificate was presented. The proof of
   * possession of the private key is always checked if client certificate was
   * presented. This mode requires the backend to implement processing of data
   * extracted from a client certificate to authenticate the peer, or to reject
   * connections if the client certificate fingerprint is missing.
   */
  public const CLIENT_VALIDATION_MODE_ALLOW_INVALID_OR_MISSING_CLIENT_CERT = 'ALLOW_INVALID_OR_MISSING_CLIENT_CERT';
  /**
   * Require a client certificate and allow connection to the backend only if
   * validation of the client certificate passed. If set, requires a reference
   * to non-empty TrustConfig specified in `client_validation_trust_config`.
   */
  public const CLIENT_VALIDATION_MODE_REJECT_INVALID = 'REJECT_INVALID';
  protected $collection_key = 'clientValidationCa';
  protected $clientValidationCaType = ValidationCA::class;
  protected $clientValidationCaDataType = 'array';
  /**
   * When the client presents an invalid certificate or no certificate to the
   * load balancer, the `client_validation_mode` specifies how the client
   * connection is handled. Required if the policy is to be used with the
   * Application Load Balancers. For Traffic Director it must be empty.
   *
   * @var string
   */
  public $clientValidationMode;
  /**
   * Reference to the TrustConfig from certificatemanager.googleapis.com
   * namespace. If specified, the chain validation will be performed against
   * certificates configured in the given TrustConfig. Allowed only if the
   * policy is to be used with Application Load Balancers.
   *
   * @var string
   */
  public $clientValidationTrustConfig;

  /**
   * Required if the policy is to be used with Traffic Director. For Application
   * Load Balancers it must be empty. Defines the mechanism to obtain the
   * Certificate Authority certificate to validate the client certificate.
   *
   * @param ValidationCA[] $clientValidationCa
   */
  public function setClientValidationCa($clientValidationCa)
  {
    $this->clientValidationCa = $clientValidationCa;
  }
  /**
   * @return ValidationCA[]
   */
  public function getClientValidationCa()
  {
    return $this->clientValidationCa;
  }
  /**
   * When the client presents an invalid certificate or no certificate to the
   * load balancer, the `client_validation_mode` specifies how the client
   * connection is handled. Required if the policy is to be used with the
   * Application Load Balancers. For Traffic Director it must be empty.
   *
   * Accepted values: CLIENT_VALIDATION_MODE_UNSPECIFIED,
   * ALLOW_INVALID_OR_MISSING_CLIENT_CERT, REJECT_INVALID
   *
   * @param self::CLIENT_VALIDATION_MODE_* $clientValidationMode
   */
  public function setClientValidationMode($clientValidationMode)
  {
    $this->clientValidationMode = $clientValidationMode;
  }
  /**
   * @return self::CLIENT_VALIDATION_MODE_*
   */
  public function getClientValidationMode()
  {
    return $this->clientValidationMode;
  }
  /**
   * Reference to the TrustConfig from certificatemanager.googleapis.com
   * namespace. If specified, the chain validation will be performed against
   * certificates configured in the given TrustConfig. Allowed only if the
   * policy is to be used with Application Load Balancers.
   *
   * @param string $clientValidationTrustConfig
   */
  public function setClientValidationTrustConfig($clientValidationTrustConfig)
  {
    $this->clientValidationTrustConfig = $clientValidationTrustConfig;
  }
  /**
   * @return string
   */
  public function getClientValidationTrustConfig()
  {
    return $this->clientValidationTrustConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MTLSPolicy::class, 'Google_Service_NetworkSecurity_MTLSPolicy');
