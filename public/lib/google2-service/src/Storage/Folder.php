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

namespace Google\Service\Storage;

class Folder extends \Google\Model
{
  /**
   * The name of the bucket containing this folder.
   *
   * @var string
   */
  public $bucket;
  /**
   * The creation time of the folder in RFC 3339 format.
   *
   * @var string
   */
  public $createTime;
  /**
   * The ID of the folder, including the bucket name, folder name.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of item this is. For folders, this is always storage#folder.
   *
   * @var string
   */
  public $kind;
  /**
   * The version of the metadata for this folder. Used for preconditions and for
   * detecting changes in metadata.
   *
   * @var string
   */
  public $metageneration;
  /**
   * The name of the folder. Required if not specified by URL parameter.
   *
   * @var string
   */
  public $name;
  protected $pendingRenameInfoType = FolderPendingRenameInfo::class;
  protected $pendingRenameInfoDataType = '';
  /**
   * The link to this folder.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The modification time of the folder metadata in RFC 3339 format.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The name of the bucket containing this folder.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * The creation time of the folder in RFC 3339 format.
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
   * The ID of the folder, including the bucket name, folder name.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The kind of item this is. For folders, this is always storage#folder.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The version of the metadata for this folder. Used for preconditions and for
   * detecting changes in metadata.
   *
   * @param string $metageneration
   */
  public function setMetageneration($metageneration)
  {
    $this->metageneration = $metageneration;
  }
  /**
   * @return string
   */
  public function getMetageneration()
  {
    return $this->metageneration;
  }
  /**
   * The name of the folder. Required if not specified by URL parameter.
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
   * Only present if the folder is part of an ongoing rename folder operation.
   * Contains information which can be used to query the operation status.
   *
   * @param FolderPendingRenameInfo $pendingRenameInfo
   */
  public function setPendingRenameInfo(FolderPendingRenameInfo $pendingRenameInfo)
  {
    $this->pendingRenameInfo = $pendingRenameInfo;
  }
  /**
   * @return FolderPendingRenameInfo
   */
  public function getPendingRenameInfo()
  {
    return $this->pendingRenameInfo;
  }
  /**
   * The link to this folder.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The modification time of the folder metadata in RFC 3339 format.
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
class_alias(Folder::class, 'Google_Service_Storage_Folder');
