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

namespace Google\Service\Compute;

class PerInstanceConfig extends \Google\Model
{
  /**
   * The per-instance configuration is being applied to the instance, but is not
   * yet effective, possibly waiting for the instance to, for example,REFRESH.
   */
  public const STATUS_APPLYING = 'APPLYING';
  /**
   * The per-instance configuration deletion is being applied on the instance,
   * possibly waiting for the instance to, for example, REFRESH.
   */
  public const STATUS_DELETING = 'DELETING';
  /**
   * The per-instance configuration is effective on the instance, meaning that
   * all disks, ips and metadata specified in this configuration are attached or
   * set on the instance.
   */
  public const STATUS_EFFECTIVE = 'EFFECTIVE';
  /**
   * *[Default]* The default status, when no per-instance configuration exists.
   */
  public const STATUS_NONE = 'NONE';
  /**
   * The per-instance configuration is set on an instance but not been applied
   * yet.
   */
  public const STATUS_UNAPPLIED = 'UNAPPLIED';
  /**
   * The per-instance configuration has been deleted, but the deletion is not
   * yet applied.
   */
  public const STATUS_UNAPPLIED_DELETION = 'UNAPPLIED_DELETION';
  /**
   * Fingerprint of this per-instance config. This field can be used in
   * optimistic locking. It is ignored when inserting a per-instance config. An
   * up-to-date fingerprint must be provided in order to update an existing per-
   * instance configuration or the field needs to be unset.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * The name of a per-instance configuration and its corresponding instance.
   * Serves as a merge key during UpdatePerInstanceConfigs operations, that is,
   * if a per-instance configuration with the same name exists then it will be
   * updated, otherwise a new one will be created for the VM instance with the
   * same name. An attempt to create a per-instance configuration for a VM
   * instance that either doesn't exist or is not part of the group will result
   * in an error.
   *
   * @var string
   */
  public $name;
  protected $preservedStateType = PreservedState::class;
  protected $preservedStateDataType = '';
  /**
   * The status of applying this per-instance configuration on the corresponding
   * managed instance.
   *
   * @var string
   */
  public $status;

  /**
   * Fingerprint of this per-instance config. This field can be used in
   * optimistic locking. It is ignored when inserting a per-instance config. An
   * up-to-date fingerprint must be provided in order to update an existing per-
   * instance configuration or the field needs to be unset.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * The name of a per-instance configuration and its corresponding instance.
   * Serves as a merge key during UpdatePerInstanceConfigs operations, that is,
   * if a per-instance configuration with the same name exists then it will be
   * updated, otherwise a new one will be created for the VM instance with the
   * same name. An attempt to create a per-instance configuration for a VM
   * instance that either doesn't exist or is not part of the group will result
   * in an error.
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
   * The intended preserved state for the given instance. Does not contain
   * preserved state generated from a stateful policy.
   *
   * @param PreservedState $preservedState
   */
  public function setPreservedState(PreservedState $preservedState)
  {
    $this->preservedState = $preservedState;
  }
  /**
   * @return PreservedState
   */
  public function getPreservedState()
  {
    return $this->preservedState;
  }
  /**
   * The status of applying this per-instance configuration on the corresponding
   * managed instance.
   *
   * Accepted values: APPLYING, DELETING, EFFECTIVE, NONE, UNAPPLIED,
   * UNAPPLIED_DELETION
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerInstanceConfig::class, 'Google_Service_Compute_PerInstanceConfig');
