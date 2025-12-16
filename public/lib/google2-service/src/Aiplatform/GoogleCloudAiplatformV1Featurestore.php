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

class GoogleCloudAiplatformV1Featurestore extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * State when the featurestore configuration is not being updated and the
   * fields reflect the current configuration of the featurestore. The
   * featurestore is usable in this state.
   */
  public const STATE_STABLE = 'STABLE';
  /**
   * The state of the featurestore configuration when it is being updated.
   * During an update, the fields reflect either the original configuration or
   * the updated configuration of the featurestore. For example,
   * `online_serving_config.fixed_node_count` can take minutes to update. While
   * the update is in progress, the featurestore is in the UPDATING state, and
   * the value of `fixed_node_count` can be the original value or the updated
   * value, depending on the progress of the operation. Until the update
   * completes, the actual number of nodes can still be the original value of
   * `fixed_node_count`. The featurestore is still usable in this state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. Timestamp when this Featurestore was created.
   *
   * @var string
   */
  public $createTime;
  protected $encryptionSpecType = GoogleCloudAiplatformV1EncryptionSpec::class;
  protected $encryptionSpecDataType = '';
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your
   * Featurestore. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one Featurestore(System
   * labels are excluded)." System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Name of the Featurestore. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
   *
   * @var string
   */
  public $name;
  protected $onlineServingConfigType = GoogleCloudAiplatformV1FeaturestoreOnlineServingConfig::class;
  protected $onlineServingConfigDataType = '';
  /**
   * Optional. TTL in days for feature values that will be stored in online
   * serving storage. The Feature Store online storage periodically removes
   * obsolete feature values older than `online_storage_ttl_days` since the
   * feature generation time. Note that `online_storage_ttl_days` should be less
   * than or equal to `offline_storage_ttl_days` for each EntityType under a
   * featurestore. If not set, default to 4000 days
   *
   * @var int
   */
  public $onlineStorageTtlDays;
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
   * Output only. State of the featurestore.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when this Featurestore was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this Featurestore was created.
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
   * Optional. Customer-managed encryption key spec for data storage. If set,
   * both of the online and offline data storage will be secured by this key.
   *
   * @param GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec
   */
  public function setEncryptionSpec(GoogleCloudAiplatformV1EncryptionSpec $encryptionSpec)
  {
    $this->encryptionSpec = $encryptionSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1EncryptionSpec
   */
  public function getEncryptionSpec()
  {
    return $this->encryptionSpec;
  }
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
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
   * Featurestore. Label keys and values can be no longer than 64 characters
   * (Unicode codepoints), can only contain lowercase letters, numeric
   * characters, underscores and dashes. International characters are allowed.
   * See https://goo.gl/xmQnxf for more information on and examples of labels.
   * No more than 64 user labels can be associated with one Featurestore(System
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
   * Output only. Name of the Featurestore. Format:
   * `projects/{project}/locations/{location}/featurestores/{featurestore}`
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
   * Optional. Config for online storage resources. The field should not co-
   * exist with the field of `OnlineStoreReplicationConfig`. If both of it and
   * OnlineStoreReplicationConfig are unset, the feature store will not have an
   * online store and cannot be used for online serving.
   *
   * @param GoogleCloudAiplatformV1FeaturestoreOnlineServingConfig $onlineServingConfig
   */
  public function setOnlineServingConfig(GoogleCloudAiplatformV1FeaturestoreOnlineServingConfig $onlineServingConfig)
  {
    $this->onlineServingConfig = $onlineServingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1FeaturestoreOnlineServingConfig
   */
  public function getOnlineServingConfig()
  {
    return $this->onlineServingConfig;
  }
  /**
   * Optional. TTL in days for feature values that will be stored in online
   * serving storage. The Feature Store online storage periodically removes
   * obsolete feature values older than `online_storage_ttl_days` since the
   * feature generation time. Note that `online_storage_ttl_days` should be less
   * than or equal to `offline_storage_ttl_days` for each EntityType under a
   * featurestore. If not set, default to 4000 days
   *
   * @param int $onlineStorageTtlDays
   */
  public function setOnlineStorageTtlDays($onlineStorageTtlDays)
  {
    $this->onlineStorageTtlDays = $onlineStorageTtlDays;
  }
  /**
   * @return int
   */
  public function getOnlineStorageTtlDays()
  {
    return $this->onlineStorageTtlDays;
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
   * Output only. State of the featurestore.
   *
   * Accepted values: STATE_UNSPECIFIED, STABLE, UPDATING
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
   * Output only. Timestamp when this Featurestore was last updated.
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
class_alias(GoogleCloudAiplatformV1Featurestore::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Featurestore');
