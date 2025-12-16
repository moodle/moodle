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

class SslPolicy extends \Google\Collection
{
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
   * Compatible profile. Allows the broadset set of clients, even those which
   * support only out-of-date SSL features to negotiate with the load balancer.
   */
  public const PROFILE_COMPATIBLE = 'COMPATIBLE';
  /**
   * Custom profile. Allow only the set of allowed SSL features specified in the
   * customFeatures field.
   */
  public const PROFILE_CUSTOM = 'CUSTOM';
  /**
   * FIPS compatible profile. Supports a reduced set of SSL features, intended
   * to meet FIPS 140-3 compliance requirements.
   */
  public const PROFILE_FIPS_202205 = 'FIPS_202205';
  /**
   * Modern profile. Supports a wide set of SSL features, allowing modern
   * clients to negotiate SSL with the load balancer.
   */
  public const PROFILE_MODERN = 'MODERN';
  /**
   * Restricted profile. Supports a reduced set of SSL features, intended to
   * meet stricter compliance requirements.
   */
  public const PROFILE_RESTRICTED = 'RESTRICTED';
  protected $collection_key = 'warnings';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * A list of features enabled when the selected profile is CUSTOM. The  method
   * returns the set of features that can be specified in this list. This field
   * must be empty if the profile is notCUSTOM.
   *
   * @var string[]
   */
  public $customFeatures;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] The list of features enabled in the SSL policy.
   *
   * @var string[]
   */
  public $enabledFeatures;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a SslPolicy. An up-to-date fingerprint must be provided in order
   * to update the SslPolicy, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * SslPolicy.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output only] Type of the resource. Alwayscompute#sslPolicyfor
   * SSL policies.
   *
   * @var string
   */
  public $kind;
  /**
   * The minimum version of SSL protocol that can be used by the clients to
   * establish a connection with the load balancer. This can be one ofTLS_1_0,
   * TLS_1_1, TLS_1_2,TLS_1_3. When set to TLS_1_3, the profile field must be
   * set to RESTRICTED.
   *
   * @var string
   */
  public $minTlsVersion;
  /**
   * Name of the resource. The name must be 1-63 characters long, and comply
   * with RFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * Profile specifies the set of SSL features that can be used by the load
   * balancer when negotiating SSL with clients. This can be one ofCOMPATIBLE,
   * MODERN, RESTRICTED, orCUSTOM. If using CUSTOM, the set of SSL features to
   * enable must be specified in the customFeatures field.
   *
   * @var string
   */
  public $profile;
  /**
   * Output only. [Output Only] URL of the region where the regional SSL policy
   * resides. This field is not applicable to global SSL policies.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $warningsType = SslPolicyWarnings::class;
  protected $warningsDataType = 'array';

  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * A list of features enabled when the selected profile is CUSTOM. The  method
   * returns the set of features that can be specified in this list. This field
   * must be empty if the profile is notCUSTOM.
   *
   * @param string[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return string[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * Output only. [Output Only] The list of features enabled in the SSL policy.
   *
   * @param string[] $enabledFeatures
   */
  public function setEnabledFeatures($enabledFeatures)
  {
    $this->enabledFeatures = $enabledFeatures;
  }
  /**
   * @return string[]
   */
  public function getEnabledFeatures()
  {
    return $this->enabledFeatures;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a SslPolicy. An up-to-date fingerprint must be provided in order
   * to update the SslPolicy, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * SslPolicy.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output only] Type of the resource. Alwayscompute#sslPolicyfor
   * SSL policies.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The minimum version of SSL protocol that can be used by the clients to
   * establish a connection with the load balancer. This can be one ofTLS_1_0,
   * TLS_1_1, TLS_1_2,TLS_1_3. When set to TLS_1_3, the profile field must be
   * set to RESTRICTED.
   *
   * Accepted values: TLS_1_0, TLS_1_1, TLS_1_2, TLS_1_3
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
   * Name of the resource. The name must be 1-63 characters long, and comply
   * with RFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * Profile specifies the set of SSL features that can be used by the load
   * balancer when negotiating SSL with clients. This can be one ofCOMPATIBLE,
   * MODERN, RESTRICTED, orCUSTOM. If using CUSTOM, the set of SSL features to
   * enable must be specified in the customFeatures field.
   *
   * Accepted values: COMPATIBLE, CUSTOM, FIPS_202205, MODERN, RESTRICTED
   *
   * @param self::PROFILE_* $profile
   */
  public function setProfile($profile)
  {
    $this->profile = $profile;
  }
  /**
   * @return self::PROFILE_*
   */
  public function getProfile()
  {
    return $this->profile;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional SSL policy
   * resides. This field is not applicable to global SSL policies.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] If potential misconfigurations are detected for
   * this SSL policy, this field will be populated with warning messages.
   *
   * @param SslPolicyWarnings[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return SslPolicyWarnings[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslPolicy::class, 'Google_Service_Compute_SslPolicy');
