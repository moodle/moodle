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

namespace Google\Service\Datastream;

class Stream extends \Google\Collection
{
  /**
   * Unspecified stream state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The stream has been created but has not yet started streaming data.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * The stream is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The stream is paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The stream is in maintenance mode. Updates are rejected on the resource in
   * this state.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The stream is experiencing an error that is preventing data from being
   * streamed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The stream has experienced a terminal failure.
   */
  public const STATE_FAILED_PERMANENTLY = 'FAILED_PERMANENTLY';
  /**
   * The stream is starting, but not yet running.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * The Stream is no longer reading new events, but still writing events in the
   * buffer.
   */
  public const STATE_DRAINING = 'DRAINING';
  protected $collection_key = 'ruleSets';
  protected $backfillAllType = BackfillAllStrategy::class;
  protected $backfillAllDataType = '';
  protected $backfillNoneType = BackfillNoneStrategy::class;
  protected $backfillNoneDataType = '';
  /**
   * Output only. The creation time of the stream.
   *
   * @var string
   */
  public $createTime;
  /**
   * Immutable. A reference to a KMS encryption key. If provided, it will be
   * used to encrypt the data. If left blank, data will be encrypted using an
   * internal Stream-specific encryption key provisioned through KMS.
   *
   * @var string
   */
  public $customerManagedEncryptionKey;
  protected $destinationConfigType = DestinationConfig::class;
  protected $destinationConfigDataType = '';
  /**
   * Required. Display name.
   *
   * @var string
   */
  public $displayName;
  protected $errorsType = Error::class;
  protected $errorsDataType = 'array';
  /**
   * Labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. If the stream was recovered, the time of the last recovery.
   * Note: This field is currently experimental.
   *
   * @var string
   */
  public $lastRecoveryTime;
  /**
   * Output only. Identifier. The stream's name.
   *
   * @var string
   */
  public $name;
  protected $ruleSetsType = RuleSet::class;
  protected $ruleSetsDataType = 'array';
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
  protected $sourceConfigType = SourceConfig::class;
  protected $sourceConfigDataType = '';
  /**
   * The state of the stream.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last update time of the stream.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Automatically backfill objects included in the stream source configuration.
   * Specific objects can be excluded.
   *
   * @param BackfillAllStrategy $backfillAll
   */
  public function setBackfillAll(BackfillAllStrategy $backfillAll)
  {
    $this->backfillAll = $backfillAll;
  }
  /**
   * @return BackfillAllStrategy
   */
  public function getBackfillAll()
  {
    return $this->backfillAll;
  }
  /**
   * Do not automatically backfill any objects.
   *
   * @param BackfillNoneStrategy $backfillNone
   */
  public function setBackfillNone(BackfillNoneStrategy $backfillNone)
  {
    $this->backfillNone = $backfillNone;
  }
  /**
   * @return BackfillNoneStrategy
   */
  public function getBackfillNone()
  {
    return $this->backfillNone;
  }
  /**
   * Output only. The creation time of the stream.
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
   * Immutable. A reference to a KMS encryption key. If provided, it will be
   * used to encrypt the data. If left blank, data will be encrypted using an
   * internal Stream-specific encryption key provisioned through KMS.
   *
   * @param string $customerManagedEncryptionKey
   */
  public function setCustomerManagedEncryptionKey($customerManagedEncryptionKey)
  {
    $this->customerManagedEncryptionKey = $customerManagedEncryptionKey;
  }
  /**
   * @return string
   */
  public function getCustomerManagedEncryptionKey()
  {
    return $this->customerManagedEncryptionKey;
  }
  /**
   * Required. Destination connection profile configuration.
   *
   * @param DestinationConfig $destinationConfig
   */
  public function setDestinationConfig(DestinationConfig $destinationConfig)
  {
    $this->destinationConfig = $destinationConfig;
  }
  /**
   * @return DestinationConfig
   */
  public function getDestinationConfig()
  {
    return $this->destinationConfig;
  }
  /**
   * Required. Display name.
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
   * Output only. Errors on the Stream.
   *
   * @param Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Labels.
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
   * Output only. If the stream was recovered, the time of the last recovery.
   * Note: This field is currently experimental.
   *
   * @param string $lastRecoveryTime
   */
  public function setLastRecoveryTime($lastRecoveryTime)
  {
    $this->lastRecoveryTime = $lastRecoveryTime;
  }
  /**
   * @return string
   */
  public function getLastRecoveryTime()
  {
    return $this->lastRecoveryTime;
  }
  /**
   * Output only. Identifier. The stream's name.
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
   * Optional. Rule sets to apply to the stream.
   *
   * @param RuleSet[] $ruleSets
   */
  public function setRuleSets($ruleSets)
  {
    $this->ruleSets = $ruleSets;
  }
  /**
   * @return RuleSet[]
   */
  public function getRuleSets()
  {
    return $this->ruleSets;
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
   * Required. Source connection profile configuration.
   *
   * @param SourceConfig $sourceConfig
   */
  public function setSourceConfig(SourceConfig $sourceConfig)
  {
    $this->sourceConfig = $sourceConfig;
  }
  /**
   * @return SourceConfig
   */
  public function getSourceConfig()
  {
    return $this->sourceConfig;
  }
  /**
   * The state of the stream.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_STARTED, RUNNING, PAUSED,
   * MAINTENANCE, FAILED, FAILED_PERMANENTLY, STARTING, DRAINING
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
   * Output only. The last update time of the stream.
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
class_alias(Stream::class, 'Google_Service_Datastream_Stream');
