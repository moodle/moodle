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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig extends \Google\Collection
{
  /**
   * Unspecified gateway identity.
   */
  public const GATEWAY_IDENTITY_GATEWAY_IDENTITY_UNSPECIFIED = 'GATEWAY_IDENTITY_UNSPECIFIED';
  /**
   * Resource name for gateway identity, in the format: projects/{project_id}/lo
   * cations/{location_id}/securityGateways/{security_gateway_id}
   */
  public const GATEWAY_IDENTITY_RESOURCE_NAME = 'RESOURCE_NAME';
  protected $collection_key = 'allowedClientHeaders';
  /**
   * Optional. List of the allowed client header names.
   *
   * @var string[]
   */
  public $allowedClientHeaders;
  /**
   * Optional. Client IP configuration. The client IP address is included if
   * true.
   *
   * @var bool
   */
  public $clientIp;
  protected $contextualHeadersType = GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders::class;
  protected $contextualHeadersDataType = '';
  /**
   * Optional. The security gateway identity configuration.
   *
   * @var string
   */
  public $gatewayIdentity;
  /**
   * Optional. Custom resource specific headers along with the values. The names
   * should conform to RFC 9110: >Field names can contain alphanumeric
   * characters, hyphens, and periods, can contain only ASCII-printable
   * characters and tabs, and must start with a letter.
   *
   * @var string[]
   */
  public $metadataHeaders;

  /**
   * Optional. List of the allowed client header names.
   *
   * @param string[] $allowedClientHeaders
   */
  public function setAllowedClientHeaders($allowedClientHeaders)
  {
    $this->allowedClientHeaders = $allowedClientHeaders;
  }
  /**
   * @return string[]
   */
  public function getAllowedClientHeaders()
  {
    return $this->allowedClientHeaders;
  }
  /**
   * Optional. Client IP configuration. The client IP address is included if
   * true.
   *
   * @param bool $clientIp
   */
  public function setClientIp($clientIp)
  {
    $this->clientIp = $clientIp;
  }
  /**
   * @return bool
   */
  public function getClientIp()
  {
    return $this->clientIp;
  }
  /**
   * Optional. Configuration for the contextual headers.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders $contextualHeaders
   */
  public function setContextualHeaders(GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders $contextualHeaders)
  {
    $this->contextualHeaders = $contextualHeaders;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ContextualHeaders
   */
  public function getContextualHeaders()
  {
    return $this->contextualHeaders;
  }
  /**
   * Optional. The security gateway identity configuration.
   *
   * Accepted values: GATEWAY_IDENTITY_UNSPECIFIED, RESOURCE_NAME
   *
   * @param self::GATEWAY_IDENTITY_* $gatewayIdentity
   */
  public function setGatewayIdentity($gatewayIdentity)
  {
    $this->gatewayIdentity = $gatewayIdentity;
  }
  /**
   * @return self::GATEWAY_IDENTITY_*
   */
  public function getGatewayIdentity()
  {
    return $this->gatewayIdentity;
  }
  /**
   * Optional. Custom resource specific headers along with the values. The names
   * should conform to RFC 9110: >Field names can contain alphanumeric
   * characters, hyphens, and periods, can contain only ASCII-printable
   * characters and tabs, and must start with a letter.
   *
   * @param string[] $metadataHeaders
   */
  public function setMetadataHeaders($metadataHeaders)
  {
    $this->metadataHeaders = $metadataHeaders;
  }
  /**
   * @return string[]
   */
  public function getMetadataHeaders()
  {
    return $this->metadataHeaders;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig');
