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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EntityType extends \Google\Model
{
  /**
   * Output only. Timestamp when this EntityType was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the EntityType.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Used to perform a consistent read-modify-write updates. If not
   * set, a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your
   * EntityTypes. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one EntityType (System
   * labels are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  protected $monitoringConfigType = GoogleCloudAiplatformV1FeaturestoreMonitoringConfig::class;
  protected $monitoringConfigDataType = '';
  /**
   * Immutable. Name of the EntityType. Format: `projects/{project}/locations/{l
   * ocation}/featurestores/{featurestore}/entityTypes/{entity_type}` The last
   * part entity_type is assigned by the client. The entity_type can be up to 64
   * characters long and can consist only of ASCII Latin letters A-Z and a-z and
   * underscore(_), and ASCII digits 0-9 starting with a letter. The value will
   * be unique given a featurestore.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Config for data retention policy in offline storage. TTL in days
   * for feature values that will be stored in offline storage. The Feature
   * Store offline storage periodically removes obsolete feature values older
   * than `offline_storage_ttl_days` since the feature generation time. If unset
   * (or explicitly set to 0), default to 4000 days TTL.
   *
   * @var int
   */
  public $offlineStorageTtlDays;
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
   * Output only. Timestamp when this EntityType was most recently updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this EntityType was created.
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
   * Optional. Description of the EntityType.
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
   * Optional. Used to perform a consistent read-modify-write updates. If not
   * set, a blind "overwrite" update happens.
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
   * Optional. The labels with user-defined metadata to organize your
   * EntityTypes. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one EntityType (System
   * labels are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
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
   * Optional. The default monitoring configuration for all Features with value
   * type (Feature.ValueType) BOOL, STRING, DOUBLE or INT64 under this
   * EntityType. If this is populated with
   * [FeaturestoreMonitoringConfig.monitoring_interval] specified, snapshot
   * analysis monitoring is enabled. Otherwise, snapshot analysis monitoring is
   * disabled.
   *
   * @param GoogleCloudAiplatformV1FeaturestoreMonitoringConfig $monitoringConfig
   */
  public function setMonitoringConfig(GoogleCloudAiplatformV1FeaturestoreMonitoringConfig $monitoringConfig)
  {
    $this->monitoringConfig = $monitoringConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeaturestoreMonitoringConfig
   */
  public function getMonitoringConfig()
  {
    return $this->monitoringConfig;
  }
  /**
   * Immutable. Name of the EntityType. Format: `projects/{project}/locations/{l
   * ocation}/featurestores/{featurestore}/entityTypes/{entity_type}` The last
   * part entity_type is assigned by the client. The entity_type can be up to 64
   * characters long and can consist only of ASCII Latin letters A-Z and a-z and
   * underscore(_), and ASCII digits 0-9 starting with a letter. The value will
   * be unique given a featurestore.
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
   * Optional. Config for data retention policy in offline storage. TTL in days
   * for feature values that will be stored in offline storage. The Feature
   * Store offline storage periodically removes obsolete feature values older
   * than `offline_storage_ttl_days` since the feature generation time. If unset
   * (or explicitly set to 0), default to 4000 days TTL.
   *
   * @param int $offlineStorageTtlDays
   */
  public function setOfflineStorageTtlDays($offlineStorageTtlDays)
  {
    $this->offlineStorageTtlDays = $offlineStorageTtlDays;
  }
  /**
   * @return int
   */
  public function getOfflineStorageTtlDays()
  {
    return $this->offlineStorageTtlDays;
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
   * Output only. Timestamp when this EntityType was most recently updated.
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
class_alias(GoogleCloudAiplatformV1EntityType::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EntityType');
