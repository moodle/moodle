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

namespace Google\Service\NetAppFiles;

class KmsConfig extends \Google\Model
{
  /**
   * Unspecified KmsConfig State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * KmsConfig State is Ready
   */
  public const STATE_READY = 'READY';
  /**
   * KmsConfig State is Creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * KmsConfig State is Deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * KmsConfig State is Updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * KmsConfig State is In Use.
   */
  public const STATE_IN_USE = 'IN_USE';
  /**
   * KmsConfig State is Error
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * KmsConfig State is Pending to verify crypto key access.
   */
  public const STATE_KEY_CHECK_PENDING = 'KEY_CHECK_PENDING';
  /**
   * KmsConfig State is Not accessbile by the SDE service account to the crypto
   * key.
   */
  public const STATE_KEY_NOT_REACHABLE = 'KEY_NOT_REACHABLE';
  /**
   * KmsConfig State is Disabling.
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * KmsConfig State is Disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * KmsConfig State is Migrating. The existing volumes are migrating from SMEK
   * to CMEK.
   */
  public const STATE_MIGRATING = 'MIGRATING';
  /**
   * Output only. Create time of the KmsConfig.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Customer managed crypto key resource full name. Format: `projects
   * /{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}
   * `.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * Description of the KmsConfig.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Instructions to provide the access to the customer provided
   * encryption key.
   *
   * @var string
   */
  public $instructions;
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Name of the KmsConfig.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The Service account which will have access to the customer
   * provided encryption key.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. State of the KmsConfig.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the KmsConfig.
   *
   * @var string
   */
  public $stateDetails;

  /**
   * Output only. Create time of the KmsConfig.
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
   * Required. Customer managed crypto key resource full name. Format: `projects
   * /{project}/locations/{location}/keyRings/{key_ring}/cryptoKeys/{crypto_key}
   * `.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * Description of the KmsConfig.
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
   * Output only. Instructions to provide the access to the customer provided
   * encryption key.
   *
   * @param string $instructions
   */
  public function setInstructions($instructions)
  {
    $this->instructions = $instructions;
  }
  /**
   * @return string
   */
  public function getInstructions()
  {
    return $this->instructions;
  }
  /**
   * Labels as key value pairs
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
   * Identifier. Name of the KmsConfig.
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
   * Output only. The Service account which will have access to the customer
   * provided encryption key.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. State of the KmsConfig.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, DELETING, UPDATING,
   * IN_USE, ERROR, KEY_CHECK_PENDING, KEY_NOT_REACHABLE, DISABLING, DISABLED,
   * MIGRATING
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
   * Output only. State details of the KmsConfig.
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KmsConfig::class, 'Google_Service_NetAppFiles_KmsConfig');
