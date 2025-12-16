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

namespace Google\Service\CloudHealthcare;

class SchemaGroup extends \Google\Collection
{
  protected $collection_key = 'members';
  /**
   * True indicates that this is a choice group, meaning that only one of its
   * segments can exist in a given message.
   *
   * @var bool
   */
  public $choice;
  /**
   * The maximum number of times this group can be repeated. 0 or -1 means
   * unbounded.
   *
   * @var int
   */
  public $maxOccurs;
  protected $membersType = GroupOrSegment::class;
  protected $membersDataType = 'array';
  /**
   * The minimum number of times this group must be present/repeated.
   *
   * @var int
   */
  public $minOccurs;
  /**
   * The name of this group. For example, "ORDER_DETAIL".
   *
   * @var string
   */
  public $name;

  /**
   * True indicates that this is a choice group, meaning that only one of its
   * segments can exist in a given message.
   *
   * @param bool $choice
   */
  public function setChoice($choice)
  {
    $this->choice = $choice;
  }
  /**
   * @return bool
   */
  public function getChoice()
  {
    return $this->choice;
  }
  /**
   * The maximum number of times this group can be repeated. 0 or -1 means
   * unbounded.
   *
   * @param int $maxOccurs
   */
  public function setMaxOccurs($maxOccurs)
  {
    $this->maxOccurs = $maxOccurs;
  }
  /**
   * @return int
   */
  public function getMaxOccurs()
  {
    return $this->maxOccurs;
  }
  /**
   * Nested groups and/or segments.
   *
   * @param GroupOrSegment[] $members
   */
  public function setMembers($members)
  {
    $this->members = $members;
  }
  /**
   * @return GroupOrSegment[]
   */
  public function getMembers()
  {
    return $this->members;
  }
  /**
   * The minimum number of times this group must be present/repeated.
   *
   * @param int $minOccurs
   */
  public function setMinOccurs($minOccurs)
  {
    $this->minOccurs = $minOccurs;
  }
  /**
   * @return int
   */
  public function getMinOccurs()
  {
    return $this->minOccurs;
  }
  /**
   * The name of this group. For example, "ORDER_DETAIL".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchemaGroup::class, 'Google_Service_CloudHealthcare_SchemaGroup');
