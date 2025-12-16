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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaTargetSite extends \Google\Model
{
  /**
   * Defaults to SUCCEEDED.
   */
  public const INDEXING_STATUS_INDEXING_STATUS_UNSPECIFIED = 'INDEXING_STATUS_UNSPECIFIED';
  /**
   * The target site is in the update queue and will be picked up by indexing
   * pipeline.
   */
  public const INDEXING_STATUS_PENDING = 'PENDING';
  /**
   * The target site fails to be indexed.
   */
  public const INDEXING_STATUS_FAILED = 'FAILED';
  /**
   * The target site has been indexed.
   */
  public const INDEXING_STATUS_SUCCEEDED = 'SUCCEEDED';
  /**
   * The previously indexed target site has been marked to be deleted. This is a
   * transitioning state which will resulted in either: 1. target site deleted
   * if unindexing is successful; 2. state reverts to SUCCEEDED if the
   * unindexing fails.
   */
  public const INDEXING_STATUS_DELETING = 'DELETING';
  /**
   * The target site change is pending but cancellable.
   */
  public const INDEXING_STATUS_CANCELLABLE = 'CANCELLABLE';
  /**
   * The target site change is cancelled.
   */
  public const INDEXING_STATUS_CANCELLED = 'CANCELLED';
  /**
   * This value is unused. In this case, server behavior defaults to
   * Type.INCLUDE.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Include the target site.
   */
  public const TYPE_INCLUDE = 'INCLUDE';
  /**
   * Exclude the target site.
   */
  public const TYPE_EXCLUDE = 'EXCLUDE';
  /**
   * Immutable. If set to false, a uri_pattern is generated to include all pages
   * whose address contains the provided_uri_pattern. If set to true, an
   * uri_pattern is generated to try to be an exact match of the
   * provided_uri_pattern or just the specific page if the provided_uri_pattern
   * is a specific one. provided_uri_pattern is always normalized to generate
   * the URI pattern to be used by the search engine.
   *
   * @var bool
   */
  public $exactMatch;
  protected $failureReasonType = GoogleCloudDiscoveryengineV1betaTargetSiteFailureReason::class;
  protected $failureReasonDataType = '';
  /**
   * Output only. This is system-generated based on the provided_uri_pattern.
   *
   * @var string
   */
  public $generatedUriPattern;
  /**
   * Output only. Indexing status.
   *
   * @var string
   */
  public $indexingStatus;
  /**
   * Output only. The fully qualified resource name of the target site. `project
   * s/{project}/locations/{location}/collections/{collection}/dataStores/{data_
   * store}/siteSearchEngine/targetSites/{target_site}` The `target_site_id` is
   * system-generated.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Input only. The user provided URI pattern from which the
   * `generated_uri_pattern` is generated.
   *
   * @var string
   */
  public $providedUriPattern;
  /**
   * Output only. Root domain of the provided_uri_pattern.
   *
   * @var string
   */
  public $rootDomainUri;
  protected $siteVerificationInfoType = GoogleCloudDiscoveryengineV1betaSiteVerificationInfo::class;
  protected $siteVerificationInfoDataType = '';
  /**
   * The type of the target site, e.g., whether the site is to be included or
   * excluded.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The target site's last updated time.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Immutable. If set to false, a uri_pattern is generated to include all pages
   * whose address contains the provided_uri_pattern. If set to true, an
   * uri_pattern is generated to try to be an exact match of the
   * provided_uri_pattern or just the specific page if the provided_uri_pattern
   * is a specific one. provided_uri_pattern is always normalized to generate
   * the URI pattern to be used by the search engine.
   *
   * @param bool $exactMatch
   */
  public function setExactMatch($exactMatch)
  {
    $this->exactMatch = $exactMatch;
  }
  /**
   * @return bool
   */
  public function getExactMatch()
  {
    return $this->exactMatch;
  }
  /**
   * Output only. Failure reason.
   *
   * @param GoogleCloudDiscoveryengineV1betaTargetSiteFailureReason $failureReason
   */
  public function setFailureReason(GoogleCloudDiscoveryengineV1betaTargetSiteFailureReason $failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaTargetSiteFailureReason
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Output only. This is system-generated based on the provided_uri_pattern.
   *
   * @param string $generatedUriPattern
   */
  public function setGeneratedUriPattern($generatedUriPattern)
  {
    $this->generatedUriPattern = $generatedUriPattern;
  }
  /**
   * @return string
   */
  public function getGeneratedUriPattern()
  {
    return $this->generatedUriPattern;
  }
  /**
   * Output only. Indexing status.
   *
   * Accepted values: INDEXING_STATUS_UNSPECIFIED, PENDING, FAILED, SUCCEEDED,
   * DELETING, CANCELLABLE, CANCELLED
   *
   * @param self::INDEXING_STATUS_* $indexingStatus
   */
  public function setIndexingStatus($indexingStatus)
  {
    $this->indexingStatus = $indexingStatus;
  }
  /**
   * @return self::INDEXING_STATUS_*
   */
  public function getIndexingStatus()
  {
    return $this->indexingStatus;
  }
  /**
   * Output only. The fully qualified resource name of the target site. `project
   * s/{project}/locations/{location}/collections/{collection}/dataStores/{data_
   * store}/siteSearchEngine/targetSites/{target_site}` The `target_site_id` is
   * system-generated.
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
   * Required. Input only. The user provided URI pattern from which the
   * `generated_uri_pattern` is generated.
   *
   * @param string $providedUriPattern
   */
  public function setProvidedUriPattern($providedUriPattern)
  {
    $this->providedUriPattern = $providedUriPattern;
  }
  /**
   * @return string
   */
  public function getProvidedUriPattern()
  {
    return $this->providedUriPattern;
  }
  /**
   * Output only. Root domain of the provided_uri_pattern.
   *
   * @param string $rootDomainUri
   */
  public function setRootDomainUri($rootDomainUri)
  {
    $this->rootDomainUri = $rootDomainUri;
  }
  /**
   * @return string
   */
  public function getRootDomainUri()
  {
    return $this->rootDomainUri;
  }
  /**
   * Output only. Site ownership and validity verification status.
   *
   * @param GoogleCloudDiscoveryengineV1betaSiteVerificationInfo $siteVerificationInfo
   */
  public function setSiteVerificationInfo(GoogleCloudDiscoveryengineV1betaSiteVerificationInfo $siteVerificationInfo)
  {
    $this->siteVerificationInfo = $siteVerificationInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSiteVerificationInfo
   */
  public function getSiteVerificationInfo()
  {
    return $this->siteVerificationInfo;
  }
  /**
   * The type of the target site, e.g., whether the site is to be included or
   * excluded.
   *
   * Accepted values: TYPE_UNSPECIFIED, INCLUDE, EXCLUDE
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
   * Output only. The target site's last updated time.
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
class_alias(GoogleCloudDiscoveryengineV1betaTargetSite::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaTargetSite');
