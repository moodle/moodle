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

class TlsInspectionPolicy extends \Google\Collection
{
  /**
   * Indicates no TLS version was specified.
   */
  public const MIN_TLS_VERSION_TLS_VERSION_UNSPECIFIED = 'TLS_VERSION_UNSPECIFIED';
  /**
   * TLS 1.0
   */
  public const MIN_TLS_VERSION_TLS_1_0 = 'TLS_1_0';
  /**
   * TLS 1.1
   */
  public const MIN_TLS_VERSION_TLS_1_1 = 'TLS_1_1';
  /**
   * TLS 1.2
   */
  public const MIN_TLS_VERSION_TLS_1_2 = 'TLS_1_2';
  /**
   * TLS 1.3
   */
  public const MIN_TLS_VERSION_TLS_1_3 = 'TLS_1_3';
  /**
   * Indicates no profile was specified.
   */
  public const TLS_FEATURE_PROFILE_PROFILE_UNSPECIFIED = 'PROFILE_UNSPECIFIED';
  /**
   * Compatible profile. Allows the broadest set of clients, even those which
   * support only out-of-date SSL features to negotiate with the TLS inspection
   * proxy.
   */
  public const TLS_FEATURE_PROFILE_PROFILE_COMPATIBLE = 'PROFILE_COMPATIBLE';
  /**
   * Modern profile. Supports a wide set of SSL features, allowing modern
   * clients to negotiate SSL with the TLS inspection proxy.
   */
  public const TLS_FEATURE_PROFILE_PROFILE_MODERN = 'PROFILE_MODERN';
  /**
   * Restricted profile. Supports a reduced set of SSL features, intended to
   * meet stricter compliance requirements.
   */
  public const TLS_FEATURE_PROFILE_PROFILE_RESTRICTED = 'PROFILE_RESTRICTED';
  /**
   * Custom profile. Allow only the set of allowed SSL features specified in the
   * custom_features field of SslPolicy.
   */
  public const TLS_FEATURE_PROFILE_PROFILE_CUSTOM = 'PROFILE_CUSTOM';
  protected $collection_key = 'customTlsFeatures';
  /**
   * Required. A CA pool resource used to issue interception certificates. The
   * CA pool string has a relative resource path following the form
   * "projects/{project}/locations/{location}/caPools/{ca_pool}".
   *
   * @var string
   */
  public $caPool;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. List of custom TLS cipher suites selected. This field is valid
   * only if the selected tls_feature_profile is CUSTOM. The
   * compute.SslPoliciesService.ListAvailableFeatures method returns the set of
   * features that can be specified in this list. Note that Secure Web Proxy
   * does not yet honor this field.
   *
   * @var string[]
   */
  public $customTlsFeatures;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. If FALSE (the default), use our default set of public CAs in
   * addition to any CAs specified in trust_config. These public CAs are
   * currently based on the Mozilla Root Program and are subject to change over
   * time. If TRUE, do not accept our default set of public CAs. Only CAs
   * specified in trust_config will be accepted. This defaults to FALSE (use
   * public CAs in addition to trust_config) for backwards compatibility, but
   * trusting public root CAs is *not recommended* unless the traffic in
   * question is outbound to public web servers. When possible, prefer setting
   * this to "false" and explicitly specifying trusted CAs and certificates in a
   * TrustConfig. Note that Secure Web Proxy does not yet honor this field.
   *
   * @var bool
   */
  public $excludePublicCaSet;
  /**
   * Optional. Minimum TLS version that the firewall should use when negotiating
   * connections with both clients and servers. If this is not set, then the
   * default value is to allow the broadest set of clients and servers (TLS 1.0
   * or higher). Setting this to more restrictive values may improve security,
   * but may also prevent the firewall from connecting to some clients or
   * servers. Note that Secure Web Proxy does not yet honor this field.
   *
   * @var string
   */
  public $minTlsVersion;
  /**
   * Required. Name of the resource. Name is of the form projects/{project}/loca
   * tions/{location}/tlsInspectionPolicies/{tls_inspection_policy}
   * tls_inspection_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The selected Profile. If this is not set, then the default value
   * is to allow the broadest set of clients and servers ("PROFILE_COMPATIBLE").
   * Setting this to more restrictive values may improve security, but may also
   * prevent the TLS inspection proxy from connecting to some clients or
   * servers. Note that Secure Web Proxy does not yet honor this field.
   *
   * @var string
   */
  public $tlsFeatureProfile;
  /**
   * Optional. A TrustConfig resource used when making a connection to the TLS
   * server. This is a relative resource path following the form
   * "projects/{project}/locations/{location}/trustConfigs/{trust_config}". This
   * is necessary to intercept TLS connections to servers with certificates
   * signed by a private CA or self-signed certificates. Note that Secure Web
   * Proxy does not yet honor this field.
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
   * Required. A CA pool resource used to issue interception certificates. The
   * CA pool string has a relative resource path following the form
   * "projects/{project}/locations/{location}/caPools/{ca_pool}".
   *
   * @param string $caPool
   */
  public function setCaPool($caPool)
  {
    $this->caPool = $caPool;
  }
  /**
   * @return string
   */
  public function getCaPool()
  {
    return $this->caPool;
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
   * Optional. List of custom TLS cipher suites selected. This field is valid
   * only if the selected tls_feature_profile is CUSTOM. The
   * compute.SslPoliciesService.ListAvailableFeatures method returns the set of
   * features that can be specified in this list. Note that Secure Web Proxy
   * does not yet honor this field.
   *
   * @param string[] $customTlsFeatures
   */
  public function setCustomTlsFeatures($customTlsFeatures)
  {
    $this->customTlsFeatures = $customTlsFeatures;
  }
  /**
   * @return string[]
   */
  public function getCustomTlsFeatures()
  {
    return $this->customTlsFeatures;
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
   * Optional. If FALSE (the default), use our default set of public CAs in
   * addition to any CAs specified in trust_config. These public CAs are
   * currently based on the Mozilla Root Program and are subject to change over
   * time. If TRUE, do not accept our default set of public CAs. Only CAs
   * specified in trust_config will be accepted. This defaults to FALSE (use
   * public CAs in addition to trust_config) for backwards compatibility, but
   * trusting public root CAs is *not recommended* unless the traffic in
   * question is outbound to public web servers. When possible, prefer setting
   * this to "false" and explicitly specifying trusted CAs and certificates in a
   * TrustConfig. Note that Secure Web Proxy does not yet honor this field.
   *
   * @param bool $excludePublicCaSet
   */
  public function setExcludePublicCaSet($excludePublicCaSet)
  {
    $this->excludePublicCaSet = $excludePublicCaSet;
  }
  /**
   * @return bool
   */
  public function getExcludePublicCaSet()
  {
    return $this->excludePublicCaSet;
  }
  /**
   * Optional. Minimum TLS version that the firewall should use when negotiating
   * connections with both clients and servers. If this is not set, then the
   * default value is to allow the broadest set of clients and servers (TLS 1.0
   * or higher). Setting this to more restrictive values may improve security,
   * but may also prevent the firewall from connecting to some clients or
   * servers. Note that Secure Web Proxy does not yet honor this field.
   *
   * Accepted values: TLS_VERSION_UNSPECIFIED, TLS_1_0, TLS_1_1, TLS_1_2,
   * TLS_1_3
   *
   * @param self::MIN_TLS_VERSION_* $minTlsVersion
   */
  public function setMinTlsVersion($minTlsVersion)
  {
    $this->minTlsVersion = $minTlsVersion;
  }
  /**
   * @return self::MIN_TLS_VERSION_*
   */
  public function getMinTlsVersion()
  {
    return $this->minTlsVersion;
  }
  /**
   * Required. Name of the resource. Name is of the form projects/{project}/loca
   * tions/{location}/tlsInspectionPolicies/{tls_inspection_policy}
   * tls_inspection_policy should match the
   * pattern:(^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$).
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
   * Optional. The selected Profile. If this is not set, then the default value
   * is to allow the broadest set of clients and servers ("PROFILE_COMPATIBLE").
   * Setting this to more restrictive values may improve security, but may also
   * prevent the TLS inspection proxy from connecting to some clients or
   * servers. Note that Secure Web Proxy does not yet honor this field.
   *
   * Accepted values: PROFILE_UNSPECIFIED, PROFILE_COMPATIBLE, PROFILE_MODERN,
   * PROFILE_RESTRICTED, PROFILE_CUSTOM
   *
   * @param self::TLS_FEATURE_PROFILE_* $tlsFeatureProfile
   */
  public function setTlsFeatureProfile($tlsFeatureProfile)
  {
    $this->tlsFeatureProfile = $tlsFeatureProfile;
  }
  /**
   * @return self::TLS_FEATURE_PROFILE_*
   */
  public function getTlsFeatureProfile()
  {
    return $this->tlsFeatureProfile;
  }
  /**
   * Optional. A TrustConfig resource used when making a connection to the TLS
   * server. This is a relative resource path following the form
   * "projects/{project}/locations/{location}/trustConfigs/{trust_config}". This
   * is necessary to intercept TLS connections to servers with certificates
   * signed by a private CA or self-signed certificates. Note that Secure Web
   * Proxy does not yet honor this field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TlsInspectionPolicy::class, 'Google_Service_NetworkSecurity_TlsInspectionPolicy');
