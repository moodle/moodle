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

namespace Google\Service\Pubsub;

class Topic extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The topic does not have any persistent errors.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Ingestion from the data source has encountered a permanent error. See the
   * more detailed error state in the corresponding ingestion source
   * configuration.
   */
  public const STATE_INGESTION_RESOURCE_ERROR = 'INGESTION_RESOURCE_ERROR';
  protected $collection_key = 'messageTransforms';
  protected $ingestionDataSourceSettingsType = IngestionDataSourceSettings::class;
  protected $ingestionDataSourceSettingsDataType = '';
  /**
   * Optional. The resource name of the Cloud KMS CryptoKey to be used to
   * protect access to messages published on this topic. The expected format is
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/pubsub/docs/labels).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Indicates the minimum duration to retain a message after it is
   * published to the topic. If this field is set, messages published to the
   * topic in the last `message_retention_duration` are always available to
   * subscribers. For instance, it allows any attached subscription to [seek to
   * a timestamp](https://cloud.google.com/pubsub/docs/replay-
   * overview#seek_to_a_time) that is up to `message_retention_duration` in the
   * past. If this field is not set, message retention is controlled by settings
   * on individual subscriptions. Cannot be more than 31 days or less than 10
   * minutes.
   *
   * @var string
   */
  public $messageRetentionDuration;
  protected $messageStoragePolicyType = MessageStoragePolicy::class;
  protected $messageStoragePolicyDataType = '';
  protected $messageTransformsType = MessageTransform::class;
  protected $messageTransformsDataType = 'array';
  /**
   * Required. The name of the topic. It must have the format
   * `"projects/{project}/topics/{topic}"`. `{topic}` must start with a letter,
   * and contain only letters (`[A-Za-z]`), numbers (`[0-9]`), dashes (`-`),
   * underscores (`_`), periods (`.`), tildes (`~`), plus (`+`) or percent signs
   * (`%`). It must be between 3 and 255 characters in length, and it must not
   * start with `"goog"`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Reserved for future use. This field is set only in responses from
   * the server; it is ignored if it is set in any requests.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $schemaSettingsType = SchemaSettings::class;
  protected $schemaSettingsDataType = '';
  /**
   * Output only. An output-only field indicating the state of the topic.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;

  /**
   * Optional. Settings for ingestion from a data source into this topic.
   *
   * @param IngestionDataSourceSettings $ingestionDataSourceSettings
   */
  public function setIngestionDataSourceSettings(IngestionDataSourceSettings $ingestionDataSourceSettings)
  {
    $this->ingestionDataSourceSettings = $ingestionDataSourceSettings;
  }
  /**
   * @return IngestionDataSourceSettings
   */
  public function getIngestionDataSourceSettings()
  {
    return $this->ingestionDataSourceSettings;
  }
  /**
   * Optional. The resource name of the Cloud KMS CryptoKey to be used to
   * protect access to messages published on this topic. The expected format is
   * `projects/locations/keyRings/cryptoKeys`.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Optional. See [Creating and managing labels]
   * (https://cloud.google.com/pubsub/docs/labels).
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
   * Optional. Indicates the minimum duration to retain a message after it is
   * published to the topic. If this field is set, messages published to the
   * topic in the last `message_retention_duration` are always available to
   * subscribers. For instance, it allows any attached subscription to [seek to
   * a timestamp](https://cloud.google.com/pubsub/docs/replay-
   * overview#seek_to_a_time) that is up to `message_retention_duration` in the
   * past. If this field is not set, message retention is controlled by settings
   * on individual subscriptions. Cannot be more than 31 days or less than 10
   * minutes.
   *
   * @param string $messageRetentionDuration
   */
  public function setMessageRetentionDuration($messageRetentionDuration)
  {
    $this->messageRetentionDuration = $messageRetentionDuration;
  }
  /**
   * @return string
   */
  public function getMessageRetentionDuration()
  {
    return $this->messageRetentionDuration;
  }
  /**
   * Optional. Policy constraining the set of Google Cloud Platform regions
   * where messages published to the topic may be stored. If not present, then
   * no constraints are in effect.
   *
   * @param MessageStoragePolicy $messageStoragePolicy
   */
  public function setMessageStoragePolicy(MessageStoragePolicy $messageStoragePolicy)
  {
    $this->messageStoragePolicy = $messageStoragePolicy;
  }
  /**
   * @return MessageStoragePolicy
   */
  public function getMessageStoragePolicy()
  {
    return $this->messageStoragePolicy;
  }
  /**
   * Optional. Transforms to be applied to messages published to the topic.
   * Transforms are applied in the order specified.
   *
   * @param MessageTransform[] $messageTransforms
   */
  public function setMessageTransforms($messageTransforms)
  {
    $this->messageTransforms = $messageTransforms;
  }
  /**
   * @return MessageTransform[]
   */
  public function getMessageTransforms()
  {
    return $this->messageTransforms;
  }
  /**
   * Required. The name of the topic. It must have the format
   * `"projects/{project}/topics/{topic}"`. `{topic}` must start with a letter,
   * and contain only letters (`[A-Za-z]`), numbers (`[0-9]`), dashes (`-`),
   * underscores (`_`), periods (`.`), tildes (`~`), plus (`+`) or percent signs
   * (`%`). It must be between 3 and 255 characters in length, and it must not
   * start with `"goog"`.
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
   * Optional. Reserved for future use. This field is set only in responses from
   * the server; it is ignored if it is set in any requests.
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
   * Optional. Settings for validating messages published against a schema.
   *
   * @param SchemaSettings $schemaSettings
   */
  public function setSchemaSettings(SchemaSettings $schemaSettings)
  {
    $this->schemaSettings = $schemaSettings;
  }
  /**
   * @return SchemaSettings
   */
  public function getSchemaSettings()
  {
    return $this->schemaSettings;
  }
  /**
   * Output only. An output-only field indicating the state of the topic.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INGESTION_RESOURCE_ERROR
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
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Topic::class, 'Google_Service_Pubsub_Topic');
