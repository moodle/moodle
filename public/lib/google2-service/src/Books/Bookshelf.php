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

namespace Google\Service\Books;

class Bookshelf extends \Google\Model
{
  /**
   * Whether this bookshelf is PUBLIC or PRIVATE.
   *
   * @var string
   */
  public $access;
  /**
   * Created time for this bookshelf (formatted UTC timestamp with millisecond
   * resolution).
   *
   * @var string
   */
  public $created;
  /**
   * Description of this bookshelf.
   *
   * @var string
   */
  public $description;
  /**
   * Id of this bookshelf, only unique by user.
   *
   * @var int
   */
  public $id;
  /**
   * Resource type for bookshelf metadata.
   *
   * @var string
   */
  public $kind;
  /**
   * URL to this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Title of this bookshelf.
   *
   * @var string
   */
  public $title;
  /**
   * Last modified time of this bookshelf (formatted UTC timestamp with
   * millisecond resolution).
   *
   * @var string
   */
  public $updated;
  /**
   * Number of volumes in this bookshelf.
   *
   * @var int
   */
  public $volumeCount;
  /**
   * Last time a volume was added or removed from this bookshelf (formatted UTC
   * timestamp with millisecond resolution).
   *
   * @var string
   */
  public $volumesLastUpdated;

  /**
   * Whether this bookshelf is PUBLIC or PRIVATE.
   *
   * @param string $access
   */
  public function setAccess($access)
  {
    $this->access = $access;
  }
  /**
   * @return string
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * Created time for this bookshelf (formatted UTC timestamp with millisecond
   * resolution).
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * Description of this bookshelf.
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
   * Id of this bookshelf, only unique by user.
   *
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Resource type for bookshelf metadata.
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
   * URL to this resource.
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
   * Title of this bookshelf.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Last modified time of this bookshelf (formatted UTC timestamp with
   * millisecond resolution).
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Number of volumes in this bookshelf.
   *
   * @param int $volumeCount
   */
  public function setVolumeCount($volumeCount)
  {
    $this->volumeCount = $volumeCount;
  }
  /**
   * @return int
   */
  public function getVolumeCount()
  {
    return $this->volumeCount;
  }
  /**
   * Last time a volume was added or removed from this bookshelf (formatted UTC
   * timestamp with millisecond resolution).
   *
   * @param string $volumesLastUpdated
   */
  public function setVolumesLastUpdated($volumesLastUpdated)
  {
    $this->volumesLastUpdated = $volumesLastUpdated;
  }
  /**
   * @return string
   */
  public function getVolumesLastUpdated()
  {
    return $this->volumesLastUpdated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Bookshelf::class, 'Google_Service_Books_Bookshelf');
