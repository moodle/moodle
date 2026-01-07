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

class DestinationVolumeParameters extends \Google\Model
{
  /**
   * Description for the destination volume.
   *
   * @var string
   */
  public $description;
  /**
   * Destination volume's share name. If not specified, source volume's share
   * name will be used.
   *
   * @var string
   */
  public $shareName;
  /**
   * Required. Existing destination StoragePool name.
   *
   * @var string
   */
  public $storagePool;
  protected $tieringPolicyType = TieringPolicy::class;
  protected $tieringPolicyDataType = '';
  /**
   * Desired destination volume resource id. If not specified, source volume's
   * resource id will be used. This value must start with a lowercase letter
   * followed by up to 62 lowercase letters, numbers, or hyphens, and cannot end
   * with a hyphen.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Description for the destination volume.
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
   * Destination volume's share name. If not specified, source volume's share
   * name will be used.
   *
   * @param string $shareName
   */
  public function setShareName($shareName)
  {
    $this->shareName = $shareName;
  }
  /**
   * @return string
   */
  public function getShareName()
  {
    return $this->shareName;
  }
  /**
   * Required. Existing destination StoragePool name.
   *
   * @param string $storagePool
   */
  public function setStoragePool($storagePool)
  {
    $this->storagePool = $storagePool;
  }
  /**
   * @return string
   */
  public function getStoragePool()
  {
    return $this->storagePool;
  }
  /**
   * Optional. Tiering policy for the volume.
   *
   * @param TieringPolicy $tieringPolicy
   */
  public function setTieringPolicy(TieringPolicy $tieringPolicy)
  {
    $this->tieringPolicy = $tieringPolicy;
  }
  /**
   * @return TieringPolicy
   */
  public function getTieringPolicy()
  {
    return $this->tieringPolicy;
  }
  /**
   * Desired destination volume resource id. If not specified, source volume's
   * resource id will be used. This value must start with a lowercase letter
   * followed by up to 62 lowercase letters, numbers, or hyphens, and cannot end
   * with a hyphen.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DestinationVolumeParameters::class, 'Google_Service_NetAppFiles_DestinationVolumeParameters');
