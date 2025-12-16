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

class SecurityPolicyRuleRateLimitOptions extends \Google\Collection
{
  public const ENFORCE_ON_KEY_ALL = 'ALL';
  public const ENFORCE_ON_KEY_HTTP_COOKIE = 'HTTP_COOKIE';
  public const ENFORCE_ON_KEY_HTTP_HEADER = 'HTTP_HEADER';
  public const ENFORCE_ON_KEY_HTTP_PATH = 'HTTP_PATH';
  public const ENFORCE_ON_KEY_IP = 'IP';
  public const ENFORCE_ON_KEY_REGION_CODE = 'REGION_CODE';
  public const ENFORCE_ON_KEY_SNI = 'SNI';
  public const ENFORCE_ON_KEY_TLS_JA3_FINGERPRINT = 'TLS_JA3_FINGERPRINT';
  public const ENFORCE_ON_KEY_TLS_JA4_FINGERPRINT = 'TLS_JA4_FINGERPRINT';
  public const ENFORCE_ON_KEY_USER_IP = 'USER_IP';
  public const ENFORCE_ON_KEY_XFF_IP = 'XFF_IP';
  protected $collection_key = 'enforceOnKeyConfigs';
  /**
   * Can only be specified if the action for the rule is "rate_based_ban". If
   * specified, determines the time (in seconds) the traffic will continue to be
   * banned by the rate limit after the rate falls below the threshold.
   *
   * @var int
   */
  public $banDurationSec;
  protected $banThresholdType = SecurityPolicyRuleRateLimitOptionsThreshold::class;
  protected $banThresholdDataType = '';
  /**
   * Action to take for requests that are under the configured rate limit
   * threshold. Valid option is "allow" only.
   *
   * @var string
   */
  public $conformAction;
  /**
   * Determines the key to enforce the rate_limit_threshold on. Possible values
   * are:        - ALL: A single rate limit threshold is applied to all    the
   * requests matching this rule. This is the default value if    "enforceOnKey"
   * is not configured.    - IP: The source IP address of    the request is the
   * key. Each IP has this limit enforced    separately.    - HTTP_HEADER: The
   * value of the HTTP    header whose name is configured under
   * "enforceOnKeyName". The key    value is truncated to the first 128 bytes of
   * the header value. If no    such header is present in the request, the key
   * type defaults toALL.    - XFF_IP: The first IP address (i.e. the
   * originating client IP address) specified in the list of IPs under
   * X-Forwarded-For HTTP header. If no such header is present or the value
   * is not a valid IP, the key defaults to the source IP address of    the
   * request i.e. key type IP.    - HTTP_COOKIE: The value of the HTTP    cookie
   * whose name is configured under "enforceOnKeyName". The key    value is
   * truncated to the first 128 bytes of the cookie value. If no    such cookie
   * is present in the request, the key type defaults toALL.    - HTTP_PATH: The
   * URL path of the HTTP request. The key    value is truncated to the first
   * 128 bytes.     - SNI: Server name indication in the TLS session of the
   * HTTPS request. The key value is truncated to the first 128 bytes. The
   * key type defaults to ALL on a HTTP session.     - REGION_CODE: The
   * country/region from which the request    originates.     -
   * TLS_JA3_FINGERPRINT: JA3 TLS/SSL fingerprint if the    client connects
   * using HTTPS, HTTP/2 or HTTP/3. If not available, the    key type defaults
   * to ALL.     - USER_IP: The IP address of the originating client,    which
   * is resolved based on "userIpRequestHeaders" configured with the    security
   * policy. If there is no "userIpRequestHeaders" configuration or    an IP
   * address cannot be resolved from it, the key type defaults toIP.
   *
   * - TLS_JA4_FINGERPRINT: JA4 TLS/SSL fingerprint if the client connects using
   * HTTPS, HTTP/2 or HTTP/3. If not available, the key type defaults to ALL.
   * For "fairshare" action, this value is limited to ALL i.e. a single rate
   * limit threshold is enforced for all the requests matching the rule.
   *
   * @var string
   */
  public $enforceOnKey;
  protected $enforceOnKeyConfigsType = SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig::class;
  protected $enforceOnKeyConfigsDataType = 'array';
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
   * Action to take for requests that are above the configured rate limit
   * threshold, to either deny with a specified HTTP response code, or redirect
   * to a different endpoint. Valid options are `deny(STATUS)`, where valid
   * values for `STATUS` are 403, 404, 429, and 502, and `redirect`, where the
   * redirect parameters come from `exceedRedirectOptions` below. The `redirect`
   * action is only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @var string
   */
  public $exceedAction;
  protected $exceedRedirectOptionsType = SecurityPolicyRuleRedirectOptions::class;
  protected $exceedRedirectOptionsDataType = '';
  protected $rateLimitThresholdType = SecurityPolicyRuleRateLimitOptionsThreshold::class;
  protected $rateLimitThresholdDataType = '';

