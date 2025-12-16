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

namespace Google\Service\Analytics;

class ProfileFilterLink extends \Google\Model
{
  protected $filterRefType = FilterRef::class;
  protected $filterRefDataType = '';
  /**
   * Profile filter link ID.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for Analytics filter.
   *
   * @var string
   */
  public $kind;
  protected $profileRefType = ProfileRef::class;
  protected $profileRefDataType = '';
  /**
   * The rank of this profile filter link relative to the other filters linked
   * to the same profile. For readonly (i.e., list and get) operations, the rank
   * always starts at 1. For write (i.e., create, update, or delete) operations,
   * you may specify a value between 0 and 255 inclusively, [0, 255]. In order
   * to insert a link at the end of the list, either don't specify a rank or set
   * a rank to a number greater than the largest rank in the list. In order to
   * insert a link to the beginning of the list specify a rank that is less than
   * or equal to 1. The new link will move all existing filters with the same or
   * lower rank down the list. After the link is inserted/updated/deleted all
   * profile filter links will be renumbered starting at 1.
   *
   * @var int
   */
  public $rank;
  /**
   * Link for this profile filter link.
   *
   * @var string
   */
  public $selfLink;

  /**
   * Filter for this link.
   *
   * @param FilterRef $filterRef
   */
  public function setFilterRef(FilterRef $filterRef)
  {
    $this->filterRef = $filterRef;
  }
  /**
   * @return FilterRef
   */
  public function getFilterRef()
  {
    return $this->filterRef;
  }
  /**
   * Profile filter link ID.
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
   * Resource type for Analytics filter.
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
   * View (Profile) for this link.
   *
   * @param ProfileRef $profileRef
   */
  public function setProfileRef(ProfileRef $profileRef)
  {
    $this->profileRef = $profileRef;
  }
  /**
   * @return ProfileRef
   */
  public function getProfileRef()
  {
    return $this->profileRef;
  }
  /**
   * The rank of this profile filter link relative to the other filters linked
   * to the same profile. For readonly (i.e., list and get) operations, the rank
   * always starts at 1. For write (i.e., create, update, or delete) operations,
   * you may specify a value between 0 and 255 inclusively, [0, 255]. In order
   * to insert a link at the end of the list, either don't specify a rank or set
   * a rank to a number greater than the largest rank in the list. In order to
   * insert a link to the beginning of the list specify a rank that is less than
   * or equal to 1. The new link will move all existing filters with the same or
   * lower rank down the list. After the link is inserted/updated/deleted all
   * profile filter links will be renumbered starting at 1.
   *
   * @param int $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return int
   */
  public function getRank()
  {
    return $this->rank;
  }
  /**
   * Link for this profile filter link.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProfileFilterLink::class, 'Google_Service_Analytics_ProfileFilterLink');
