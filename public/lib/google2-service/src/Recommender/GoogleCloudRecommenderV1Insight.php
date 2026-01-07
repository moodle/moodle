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

namespace Google\Service\Recommender;

class GoogleCloudRecommenderV1Insight extends \Google\Collection
{
  /**
   * Unspecified category.
   */
  public const CATEGORY_CATEGORY_UNSPECIFIED = 'CATEGORY_UNSPECIFIED';
  /**
   * The insight is related to cost.
   */
  public const CATEGORY_COST = 'COST';
  /**
   * The insight is related to security.
   */
  public const CATEGORY_SECURITY = 'SECURITY';
  /**
   * The insight is related to performance.
   */
  public const CATEGORY_PERFORMANCE = 'PERFORMANCE';
  /**
   * This insight is related to manageability.
   */
  public const CATEGORY_MANAGEABILITY = 'MANAGEABILITY';
  /**
   * The insight is related to sustainability.
   */
  public const CATEGORY_SUSTAINABILITY = 'SUSTAINABILITY';
  /**
   * This insight is related to reliability.
   */
  public const CATEGORY_RELIABILITY = 'RELIABILITY';
  /**
   * Insight has unspecified severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Insight has low severity.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Insight has medium severity.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Insight has high severity.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Insight has critical severity.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  protected $collection_key = 'targetResources';
  protected $associatedRecommendationsType = GoogleCloudRecommenderV1InsightRecommendationReference::class;
  protected $associatedRecommendationsDataType = 'array';
  /**
   * Category being targeted by the insight.
   *
   * @var string
   */
  public $category;
  /**
   * A struct of custom fields to explain the insight. Example:
   * "grantedPermissionsCount": "1000"
   *
   * @var array[]
   */
  public $content;
  /**
   * Free-form human readable summary in English. The maximum length is 500
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Fingerprint of the Insight. Provides optimistic locking when updating
   * states.
   *
   * @var string
   */
  public $etag;
  /**
   * Insight subtype. Insight content schema will be stable for a given subtype.
   *
   * @var string
   */
  public $insightSubtype;
  /**
   * Timestamp of the latest data used to generate the insight.
   *
   * @var string
   */
  public $lastRefreshTime;
  /**
   * Identifier. Name of the insight.
   *
   * @var string
   */
  public $name;
  /**
   * Observation period that led to the insight. The source data used to
   * generate the insight ends at last_refresh_time and begins at
   * (last_refresh_time - observation_period).
   *
   * @var string
   */
  public $observationPeriod;
  /**
   * Insight's severity.
   *
   * @var string
   */
  public $severity;
  protected $stateInfoType = GoogleCloudRecommenderV1InsightStateInfo::class;
  protected $stateInfoDataType = '';
  /**
   * Fully qualified resource names that this insight is targeting.
   *
   * @var string[]
   */
  public $targetResources;

  /**
   * Recommendations derived from this insight.
   *
   * @param GoogleCloudRecommenderV1InsightRecommendationReference[] $associatedRecommendations
   */
  public function setAssociatedRecommendations($associatedRecommendations)
  {
    $this->associatedRecommendations = $associatedRecommendations;
  }
  /**
   * @return GoogleCloudRecommenderV1InsightRecommendationReference[]
   */
  public function getAssociatedRecommendations()
  {
    return $this->associatedRecommendations;
  }
  /**
   * Category being targeted by the insight.
   *
   * Accepted values: CATEGORY_UNSPECIFIED, COST, SECURITY, PERFORMANCE,
   * MANAGEABILITY, SUSTAINABILITY, RELIABILITY
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * A struct of custom fields to explain the insight. Example:
   * "grantedPermissionsCount": "1000"
   *
   * @param array[] $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return array[]
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Free-form human readable summary in English. The maximum length is 500
   * characters.
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
   * Fingerprint of the Insight. Provides optimistic locking when updating
   * states.
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
   * Insight subtype. Insight content schema will be stable for a given subtype.
   *
   * @param string $insightSubtype
   */
  public function setInsightSubtype($insightSubtype)
  {
    $this->insightSubtype = $insightSubtype;
  }
  /**
   * @return string
   */
  public function getInsightSubtype()
  {
    return $this->insightSubtype;
  }
  /**
   * Timestamp of the latest data used to generate the insight.
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * Identifier. Name of the insight.
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
   * Observation period that led to the insight. The source data used to
   * generate the insight ends at last_refresh_time and begins at
   * (last_refresh_time - observation_period).
   *
   * @param string $observationPeriod
   */
  public function setObservationPeriod($observationPeriod)
  {
    $this->observationPeriod = $observationPeriod;
  }
  /**
   * @return string
   */
  public function getObservationPeriod()
  {
    return $this->observationPeriod;
  }
  /**
   * Insight's severity.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, LOW, MEDIUM, HIGH, CRITICAL
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Information state and metadata.
   *
   * @param GoogleCloudRecommenderV1InsightStateInfo $stateInfo
   */
  public function setStateInfo(GoogleCloudRecommenderV1InsightStateInfo $stateInfo)
  {
    $this->stateInfo = $stateInfo;
  }
  /**
   * @return GoogleCloudRecommenderV1InsightStateInfo
   */
  public function getStateInfo()
  {
    return $this->stateInfo;
  }
  /**
   * Fully qualified resource names that this insight is targeting.
   *
   * @param string[] $targetResources
   */
  public function setTargetResources($targetResources)
  {
    $this->targetResources = $targetResources;
  }
  /**
   * @return string[]
   */
  public function getTargetResources()
  {
    return $this->targetResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommenderV1Insight::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1Insight');
