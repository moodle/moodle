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

namespace Google\Service\TagManager;

class Zone extends \Google\Collection
{
  protected $collection_key = 'childContainer';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  protected $boundaryType = ZoneBoundary::class;
  protected $boundaryDataType = '';
  protected $childContainerType = ZoneChildContainer::class;
  protected $childContainerDataType = 'array';
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The fingerprint of the GTM Zone as computed at storage time. This value is
   * recomputed whenever the zone is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Zone display name.
   *
   * @var string
   */
  public $name;
  /**
   * User notes on how to apply this zone in the container.
   *
   * @var string
   */
  public $notes;
  /**
   * GTM Zone's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  protected $typeRestrictionType = ZoneTypeRestriction::class;
  protected $typeRestrictionDataType = '';
  /**
   * GTM Workspace ID.
   *
   * @var string
   */
  public $workspaceId;
  /**
   * The Zone ID uniquely identifies the GTM Zone.
   *
   * @var string
   */
  public $zoneId;

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * This Zone's boundary.
   *
   * @param ZoneBoundary $boundary
   */
  public function setBoundary(ZoneBoundary $boundary)
  {
    $this->boundary = $boundary;
  }
  /**
   * @return ZoneBoundary
   */
  public function getBoundary()
  {
    return $this->boundary;
  }
  /**
   * Containers that are children of this Zone.
   *
   * @param ZoneChildContainer[] $childContainer
   */
  public function setChildContainer($childContainer)
  {
    $this->childContainer = $childContainer;
  }
  /**
   * @return ZoneChildContainer[]
   */
  public function getChildContainer()
  {
    return $this->childContainer;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * The fingerprint of the GTM Zone as computed at storage time. This value is
   * recomputed whenever the zone is modified.
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
   * Zone display name.
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
   * User notes on how to apply this zone in the container.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * GTM Zone's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
  /**
   * This Zone's type restrictions.
   *
   * @param ZoneTypeRestriction $typeRestriction
   */
  public function setTypeRestriction(ZoneTypeRestriction $typeRestriction)
  {
    $this->typeRestriction = $typeRestriction;
  }
  /**
   * @return ZoneTypeRestriction
   */
  public function getTypeRestriction()
  {
    return $this->typeRestriction;
  }
  /**
   * GTM Workspace ID.
   *
   * @param string $workspaceId
   */
  public function setWorkspaceId($workspaceId)
  {
    $this->workspaceId = $workspaceId;
  }
  /**
   * @return string
   */
  public function getWorkspaceId()
  {
    return $this->workspaceId;
  }
  /**
   * The Zone ID uniquely identifies the GTM Zone.
   *
   * @param string $zoneId
   */
  public function setZoneId($zoneId)
  {
    $this->zoneId = $zoneId;
  }
  /**
   * @return string
   */
  public function getZoneId()
  {
    return $this->zoneId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Zone::class, 'Google_Service_TagManager_Zone');
