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

class GoogleCloudAiplatformV1FeatureOnlineStore extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * State when the featureOnlineStore configuration is not being updated and
   * the fields reflect the current configuration of the featureOnlineStore. The
   * featureOnlineStore is usable in this state.
   */
  public const STATE_STABLE = 'STABLE';
  /**
   * The state of the featureOnlineStore configuration when it is being updated.
   * During an update, the fields reflect either the original configuration or
   * the updated configuration of the featureOnlineStore. The featureOnlineStore
   * is still usable in this state.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $bigtableType = GoogleCloudAiplatformV1FeatureOnlineStoreBigtable::class;
  protected $bigtableDataType = '';
  /**
   * Output only. Timestamp when this FeatureOnlineStore was created.
   *
   * @var string
   */
  public $createTime;
  protected $dedicatedServingEndpointType = GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint::class;
  protected $dedicatedServingEndpointDataType = '';
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
   * FeatureOnlineStore. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information on and examples of
   * labels. No more than 64 user labels can be associated with one
   * FeatureOnlineStore(System labels are excluded)." System reserved label keys
   * are prefixed with "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the FeatureOnlineStore. Format: `projects/{project}/loc
   * ations/{location}/featureOnlineStores/{featureOnlineStore}`
   *
   * @var string
   */
  public $name;
  protected $optimizedType = GoogleCloudAiplatformV1FeatureOnlineStoreOptimized::class;
  protected $optimizedDataType = '';
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
   * Output only. State of the featureOnlineStore.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when this FeatureOnlineStore was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Contains settings for the Cloud Bigtable instance that will be created to
   * serve featureValues for all FeatureViews under this FeatureOnlineStore.
   *
   * @param GoogleCloudAiplatformV1FeatureOnlineStoreBigtable $bigtable
   */
  public function setBigtable(GoogleCloudAiplatformV1FeatureOnlineStoreBigtable $bigtable)
  {
    $this->bigtable = $bigtable;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureOnlineStoreBigtable
   */
  public function getBigtable()
  {
    return $this->bigtable;
  }
  /**
   * Output only. Timestamp when this FeatureOnlineStore was created.
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
   * Optional. The dedicated serving endpoint for this FeatureOnlineStore, which
   * is different from common Vertex service endpoint.
   *
   * @param GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint $dedicatedServingEndpoint
   */
  public function setDedicatedServingEndpoint(GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint $dedicatedServingEndpoint)
  {
    $this->dedicatedServingEndpoint = $dedicatedServingEndpoint;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureOnlineStoreDedicatedServingEndpoint
   */
  public function getDedicatedServingEndpoint()
  {
    return $this->dedicatedServingEndpoint;
  }
  /**
   * Optional. Customer-managed encryption key spec for data storage. If set,
   * online store will be secured by this key.
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
   * FeatureOnlineStore. Label keys and values can be no longer than 64
   * characters (Unicode codepoints), can only contain lowercase letters,
   * numeric characters, underscores and dashes. International characters are
   * allowed. See https://goo.gl/xmQnxf for more information on and examples of
   * labels. No more than 64 user labels can be associated with one
   * FeatureOnlineStore(System labels are excluded)." System reserved label keys
   * are prefixed with "aiplatform.googleapis.com/" and are immutable.
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
   * Identifier. Name of the FeatureOnlineStore. Format: `projects/{project}/loc
   * ations/{location}/featureOnlineStores/{featureOnlineStore}`
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
   * Contains settings for the Optimized store that will be created to serve
   * featureValues for all FeatureViews under this FeatureOnlineStore. When
   * choose Optimized storage type, need to set
   * PrivateServiceConnectConfig.enable_private_service_connect to use private
   * endpoint. Otherwise will use public endpoint by default.
   *
   * @param GoogleCloudAiplatformV1FeatureOnlineStoreOptimized $optimized
   */
  public function setOptimized(GoogleCloudAiplatformV1FeatureOnlineStoreOptimized $optimized)
  {
    $this->optimized = $optimized;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureOnlineStoreOptimized
   */
  public function getOptimized()
  {
    return $this->optimized;
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
   * Output only. State of the featureOnlineStore.
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
   * Output only. Timestamp when this FeatureOnlineStore was last updated.
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
class_alias(GoogleCloudAiplatformV1FeatureOnlineStore::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureOnlineStore');
