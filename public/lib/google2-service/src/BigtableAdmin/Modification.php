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

namespace Google\Service\BigtableAdmin;

class Modification extends \Google\Model
{
  protected $createType = ColumnFamily::class;
  protected $createDataType = '';
  /**
   * Drop (delete) the column family with the given ID, or fail if no such
   * family exists.
   *
   * @var bool
   */
  public $drop;
  /**
   * The ID of the column family to be modified.
   *
   * @var string
   */
  public $id;
  protected $updateType = ColumnFamily::class;
  protected $updateDataType = '';
  /**
   * Optional. A mask specifying which fields (e.g. `gc_rule`) in the `update`
   * mod should be updated, ignored for other modification types. If unset or
   * empty, we treat it as updating `gc_rule` to be backward compatible.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Create a new column family with the specified schema, or fail if one
   * already exists with the given ID.
   *
   * @param ColumnFamily $create
   */
  public function setCreate(ColumnFamily $create)
  {
    $this->create = $create;
  }
  /**
   * @return ColumnFamily
   */
  public function getCreate()
  {
    return $this->create;
  }
  /**
   * Drop (delete) the column family with the given ID, or fail if no such
   * family exists.
   *
   * @param bool $drop
   */
  public function setDrop($drop)
  {
    $this->drop = $drop;
  }
  /**
   * @return bool
   */
  public function getDrop()
  {
    return $this->drop;
  }
  /**
   * The ID of the column family to be modified.
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
   * Update an existing column family to the specified schema, or fail if no
   * column family exists with the given ID.
   *
   * @param ColumnFamily $update
   */
  public function setUpdate(ColumnFamily $update)
  {
    $this->update = $update;
  }
  /**
   * @return ColumnFamily
   */
  public function getUpdate()
  {
    return $this->update;
  }
  /**
   * Optional. A mask specifying which fields (e.g. `gc_rule`) in the `update`
   * mod should be updated, ignored for other modification types. If unset or
   * empty, we treat it as updating `gc_rule` to be backward compatible.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Modification::class, 'Google_Service_BigtableAdmin_Modification');
