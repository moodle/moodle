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

namespace Google\Service\CertificateAuthorityService;

class CertificateAuthority extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Certificates can be issued from this CA. CRLs will be generated for this
   * CA. The CA will be part of the CaPool's trust anchor, and will be used to
   * issue certificates from the CaPool.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * Certificates cannot be issued from this CA. CRLs will still be generated.
   * The CA will be part of the CaPool's trust anchor, but will not be used to
   * issue certificates from the CaPool.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Certificates can be issued from this CA. CRLs will be generated for this
   * CA. The CA will be part of the CaPool's trust anchor, but will not be used
   * to issue certificates from the CaPool.
   */
  public const STATE_STAGED = 'STAGED';
  /**
   * Certificates cannot be issued from this CA. CRLs will not be generated. The
   * CA will not be part of the CaPool's trust anchor, and will not be used to
   * issue certificates from the CaPool.
   */
  public const STATE_AWAITING_USER_ACTIVATION = 'AWAITING_USER_ACTIVATION';
  /**
   * Certificates cannot be issued from this CA. CRLs will not be generated. The
   * CA may still be recovered by calling
   * CertificateAuthorityService.UndeleteCertificateAuthority before
   * expire_time. The CA will not be part of the CaPool's trust anchor, and will
   * not be used to issue certificates from the CaPool.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * Not specified.
   */
  public const TIER_TIER_UNSPECIFIED = 'TIER_UNSPECIFIED';
  /**
   * Enterprise tier.
   */
  public const TIER_ENTERPRISE = 'ENTERPRISE';
  /**
   * DevOps tier.
   */
  public const TIER_DEVOPS = 'DEVOPS';
  /**
   * Not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Self-signed CA.
   */
  public const TYPE_SELF_SIGNED = 'SELF_SIGNED';
  /**
   * Subordinate CA. Could be issued by a Private CA CertificateAuthority or an
   * unmanaged CA.
   */
  public const TYPE_SUBORDINATE = 'SUBORDINATE';
  protected $collection_key = 'pemCaCertificates';
  protected $accessUrlsType = AccessUrls::class;
  protected $accessUrlsDataType = '';
  protected $caCertificateDescriptionsType = CertificateDescription::class;
  protected $caCertificateDescriptionsDataType = 'array';
  protected $configType = CertificateConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time at which this CertificateAuthority was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which this CertificateAuthority was soft deleted,
   * if it is in the DELETED state.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Output only. The time at which this CertificateAuthority will be
   * permanently purged, if it is in the DELETED state.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Immutable. The name of a Cloud Storage bucket where this
   * CertificateAuthority will publish content, such as the CA certificate and
   * CRLs. This must be a bucket name, without any prefixes (such as `gs://`) or
   * suffixes (such as `.googleapis.com`). For example, to use a bucket named
   * `my-bucket`, you would simply specify `my-bucket`. If not specified, a
   * managed bucket will be created.
   *
   * @var string
   */
  public $gcsBucket;
  protected $keySpecType = KeyVersionSpec::class;
  protected $keySpecDataType = '';
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Immutable. The desired lifetime of the CA certificate. Used to
   * create the "not_before_time" and "not_after_time" fields inside an X.509
   * certificate.
   *
   * @var string
   */
  public $lifetime;
  /**
   * Identifier. The resource name for this CertificateAuthority in the format
   * `projects/locations/caPools/certificateAuthorities`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. This CertificateAuthority's certificate chain, including the
   * current CertificateAuthority's certificate. Ordered such that the root
   * issuer is the final element (consistent with RFC 5246). For a self-signed
   * CA, this will only list the current CertificateAuthority's certificate.
   *
   * @var string[]
   */
  public $pemCaCertificates;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The State for this CertificateAuthority.
   *
   * @var string
   */
  public $state;
  protected $subordinateConfigType = SubordinateConfig::class;
  protected $subordinateConfigDataType = '';
  /**
   * Output only. The CaPool.Tier of the CaPool that includes this
   * CertificateAuthority.
   *
   * @var string
   */
  public $tier;
  /**
   * Required. Immutable. The Type of this CertificateAuthority.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time at which this CertificateAuthority was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $userDefinedAccessUrlsType = UserDefinedAccessUrls::class;
  protected $userDefinedAccessUrlsDataType = '';

  /**
   * Output only. URLs for accessing content published by this CA, such as the
   * CA certificate and CRLs.
   *
   * @param AccessUrls $accessUrls
   */
  public function setAccessUrls(AccessUrls $accessUrls)
  {
    $this->accessUrls = $accessUrls;
  }
  /**
   * @return AccessUrls
   */
  public function getAccessUrls()
  {
    return $this->accessUrls;
  }
  /**
   * Output only. A structured description of this CertificateAuthority's CA
   * certificate and its issuers. Ordered as self-to-root.
   *
   * @param CertificateDescription[] $caCertificateDescriptions
   */
  public function setCaCertificateDescriptions($caCertificateDescriptions)
  {
    $this->caCertificateDescriptions = $caCertificateDescriptions;
  }
  /**
   * @return CertificateDescription[]
   */
  public function getCaCertificateDescriptions()
  {
    return $this->caCertificateDescriptions;
  }
  /**
   * Required. Immutable. The config used to create a self-signed X.509
   * certificate or CSR.
   *
   * @param CertificateConfig $config
   */
  public function setConfig(CertificateConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return CertificateConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which this CertificateAuthority was created.
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
   * Output only. The time at which this CertificateAuthority was soft deleted,
   * if it is in the DELETED state.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Output only. The time at which this CertificateAuthority will be
   * permanently purged, if it is in the DELETED state.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Immutable. The name of a Cloud Storage bucket where this
   * CertificateAuthority will publish content, such as the CA certificate and
   * CRLs. This must be a bucket name, without any prefixes (such as `gs://`) or
   * suffixes (such as `.googleapis.com`). For example, to use a bucket named
   * `my-bucket`, you would simply specify `my-bucket`. If not specified, a
   * managed bucket will be created.
   *
   * @param string $gcsBucket
   */
  public function setGcsBucket($gcsBucket)
  {
    $this->gcsBucket = $gcsBucket;
  }
  /**
   * @return string
   */
  public function getGcsBucket()
  {
    return $this->gcsBucket;
  }
  /**
   * Required. Immutable. Used when issuing certificates for this
   * CertificateAuthority. If this CertificateAuthority is a self-signed
   * CertificateAuthority, this key is also used to sign the self-signed CA
   * certificate. Otherwise, it is used to sign a CSR.
   *
   * @param KeyVersionSpec $keySpec
   */
  public function setKeySpec(KeyVersionSpec $keySpec)
  {
    $this->keySpec = $keySpec;
  }
  /**
   * @return KeyVersionSpec
   */
  public function getKeySpec()
  {
    return $this->keySpec;
  }
  /**
   * Optional. Labels with user-defined metadata.
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
   * Required. Immutable. The desired lifetime of the CA certificate. Used to
   * create the "not_before_time" and "not_after_time" fields inside an X.509
   * certificate.
   *
   * @param string $lifetime
   */
  public function setLifetime($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  /**
   * @return string
   */
  public function getLifetime()
  {
    return $this->lifetime;
  }
  /**
   * Identifier. The resource name for this CertificateAuthority in the format
   * `projects/locations/caPools/certificateAuthorities`.
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
   * Output only. This CertificateAuthority's certificate chain, including the
   * current CertificateAuthority's certificate. Ordered such that the root
   * issuer is the final element (consistent with RFC 5246). For a self-signed
   * CA, this will only list the current CertificateAuthority's certificate.
   *
   * @param string[] $pemCaCertificates
   */
  public function setPemCaCertificates($pemCaCertificates)
  {
    $this->pemCaCertificates = $pemCaCertificates;
  }
  /**
   * @return string[]
   */
  public function getPemCaCertificates()
  {
    return $this->pemCaCertificates;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The State for this CertificateAuthority.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, STAGED,
   * AWAITING_USER_ACTIVATION, DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. If this is a subordinate CertificateAuthority, this field will be
   * set with the subordinate configuration, which describes its issuers. This
   * may be updated, but this CertificateAuthority must continue to validate.
   *
   * @param SubordinateConfig $subordinateConfig
   */
  public function setSubordinateConfig(SubordinateConfig $subordinateConfig)
  {
    $this->subordinateConfig = $subordinateConfig;
  }
  /**
   * @return SubordinateConfig
   */
  public function getSubordinateConfig()
  {
    return $this->subordinateConfig;
  }
  /**
   * Output only. The CaPool.Tier of the CaPool that includes this
   * CertificateAuthority.
   *
   * Accepted values: TIER_UNSPECIFIED, ENTERPRISE, DEVOPS
   *
   * @param self::TIER_* $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return self::TIER_*
   */
  public function getTier()
  {
    return $this->tier;
  }
  /**
   * Required. Immutable. The Type of this CertificateAuthority.
   *
   * Accepted values: TYPE_UNSPECIFIED, SELF_SIGNED, SUBORDINATE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The time at which this CertificateAuthority was last updated.
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
   * Optional. User-defined URLs for CA certificate and CRLs. The service does
   * not publish content to these URLs. It is up to the user to mirror content
   * to these URLs.
   *
   * @param UserDefinedAccessUrls $userDefinedAccessUrls
   */
  public function setUserDefinedAccessUrls(UserDefinedAccessUrls $userDefinedAccessUrls)
  {
    $this->userDefinedAccessUrls = $userDefinedAccessUrls;
  }
  /**
   * @return UserDefinedAccessUrls
   */
  public function getUserDefinedAccessUrls()
  {
    return $this->userDefinedAccessUrls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateAuthority::class, 'Google_Service_CertificateAuthorityService_CertificateAuthority');
