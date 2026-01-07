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

class ItemAcl extends \Google\Collection
{
  /**
   * The default value when this item does not inherit an ACL. Use
   * NOT_APPLICABLE when inheritAclFrom is empty. An item without ACL
   * inheritance can still have ACLs supplied by its own readers and
   * deniedReaders fields.
   */
  public const ACL_INHERITANCE_TYPE_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * During an authorization conflict, the ACL of the child item determines its
   * read access.
   */
  public const ACL_INHERITANCE_TYPE_CHILD_OVERRIDE = 'CHILD_OVERRIDE';
  /**
   * During an authorization conflict, the ACL of the parent item specified in
   * the inheritAclFrom field determines read access.
   */
  public const ACL_INHERITANCE_TYPE_PARENT_OVERRIDE = 'PARENT_OVERRIDE';
  /**
   * Access is granted only if this item and the parent item specified in the
   * inheritAclFrom field both permit read access.
   */
  public const ACL_INHERITANCE_TYPE_BOTH_PERMIT = 'BOTH_PERMIT';
  protected $collection_key = 'readers';
  /**
   * Sets the type of access rules to apply when an item inherits its ACL from a
   * parent. This should always be set in tandem with the inheritAclFrom field.
   * Also, when the inheritAclFrom field is set, this field should be set to a
   * valid AclInheritanceType.
   *
   * @var string
   */
  public $aclInheritanceType;
  protected $deniedReadersType = Principal::class;
  protected $deniedReadersDataType = 'array';
  /**
   * The name of the item to inherit the Access Permission List (ACL) from.
   * Note: ACL inheritance *only* provides access permissions to child items and
   * does not define structural relationships, nor does it provide convenient
   * ways to delete large groups of items. Deleting an ACL parent from the index
   * only alters the access permissions of child items that reference the parent
   * in the inheritAclFrom field. The item is still in the index, but may not
   * visible in search results. By contrast, deletion of a container item also
   * deletes all items that reference the container via the containerName field.
   * The maximum length for this field is 1536 characters.
   *
   * @var string
   */
  public $inheritAclFrom;
  protected $ownersType = Principal::class;
  protected $ownersDataType = 'array';
  protected $readersType = Principal::class;
  protected $readersDataType = 'array';

  /**
   * Sets the type of access rules to apply when an item inherits its ACL from a
   * parent. This should always be set in tandem with the inheritAclFrom field.
   * Also, when the inheritAclFrom field is set, this field should be set to a
   * valid AclInheritanceType.
   *
   * Accepted values: NOT_APPLICABLE, CHILD_OVERRIDE, PARENT_OVERRIDE,
   * BOTH_PERMIT
   *
   * @param self::ACL_INHERITANCE_TYPE_* $aclInheritanceType
   */
  public function setAclInheritanceType($aclInheritanceType)
  {
    $this->aclInheritanceType = $aclInheritanceType;
  }
  /**
   * @return self::ACL_INHERITANCE_TYPE_*
   */
  public function getAclInheritanceType()
  {
    return $this->aclInheritanceType;
  }
  /**
   * List of principals who are explicitly denied access to the item in search
   * results. While principals are denied access by default, use denied readers
   * to handle exceptions and override the list allowed readers. The maximum
   * number of elements is 100.
   *
   * @param Principal[] $deniedReaders
   */
  public function setDeniedReaders($deniedReaders)
  {
    $this->deniedReaders = $deniedReaders;
  }
  /**
   * @return Principal[]
   */
  public function getDeniedReaders()
  {
    return $this->deniedReaders;
  }
  /**
   * The name of the item to inherit the Access Permission List (ACL) from.
   * Note: ACL inheritance *only* provides access permissions to child items and
   * does not define structural relationships, nor does it provide convenient
   * ways to delete large groups of items. Deleting an ACL parent from the index
   * only alters the access permissions of child items that reference the parent
   * in the inheritAclFrom field. The item is still in the index, but may not
   * visible in search results. By contrast, deletion of a container item also
   * deletes all items that reference the container via the containerName field.
   * The maximum length for this field is 1536 characters.
   *
   * @param string $inheritAclFrom
   */
  public function setInheritAclFrom($inheritAclFrom)
  {
    $this->inheritAclFrom = $inheritAclFrom;
  }
  /**
   * @return string
   */
  public function getInheritAclFrom()
  {
    return $this->inheritAclFrom;
  }
  /**
   * Optional. List of owners for the item. This field has no bearing on
   * document access permissions. It does, however, offer a slight ranking
   * boosts items where the querying user is an owner. The maximum number of
   * elements is 5.
   *
   * @param Principal[] $owners
   */
  public function setOwners($owners)
  {
    $this->owners = $owners;
  }
  /**
   * @return Principal[]
   */
  public function getOwners()
  {
    return $this->owners;
  }
  /**
   * List of principals who are allowed to see the item in search results.
   * Optional if inheriting permissions from another item or if the item is not
   * intended to be visible, such as virtual containers. The maximum number of
   * elements is 1000.
   *
   * @param Principal[] $readers
   */
  public function setReaders($readers)
  {
    $this->readers = $readers;
  }
  /**
   * @return Principal[]
   */
  public function getReaders()
  {
    return $this->readers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemAcl::class, 'Google_Service_CloudSearch_ItemAcl');
