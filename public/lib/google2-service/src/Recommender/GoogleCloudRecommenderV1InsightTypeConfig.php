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

class GoogleCloudRecommenderV1InsightTypeConfig extends \Google\Model
{
  /**
   * Allows clients to store small amounts of arbitrary data. Annotations must
   * follow the Kubernetes syntax. The total size of all keys and values
   * combined is limited to 256k. Key can have 2 segments: prefix (optional) and
   * name (required), separated by a slash (/). Prefix must be a DNS subdomain.
   * Name must be 63 characters or less, begin and end with alphanumerics, with
   * dashes (-), underscores (_), dots (.), and alphanumerics between.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * A user-settable field to provide a human-readable name to be used in user
   * interfaces.
   *
   * @var string
   */
  public $displayName;
  /**
   * Fingerprint of the InsightTypeConfig. Provides optimistic locking when
   * updating.
   *
   * @var string
   */
  public $etag;
  protected $insightTypeGenerationConfigType = GoogleCloudRecommenderV1InsightTypeGenerationConfig::class;
  protected $insightTypeGenerationConfigDataType = '';
  /**
   * Identifier. Name of insight type config. Eg, projects/[PROJECT_NUMBER]/loca
   * tions/[LOCATION]/insightTypes/[INSIGHT_TYPE_ID]/config
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. The revision ID of the config. A new revision is
   * committed whenever the config is changed in any way. The format is an
   * 8-character hexadecimal string.
   *
   * @var string
   */
  public $revisionId;
  /**
   * Last time when the config was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Allows clients to store small amounts of arbitrary data. Annotations must
   * follow the Kubernetes syntax. The total size of all keys and values
   * combined is limited to 256k. Key can have 2 segments: prefix (optional) and
   * name (required), separated by a slash (/). Prefix must be a DNS subdomain.
   * Name must be 63 characters or less, begin and end with alphanumerics, with
   * dashes (-), underscores (_), dots (.), and alphanumerics between.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * A user-settable field to provide a human-readable name to be used in user
   * interfaces.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Fingerprint of the InsightTypeConfig. Provides optimistic locking when
   * updating.
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
   * InsightTypeGenerationConfig which configures the generation of insights for
   * this insight type.
   *
   * @param GoogleCloudRecommenderV1InsightTypeGenerationConfig $insightTypeGenerationConfig
   */
  public function setInsightTypeGenerationConfig(GoogleCloudRecommenderV1InsightTypeGenerationConfig $insightTypeGenerationConfig)
  {
    $this->insightTypeGenerationConfig = $insightTypeGenerationConfig;
  }
  /**
   * @return GoogleCloudRecommenderV1InsightTypeGenerationConfig
   */
  public function getInsightTypeGenerationConfig()
  {
    return $this->insightTypeGenerationConfig;
  }
  /**
   * Identifier. Name of insight type config. Eg, projects/[PROJECT_NUMBER]/loca
   * tions/[LOCATION]/insightTypes/[INSIGHT_TYPE_ID]/config
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
   * Output only. Immutable. The revision ID of the config. A new revision is
   * committed whenever the config is changed in any way. The format is an
   * 8-character hexadecimal string.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Last time when the config was updated.
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
class_alias(GoogleCloudRecommenderV1InsightTypeConfig::class, 'Google_Service_Recommender_GoogleCloudRecommenderV1InsightTypeConfig');
