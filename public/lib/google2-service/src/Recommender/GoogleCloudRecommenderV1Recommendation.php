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

class GoogleCloudRecommenderV1Recommendation extends \Google\Collection
{
  /**
   * Recommendation has unspecified priority.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  /**
   * Recommendation has P4 priority (lowest priority).
   */
  public const PRIORITY_P4 = 'P4';
  /**
   * Recommendation has P3 priority (second lowest priority).
   */
  public const PRIORITY_P3 = 'P3';
  /**
   * Recommendation has P2 priority (second highest priority).
   */
  public const PRIORITY_P2 = 'P2';
  /**
   * Recommendation has P1 priority (highest priority).
   */
  public const PRIORITY_P1 = 'P1';
  protected $collection_key = 'targetResources';
  protected $additionalImpactType = GoogleCloudRecommenderV1Impact::class;
  protected $additionalImpactDataType = 'array';
  protected $associatedInsightsType = GoogleCloudRecommenderV1RecommendationInsightReference::class;
  protected $associatedInsightsDataType = 'array';
  protected $contentType = GoogleCloudRecommenderV1RecommendationContent::class;
  protected $contentDataType = '';
  /**
   * Free-form human readable summary in English. The maximum length is 500
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Fingerprint of the Recommendation. Provides optimistic locking when
   * updating states.
   *
   * @var string
   */
  public $etag;
  /**
   * Last time this recommendation was refreshed by the system that created it
   * in the first place.
   *
   * @var string
   */
  public $lastRefreshTime;
  /**
   * Identifier. Name of recommendation.
   *
   * @var string
   */
  public $name;
  protected $primaryImpactType = GoogleCloudRecommenderV1Impact::class;
  protected $primaryImpactDataType = '';
  /**
   * Recommendation's priority.
   *
   * @var string
   */
  public $priority;
  /**
   * Contains an identifier for a subtype of recommendations produced for the
   * same recommender. Subtype is a function of content and impact, meaning a
   * new subtype might be added when significant changes to `content` or
   * `primary_impact.category` are introduced. See the Recommenders section to
   * see a list of subtypes for a given Recommender. Examples: For recommender =
   * "google.iam.policy.Recommender", recommender_subtype can be one of
   * "REMOVE_ROLE"/"REPLACE_ROLE"
   *
   * @var string
   */
  public $recommenderSubtype;
  protected $stateInfoType = GoogleCloudRecommenderV1RecommendationStateInfo::class;
  protected $stateInfoDataType = '';
  /**
   * Fully qualified resource names that this recommendation is targeting.
   *
   * @var string[]
   */
  public $targetResources;
  /**
   * Corresponds to a mutually exclusive group ID within a recommender. A non-
   * empty ID indicates that the recommendation belongs to a mutually exclusive
   * group. This means that only one recommendation within the group is
   * suggested to be applied.
   *
   * @var string
   */
  public $xorGroupId;

  /**
   * Optional set of additional impact that this recommendation may have when
   * trying to optimize for the primary category. These may be positive or
   * negative.
   *
   * @param GoogleCloudRecommenderV1Impact[] $additionalImpact
   */
  public function setAdditionalImpact($additionalImpact)
  {
    $this->additionalImpact = $additionalImpact;
  }
  /**
   * @return GoogleCloudRecommenderV1Impact[]
   */
  public function getAdditionalImpact()
  {
    return $this->additionalImpact;
  }
  /**
   * Insights that led to this recommendation.
   *
   * @param GoogleCloudRecommenderV1RecommendationInsightReference[] $associatedInsights
   */
  public function setAssociatedInsights($associatedInsights)
  {
    $this->associatedInsights = $associatedInsights;
  }
  /**
   * @return GoogleCloudRecommenderV1RecommendationInsightReference[]
   */
  public function getAssociatedInsights()
  {
    return $this->associatedInsights;
  }
  /**
   * Content of the recommendation describing recommended changes to resources.
   *
   * @param GoogleCloudRecommenderV1RecommendationContent $content
   */
  public function setContent(GoogleCloudRecommenderV1RecommendationContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return GoogleCloudRecommenderV1RecommendationContent
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
   * Fingerprint of the Recommendation. Provides optimistic locking when
   * updating states.
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
   * Last time this recommendation was refreshed by the system that created it
   * in the first place.
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
   * Identifier. Name of recommendation.
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
   * The primary impact that this recommendation can have while trying to
   * optimize for one category.
   *
   * @param GoogleCloudRecommenderV1Impact $primaryImpact
   */
  public function setPrimaryImpact(GoogleCloudRecommenderV1Impact $primaryImpact)
  {
    $this->primaryImpact = $primaryImpact;
  }
  /**
   * @return GoogleCloudRecommenderV1Impact
   */
  public function getPrimaryImpact()
  {
    return $this->primaryImpact;
  }
  /**
   * Recommendation's priority.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, P4, P3, P2, P1
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Contains an identifier for a subtype of recommendations produced for the
   * same recommender. Subtype is a function of content and impact, meaning a
   * new subtype might be added when significant changes to `content` or
   * `primary_impact.category` are introduced. See the Recommenders section to
   * see a list of subtypes for a given Recommender. Examples: For recommender =
   * "google.iam.policy.Recommender", recommender_subtype can be one of
   * "REMOVE_ROLE"/"REPLACE_ROLE"
   *
   * @param string $recommenderSubtype
   */
  public function setRecommenderSubtype($recommenderSubtype)
  {
    $this->recommenderSubtype = $recommenderSubtype;
  }
  /**
   * @return string
   */
  public function getRecommenderSubtype()
  {
    return $this->recommenderSubtype;
  }
  /**
   * Information for state. Contains state and metadata.
   *
   * @param GoogleCloudRecommenderV1RecommendationStateInfo $stateInfo
   */
  public function setStateInfo(GoogleCloudRecommenderV1RecommendationStateInfo $stateInfo)
  {
    $this->stateInfo = $stateInfo;
  }
  /**
   * @return GoogleCloudRecommenderV1RecommendationStateInfo
   */
  public function getStateInfo()
  {
    return $this->stateInfo;
  }
  /**
   * Fully qualified resource names that this recommendation is targeting.
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
  /**
   * Corresponds to a mutually exclusive group ID within a recommender. A non-
   * empty ID indicates that the recommendation belongs to a mutually exclusive
   * group. This means that only one recommendation within the group is
   * suggested to be applied.
   *
   * @param string $xorGroupId
   */
  public function setXorGroupId($xorGroupId)
  {
    $this->xorGroupId = $xorGroupId;
  }
  /**
   * @return string
   */
  public function getXorGroupId()
  {
    return $this->xorGroupId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommenderV1Recommendation::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1Recommendation');
