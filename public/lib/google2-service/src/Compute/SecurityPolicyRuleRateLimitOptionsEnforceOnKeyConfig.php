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

class SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig extends \Google\Model
{
  public const ENFORCE_ON_KEY_TYPE_ALL = 'ALL';
  public const ENFORCE_ON_KEY_TYPE_HTTP_COOKIE = 'HTTP_COOKIE';
  public const ENFORCE_ON_KEY_TYPE_HTTP_HEADER = 'HTTP_HEADER';
  public const ENFORCE_ON_KEY_TYPE_HTTP_PATH = 'HTTP_PATH';
  public const ENFORCE_ON_KEY_TYPE_IP = 'IP';
  public const ENFORCE_ON_KEY_TYPE_REGION_CODE = 'REGION_CODE';
  public const ENFORCE_ON_KEY_TYPE_SNI = 'SNI';
  public const ENFORCE_ON_KEY_TYPE_TLS_JA3_FINGERPRINT = 'TLS_JA3_FINGERPRINT';
  public const ENFORCE_ON_KEY_TYPE_TLS_JA4_FINGERPRINT = 'TLS_JA4_FINGERPRINT';
  public const ENFORCE_ON_KEY_TYPE_USER_IP = 'USER_IP';
  public const ENFORCE_ON_KEY_TYPE_XFF_IP = 'XFF_IP';
  /**
   * Rate limit key name applicable only for the following key types:
   * HTTP_HEADER -- Name of the HTTP header whose value is taken as the key
   * value. HTTP_COOKIE -- Name of the HTTP cookie whose value is taken as the
   * key value.
   *
   * @var string
   */
  public $enforceOnKeyName;
  /**
   * Determines the key to enforce the rate_limit_threshold on. Possible values
   * are:        - ALL: A single rate limit threshold is applied to all    the
   * requests matching this rule. This is the default value if
   * "enforceOnKeyConfigs" is not configured.    - IP: The source IP address of
   * the request is the key. Each IP has this limit enforced    separately.    -
   * HTTP_HEADER: The value of the HTTP    header whose name is configured under
   * "enforceOnKeyName". The key    value is truncated to the first 128 bytes of
   * the header value. If no    such header is present in the request, the key
   * type defaults toALL.    - XFF_IP: The first IP address (i.e. the
   * originating client IP address) specified in the list of IPs under
   * X-Forwarded-For HTTP header. If no such header is present or the    value
   * is not a valid IP, the key defaults to the source IP address of    the
   * request i.e. key type IP.    - HTTP_COOKIE: The value of the HTTP    cookie
   * whose name is configured under "enforceOnKeyName". The key    value is
   * truncated to the first 128 bytes of the cookie value. If no    such cookie
   * is present in the request, the key type defaults toALL.    - HTTP_PATH: The
   * URL path of the HTTP request. The key    value is truncated to the first
   * 128 bytes.     - SNI: Server name indication in the TLS session of    the
   * HTTPS request. The key value is truncated to the first 128 bytes.    The
   * key type defaults to ALL on a HTTP session.     - REGION_CODE: The
   * country/region from which the    request originates.     -
   * TLS_JA3_FINGERPRINT: JA3 TLS/SSL fingerprint if the    client connects
   * using HTTPS, HTTP/2 or HTTP/3. If not available, the    key type defaults
   * to ALL.     - USER_IP: The IP address of the originating client,    which
   * is resolved based on "userIpRequestHeaders" configured with the    security
   * policy. If there is no "userIpRequestHeaders" configuration    or an IP
   * address cannot be resolved from it, the key type defaults toIP.
   *
   * - TLS_JA4_FINGERPRINT: JA4 TLS/SSL fingerprint if the client connects using
   * HTTPS, HTTP/2 or HTTP/3. If not available, the key type defaults to ALL.
   *
   * @var string
   */
  public $enforceOnKeyType;

  /**
   * Rate limit key name applicable only for the following key types:
   * HTTP_HEADER -- Name of the HTTP header whose value is taken as the key
   * value. HTTP_COOKIE -- Name of the HTTP cookie whose value is taken as the
   * key value.
   *
   * @param string $enforceOnKeyName
   */
  public function setEnforceOnKeyName($enforceOnKeyName)
  {
    $this->enforceOnKeyName = $enforceOnKeyName;
  }
  /**
   * @return string
   */
  public function getEnforceOnKeyName()
  {
    return $this->enforceOnKeyName;
  }
  /**
   * Determines the key to enforce the rate_limit_threshold on. Possible values
   * are:        - ALL: A single rate limit threshold is applied to all    the
   * requests matching this rule. This is the default value if
   * "enforceOnKeyConfigs" is not configured.    - IP: The source IP address of
   * the request is the key. Each IP has this limit enforced    separately.    -
   * HTTP_HEADER: The value of the HTTP    header whose name is configured under
   * "enforceOnKeyName". The key    value is truncated to the first 128 bytes of
   * the header value. If no    such header is present in the request, the key
   * type defaults toALL.    - XFF_IP: The first IP address (i.e. the
   * originating client IP address) specified in the list of IPs under
   * X-Forwarded-For HTTP header. If no such header is present or the    value
   * is not a valid IP, the key defaults to the source IP address of    the
   * request i.e. key type IP.    - HTTP_COOKIE: The value of the HTTP    cookie
   * whose name is configured under "enforceOnKeyName". The key    value is
   * truncated to the first 128 bytes of the cookie value. If no    such cookie
   * is present in the request, the key type defaults toALL.    - HTTP_PATH: The
   * URL path of the HTTP request. The key    value is truncated to the first
   * 128 bytes.     - SNI: Server name indication in the TLS session of    the
   * HTTPS request. The key value is truncated to the first 128 bytes.    The
   * key type defaults to ALL on a HTTP session.     - REGION_CODE: The
   * country/region from which the    request originates.     -
   * TLS_JA3_FINGERPRINT: JA3 TLS/SSL fingerprint if the    client connects
   * using HTTPS, HTTP/2 or HTTP/3. If not available, the    key type defaults
   * to ALL.     - USER_IP: The IP address of the originating client,    which
   * is resolved based on "userIpRequestHeaders" configured with the    security
   * policy. If there is no "userIpRequestHeaders" configuration    or an IP
   * address cannot be resolved from it, the key type defaults toIP.
   *
   * - TLS_JA4_FINGERPRINT: JA4 TLS/SSL fingerprint if the client connects using
   * HTTPS, HTTP/2 or HTTP/3. If not available, the key type defaults to ALL.
   *
   * Accepted values: ALL, HTTP_COOKIE, HTTP_HEADER, HTTP_PATH, IP, REGION_CODE,
   * SNI, TLS_JA3_FINGERPRINT, TLS_JA4_FINGERPRINT, USER_IP, XFF_IP
   *
   * @param self::ENFORCE_ON_KEY_TYPE_* $enforceOnKeyType
   */
  public function setEnforceOnKeyType($enforceOnKeyType)
  {
    $this->enforceOnKeyType = $enforceOnKeyType;
  }
  /**
   * @return self::ENFORCE_ON_KEY_TYPE_*
   */
  public function getEnforceOnKeyType()
  {
    return $this->enforceOnKeyType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig::class, 'Google_Service_Compute_SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig');
