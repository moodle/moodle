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

class BackendAuthenticationConfig extends \Google\Model
{
  /**
   * Equivalent to NONE.
   */
  public const WELL_KNOWN_ROOTS_WELL_KNOWN_ROOTS_UNSPECIFIED = 'WELL_KNOWN_ROOTS_UNSPECIFIED';
  /**
   * The BackendService will only validate server certificates against roots
   * specified in TrustConfig.
   */
  public const WELL_KNOWN_ROOTS_NONE = 'NONE';
  /**
   * The BackendService uses a set of well-known public roots, in addition to
   * any roots specified in the trustConfig field, when validating the server
   * certificates presented by the backend. Validation with these roots is only
   * considered when the TlsSettings.sni field in the BackendService is set. The
   * well-known roots are a set of root CAs managed by Google. CAs in this set
   * can be added or removed without notice.
   */
  public const WELL_KNOWN_ROOTS_PUBLIC_ROOTS = 'PUBLIC_ROOTS';
  /**
   * Optional. A reference to a certificatemanager.googleapis.com.Certificate
   * resource. This is a relative resource path following the form
   * "projects/{project}/locations/{location}/certificates/{certificate}". Used
   * by a BackendService to negotiate mTLS when the backend connection uses TLS
   * and the backend requests a client certificate. Must have a CLIENT_AUTH
   * scope.
   *
   * @var string
   */
  public $clientCertificate;
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
   * Output only. Etag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Set of label tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Name of the BackendAuthenticationConfig resource. It matches the
   * pattern `projects/locations/{location}/backendAuthenticationConfigs/{backen
   * d_authentication_config}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. A reference to a TrustConfig resource from the
   * certificatemanager.googleapis.com namespace. This is a relative resource
   * path following the form
   * "projects/{project}/locations/{location}/trustConfigs/{trust_config}". A
   * BackendService uses the chain of trust represented by this TrustConfig, if
   * specified, to validate the server certificates presented by the backend.
   * Required unless wellKnownRoots is set to PUBLIC_ROOTS.
   *
   * @var string
   */
  public $trustConfig;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Well known roots to use for server certificate validation.
   *
   * @var string
   */
  public $wellKnownRoots;

  /**
   * Optional. A reference to a certificatemanager.googleapis.com.Certificate
   * resource. This is a relative resource path following the form
   * "projects/{project}/locations/{location}/certificates/{certificate}". Used
   * by a BackendService to negotiate mTLS when the backend connection uses TLS
   * and the backend requests a client certificate. Must have a CLIENT_AUTH
   * scope.
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
   * Output only. Etag of the resource.
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
   * Set of label tags associated with the resource.
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
   * Required. Name of the BackendAuthenticationConfig resource. It matches the
   * pattern `projects/locations/{location}/backendAuthenticationConfigs/{backen
   * d_authentication_config}`
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
   * Optional. A reference to a TrustConfig resource from the
   * certificatemanager.googleapis.com namespace. This is a relative resource
   * path following the form
   * "projects/{project}/locations/{location}/trustConfigs/{trust_config}". A
   * BackendService uses the chain of trust represented by this TrustConfig, if
   * specified, to validate the server certificates presented by the backend.
   * Required unless wellKnownRoots is set to PUBLIC_ROOTS.
   *
   * @param string $trustConfig
   */
  public function setTrustConfig($trustConfig)
  {
    $this->trustConfig = $trustConfig;
  }
  /**
   * @return string
   */
  public function getTrustConfig()
  {
    return $this->trustConfig;
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
  /**
   * Well known roots to use for server certificate validation.
   *
   * Accepted values: WELL_KNOWN_ROOTS_UNSPECIFIED, NONE, PUBLIC_ROOTS
   *
   * @param self::WELL_KNOWN_ROOTS_* $wellKnownRoots
   */
  public function setWellKnownRoots($wellKnownRoots)
  {
    $this->wellKnownRoots = $wellKnownRoots;
  }
  /**
   * @return self::WELL_KNOWN_ROOTS_*
   */
  public function getWellKnownRoots()
  {
    return $this->wellKnownRoots;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendAuthenticationConfig::class, 'Google_Service_NetworkSecurity_BackendAuthenticationConfig');
