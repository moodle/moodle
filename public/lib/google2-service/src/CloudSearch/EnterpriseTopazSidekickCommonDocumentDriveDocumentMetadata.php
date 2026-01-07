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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata extends \Google\Model
{
  public const SCOPE_UNKNOWN_DOCUMENT_SCOPE = 'UNKNOWN_DOCUMENT_SCOPE';
  public const SCOPE_LIMITED = 'LIMITED';
  public const SCOPE_DASHER_DOMAIN_WITH_LINK = 'DASHER_DOMAIN_WITH_LINK';
  public const SCOPE_DASHER_DOMAIN = 'DASHER_DOMAIN';
  public const SCOPE_PUBLIC_WITH_LINK = 'PUBLIC_WITH_LINK';
  public const SCOPE_PUBLIC = 'PUBLIC';
  public const SCOPE_TEAM_DRIVE = 'TEAM_DRIVE';
  /**
   * The drive document cosmo id. Client could use the id to build a URL to open
   * a document. Please use Document.document_id.
   *
   * @deprecated
   * @var string
   */
  public $documentId;
  /**
   * Additional field to identify whether a document is private since scope set
   * to LIMITED can mean both that the doc is private or that it's shared with
   * others. is_private indicates whether the doc is not shared with anyone
   * except for the owner.
   *
   * @var bool
   */
  public $isPrivate;
  /**
   * Timestamp of the most recent comment added to the document in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastCommentTimeMs;
  /**
   * Timestamp of the most recent edit from the current user in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastEditTimeMs;
  /**
   * Last modification time of the document (independent of the user that
   * modified it).
   *
   * @var string
   */
  public $lastModificationTimeMillis;
  /**
   * Timestamp of the last updated time of the document in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $lastUpdatedTimeMs;
  /**
   * Timestamp of the most recent view from the current user in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastViewTimeMs;
  protected $ownerType = EnterpriseTopazSidekickCommonPerson::class;
  protected $ownerDataType = '';
  /**
   * ACL scope of the document which identifies the sharing status of the doc
   * (e.g., limited, shared with link, team drive, ...).
   *
   * @var string
   */
  public $scope;

  /**
   * The drive document cosmo id. Client could use the id to build a URL to open
   * a document. Please use Document.document_id.
   *
   * @deprecated
   * @param string $documentId
   */
  public function setDocumentId($documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * Additional field to identify whether a document is private since scope set
   * to LIMITED can mean both that the doc is private or that it's shared with
   * others. is_private indicates whether the doc is not shared with anyone
   * except for the owner.
   *
   * @param bool $isPrivate
   */
  public function setIsPrivate($isPrivate)
  {
    $this->isPrivate = $isPrivate;
  }
  /**
   * @return bool
   */
  public function getIsPrivate()
  {
    return $this->isPrivate;
  }
  /**
   * Timestamp of the most recent comment added to the document in milliseconds
   * since epoch.
   *
   * @param string $lastCommentTimeMs
   */
  public function setLastCommentTimeMs($lastCommentTimeMs)
  {
    $this->lastCommentTimeMs = $lastCommentTimeMs;
  }
  /**
   * @return string
   */
  public function getLastCommentTimeMs()
  {
    return $this->lastCommentTimeMs;
  }
  /**
   * Timestamp of the most recent edit from the current user in milliseconds
   * since epoch.
   *
   * @param string $lastEditTimeMs
   */
  public function setLastEditTimeMs($lastEditTimeMs)
  {
    $this->lastEditTimeMs = $lastEditTimeMs;
  }
  /**
   * @return string
   */
  public function getLastEditTimeMs()
  {
    return $this->lastEditTimeMs;
  }
  /**
   * Last modification time of the document (independent of the user that
   * modified it).
   *
   * @param string $lastModificationTimeMillis
   */
  public function setLastModificationTimeMillis($lastModificationTimeMillis)
  {
    $this->lastModificationTimeMillis = $lastModificationTimeMillis;
  }
  /**
   * @return string
   */
  public function getLastModificationTimeMillis()
  {
    return $this->lastModificationTimeMillis;
  }
  /**
   * Timestamp of the last updated time of the document in milliseconds since
   * epoch.
   *
   * @param string $lastUpdatedTimeMs
   */
  public function setLastUpdatedTimeMs($lastUpdatedTimeMs)
  {
    $this->lastUpdatedTimeMs = $lastUpdatedTimeMs;
  }
  /**
   * @return string
   */
  public function getLastUpdatedTimeMs()
  {
    return $this->lastUpdatedTimeMs;
  }
  /**
   * Timestamp of the most recent view from the current user in milliseconds
   * since epoch.
   *
   * @param string $lastViewTimeMs
   */
  public function setLastViewTimeMs($lastViewTimeMs)
  {
    $this->lastViewTimeMs = $lastViewTimeMs;
  }
  /**
   * @return string
   */
  public function getLastViewTimeMs()
  {
    return $this->lastViewTimeMs;
  }
  /**
   * The owner of the document.
   *
   * @param EnterpriseTopazSidekickCommonPerson $owner
   */
  public function setOwner(EnterpriseTopazSidekickCommonPerson $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPerson
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * ACL scope of the document which identifies the sharing status of the doc
   * (e.g., limited, shared with link, team drive, ...).
   *
   * Accepted values: UNKNOWN_DOCUMENT_SCOPE, LIMITED, DASHER_DOMAIN_WITH_LINK,
   * DASHER_DOMAIN, PUBLIC_WITH_LINK, PUBLIC, TEAM_DRIVE
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickCommonDocumentDriveDocumentMetadata');
