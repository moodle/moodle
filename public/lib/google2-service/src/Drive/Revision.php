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

namespace Google\Service\Drive;

class Revision extends \Google\Model
{
  /**
   * Output only. Links for exporting Docs Editors files to specific formats.
   *
   * @var string[]
   */
  public $exportLinks;
  /**
   * Output only. The ID of the revision.
   *
   * @var string
   */
  public $id;
  /**
   * Whether to keep this revision forever, even if it is no longer the head
   * revision. If not set, the revision will be automatically purged 30 days
   * after newer content is uploaded. This can be set on a maximum of 200
   * revisions for a file. This field is only applicable to files with binary
   * content in Drive.
   *
   * @var bool
   */
  public $keepForever;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#revision"`.
   *
   * @var string
   */
  public $kind;
  protected $lastModifyingUserType = User::class;
  protected $lastModifyingUserDataType = '';
  /**
   * Output only. The MD5 checksum of the revision's content. This is only
   * applicable to files with binary content in Drive.
   *
   * @var string
   */
  public $md5Checksum;
  /**
   * Output only. The MIME type of the revision.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The last time the revision was modified (RFC 3339 date-time).
   *
   * @var string
   */
  public $modifiedTime;
  /**
   * Output only. The original filename used to create this revision. This is
   * only applicable to files with binary content in Drive.
   *
   * @var string
   */
  public $originalFilename;
  /**
   * Whether subsequent revisions will be automatically republished. This is
   * only applicable to Docs Editors files.
   *
   * @var bool
   */
  public $publishAuto;
  /**
   * Whether this revision is published. This is only applicable to Docs Editors
   * files.
   *
   * @var bool
   */
  public $published;
  /**
   * Output only. A link to the published revision. This is only populated for
   * Docs Editors files.
   *
   * @var string
   */
  public $publishedLink;
  /**
   * Whether this revision is published outside the domain. This is only
   * applicable to Docs Editors files.
   *
   * @var bool
   */
  public $publishedOutsideDomain;
  /**
   * Output only. The size of the revision's content in bytes. This is only
   * applicable to files with binary content in Drive.
   *
   * @var string
   */
  public $size;

  /**
   * Output only. Links for exporting Docs Editors files to specific formats.
   *
   * @param string[] $exportLinks
   */
  public function setExportLinks($exportLinks)
  {
    $this->exportLinks = $exportLinks;
  }
  /**
   * @return string[]
   */
  public function getExportLinks()
  {
    return $this->exportLinks;
  }
  /**
   * Output only. The ID of the revision.
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
   * Whether to keep this revision forever, even if it is no longer the head
   * revision. If not set, the revision will be automatically purged 30 days
   * after newer content is uploaded. This can be set on a maximum of 200
   * revisions for a file. This field is only applicable to files with binary
   * content in Drive.
   *
   * @param bool $keepForever
   */
  public function setKeepForever($keepForever)
  {
    $this->keepForever = $keepForever;
  }
  /**
   * @return bool
   */
  public function getKeepForever()
  {
    return $this->keepForever;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#revision"`.
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
   * Output only. The last user to modify this revision. This field is only
   * populated when the last modification was performed by a signed-in user.
   *
   * @param User $lastModifyingUser
   */
  public function setLastModifyingUser(User $lastModifyingUser)
  {
    $this->lastModifyingUser = $lastModifyingUser;
  }
  /**
   * @return User
   */
  public function getLastModifyingUser()
  {
    return $this->lastModifyingUser;
  }
  /**
   * Output only. The MD5 checksum of the revision's content. This is only
   * applicable to files with binary content in Drive.
   *
   * @param string $md5Checksum
   */
  public function setMd5Checksum($md5Checksum)
  {
    $this->md5Checksum = $md5Checksum;
  }
  /**
   * @return string
   */
  public function getMd5Checksum()
  {
    return $this->md5Checksum;
  }
  /**
   * Output only. The MIME type of the revision.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The last time the revision was modified (RFC 3339 date-time).
   *
   * @param string $modifiedTime
   */
  public function setModifiedTime($modifiedTime)
  {
    $this->modifiedTime = $modifiedTime;
  }
  /**
   * @return string
   */
  public function getModifiedTime()
  {
    return $this->modifiedTime;
  }
  /**
   * Output only. The original filename used to create this revision. This is
   * only applicable to files with binary content in Drive.
   *
   * @param string $originalFilename
   */
  public function setOriginalFilename($originalFilename)
  {
    $this->originalFilename = $originalFilename;
  }
  /**
   * @return string
   */
  public function getOriginalFilename()
  {
    return $this->originalFilename;
  }
  /**
   * Whether subsequent revisions will be automatically republished. This is
   * only applicable to Docs Editors files.
   *
   * @param bool $publishAuto
   */
  public function setPublishAuto($publishAuto)
  {
    $this->publishAuto = $publishAuto;
  }
  /**
   * @return bool
   */
  public function getPublishAuto()
  {
    return $this->publishAuto;
  }
  /**
   * Whether this revision is published. This is only applicable to Docs Editors
   * files.
   *
   * @param bool $published
   */
  public function setPublished($published)
  {
    $this->published = $published;
  }
  /**
   * @return bool
   */
  public function getPublished()
  {
    return $this->published;
  }
  /**
   * Output only. A link to the published revision. This is only populated for
   * Docs Editors files.
   *
   * @param string $publishedLink
   */
  public function setPublishedLink($publishedLink)
  {
    $this->publishedLink = $publishedLink;
  }
  /**
   * @return string
   */
  public function getPublishedLink()
  {
    return $this->publishedLink;
  }
  /**
   * Whether this revision is published outside the domain. This is only
   * applicable to Docs Editors files.
   *
   * @param bool $publishedOutsideDomain
   */
  public function setPublishedOutsideDomain($publishedOutsideDomain)
  {
    $this->publishedOutsideDomain = $publishedOutsideDomain;
  }
  /**
   * @return bool
   */
  public function getPublishedOutsideDomain()
  {
    return $this->publishedOutsideDomain;
  }
  /**
   * Output only. The size of the revision's content in bytes. This is only
   * applicable to files with binary content in Drive.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Revision::class, 'Google_Service_Drive_Revision');