  /**
   * Can only be specified if the action for the rule is "rate_based_ban". If
   * specified, determines the time (in seconds) the traffic will continue to be
   * banned by the rate limit after the rate falls below the threshold.
   *
   * @param int $banDurationSec
   */
  public function setBanDurationSec($banDurationSec)
  {
    $this->banDurationSec = $banDurationSec;
  }
  /**
   * @return int
   */
  public function getBanDurationSec()
  {
    return $this->banDurationSec;
  }
  /**
   * Can only be specified if the action for the rule is "rate_based_ban". If
   * specified, the key will be banned for the configured 'ban_duration_sec'
   * when the number of requests that exceed the 'rate_limit_threshold' also
   * exceed this 'ban_threshold'.
   *
   * @param SecurityPolicyRuleRateLimitOptionsThreshold $banThreshold
   */
  public function setBanThreshold(SecurityPolicyRuleRateLimitOptionsThreshold $banThreshold)
  {
    $this->banThreshold = $banThreshold;
  }
  /**
   * @return SecurityPolicyRuleRateLimitOptionsThreshold
   */
  public function getBanThreshold()
  {
    return $this->banThreshold;
  }
  /**
   * Action to take for requests that are under the configured rate limit
   * threshold. Valid option is "allow" only.
   *
   * @param string $conformAction
   */
  public function setConformAction($conformAction)
  {
    $this->conformAction = $conformAction;
  }
  /**
   * @return string
   */
  public function getConformAction()
  {
    return $this->conformAction;
  }
  /**
   * Determines the key to enforce the rate_limit_threshold on. Possible values
   * are:        - ALL: A single rate limit threshold is applied to all    the
   * requests matching this rule. This is the default value if    "enforceOnKey"
   * is not configured.    - IP: The source IP address of    the request is the
   * key. Each IP has this limit enforced    separately.    - HTTP_HEADER: The
   * value of the HTTP    header whose name is configured under
   * "enforceOnKeyName". The key    value is truncated to the first 128 bytes of
   * the header value. If no    such header is present in the request, the key
   * type defaults toALL.    - XFF_IP: The first IP address (i.e. the
   * originating client IP address) specified in the list of IPs under
   * X-Forwarded-For HTTP header. If no such header is present or the value
   * is not a valid IP, the key defaults to the source IP address of    the
   * request i.e. key type IP.    - HTTP_COOKIE: The value of the HTTP    cookie
   * whose name is configured under "enforceOnKeyName". The key    value is
   * truncated to the first 128 bytes of the cookie value. If no    such cookie
   * is present in the request, the key type defaults toALL.    - HTTP_PATH: The
   * URL path of the HTTP request. The key    value is truncated to the first
   * 128 bytes.     - SNI: Server name indication in the TLS session of the
   * HTTPS request. The key value is truncated to the first 128 bytes. The
   * key type defaults to ALL on a HTTP session.     - REGION_CODE: The
   * country/region from which the request    originates.     -
   * TLS_JA3_FINGERPRINT: JA3 TLS/SSL fingerprint if the    client connects
   * using HTTPS, HTTP/2 or HTTP/3. If not available, the    key type defaults
   * to ALL.     - USER_IP: The IP address of the originating client,    which
   * is resolved based on "userIpRequestHeaders" configured with the    security
   * policy. If there is no "userIpRequestHeaders" configuration or    an IP
   * address cannot be resolved from it, the key type defaults toIP.
   *
   * - TLS_JA4_FINGERPRINT: JA4 TLS/SSL fingerprint if the client connects using
   * HTTPS, HTTP/2 or HTTP/3. If not available, the key type defaults to ALL.
   * For "fairshare" action, this value is limited to ALL i.e. a single rate
   * limit threshold is enforced for all the requests matching the rule.
   *
   * Accepted values: ALL, HTTP_COOKIE, HTTP_HEADER, HTTP_PATH, IP, REGION_CODE,
   * SNI, TLS_JA3_FINGERPRINT, TLS_JA4_FINGERPRINT, USER_IP, XFF_IP
   *
   * @param self::ENFORCE_ON_KEY_* $enforceOnKey
   */
  public function setEnforceOnKey($enforceOnKey)
  {
    $this->enforceOnKey = $enforceOnKey;
  }
  /**
   * @return self::ENFORCE_ON_KEY_*
   */
  public function getEnforceOnKey()
  {
    return $this->enforceOnKey;
  }
  /**
   * If specified, any combination of values of
   * enforce_on_key_type/enforce_on_key_name is treated as the key on which
   * ratelimit threshold/action is enforced. You can specify up to 3
   * enforce_on_key_configs. If enforce_on_key_configs is specified,
   * enforce_on_key must not be specified.
   *
   * @param SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig[] $enforceOnKeyConfigs
   */
  public function setEnforceOnKeyConfigs($enforceOnKeyConfigs)
  {
    $this->enforceOnKeyConfigs = $enforceOnKeyConfigs;
  }
  /**
   * @return SecurityPolicyRuleRateLimitOptionsEnforceOnKeyConfig[]
   */
  public function getEnforceOnKeyConfigs()
  {
    return $this->enforceOnKeyConfigs;
  }
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
   * Action to take for requests that are above the configured rate limit
   * threshold, to either deny with a specified HTTP response code, or redirect
   * to a different endpoint. Valid options are `deny(STATUS)`, where valid
   * values for `STATUS` are 403, 404, 429, and 502, and `redirect`, where the
   * redirect parameters come from `exceedRedirectOptions` below. The `redirect`
   * action is only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @param string $exceedAction
   */
  public function setExceedAction($exceedAction)
  {
    $this->exceedAction = $exceedAction;
  }
  /**
   * @return string
   */
  public function getExceedAction()
  {
    return $this->exceedAction;
  }
  /**
   * Parameters defining the redirect action that is used as the exceed action.
   * Cannot be specified if the exceed action is not redirect. This field is
   * only supported in Global Security Policies of type CLOUD_ARMOR.
   *
   * @param SecurityPolicyRuleRedirectOptions $exceedRedirectOptions
   */
  public function setExceedRedirectOptions(SecurityPolicyRuleRedirectOptions $exceedRedirectOptions)
  {
    $this->exceedRedirectOptions = $exceedRedirectOptions;
  }
  /**
   * @return SecurityPolicyRuleRedirectOptions
   */
  public function getExceedRedirectOptions()
  {
    return $this->exceedRedirectOptions;
  }
  /**
   * Threshold at which to begin ratelimiting.
   *
   * @param SecurityPolicyRuleRateLimitOptionsThreshold $rateLimitThreshold
   */
  public function setRateLimitThreshold(SecurityPolicyRuleRateLimitOptionsThreshold $rateLimitThreshold)
  {
    $this->rateLimitThreshold = $rateLimitThreshold;
  }
  /**
   * @return SecurityPolicyRuleRateLimitOptionsThreshold
   */
  public function getRateLimitThreshold()
  {
    return $this->rateLimitThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicyRuleRateLimitOptions::class, 'Google_Service_Compute_SecurityPolicyRuleRateLimitOptions');
