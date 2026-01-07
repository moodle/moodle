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

namespace Google\Service\Dataform;

class Workspace extends \Google\Model
{
  /**
   * Output only. The timestamp of when the workspace was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataEncryptionStateType = DataEncryptionState::class;
  protected $dataEncryptionStateDataType = '';
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  /**
   * Identifier. The workspace's name.
   *
   * @var string
   */
  public $name;
  protected $privateResourceMetadataType = PrivateResourceMetadata::class;
  protected $privateResourceMetadataDataType = '';

  /**
   * Output only. The timestamp of when the workspace was created.
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
   * Output only. A data encryption state of a Git repository if this Workspace
   * is protected by a KMS key.
   *
   * @param DataEncryptionState $dataEncryptionState
   */
  public function setDataEncryptionState(DataEncryptionState $dataEncryptionState)
  {
    $this->dataEncryptionState = $dataEncryptionState;
  }
  /**
   * @return DataEncryptionState
   */
  public function getDataEncryptionState()
  {
    return $this->dataEncryptionState;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Identifier. The workspace's name.
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
   * Output only. Metadata indicating whether this resource is user-scoped. For
   * `Workspace` resources, the `user_scoped` field is always `true`.
   *
   * @param PrivateResourceMetadata $privateResourceMetadata
   */
  public function setPrivateResourceMetadata(PrivateResourceMetadata $privateResourceMetadata)
  {
    $this->privateResourceMetadata = $privateResourceMetadata;
  }
  /**
   * @return PrivateResourceMetadata
   */
  public function getPrivateResourceMetadata()
  {
    return $this->privateResourceMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Workspace::class, 'Google_Service_Dataform_Workspace');
