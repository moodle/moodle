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

namespace Google\Service\Dfareporting;

class ObjectFilter extends \Google\Collection
{
  /**
   * Profile has access to none of the objects.
   */
  public const STATUS_NONE = 'NONE';
  /**
   * Profile has access to only specific objects.
   */
  public const STATUS_ASSIGNED = 'ASSIGNED';
  /**
   * Profile has access to all objects.
   */
  public const STATUS_ALL = 'ALL';
  protected $collection_key = 'objectIds';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#objectFilter".
   *
   * @var string
   */
  public $kind;
  /**
   * Applicable when status is ASSIGNED. The user has access to objects with
   * these object IDs.
   *
   * @var string[]
   */
  public $objectIds;
  /**
   * Status of the filter. NONE means the user has access to none of the
   * objects. ALL means the user has access to all objects. ASSIGNED means the
   * user has access to the objects with IDs in the objectIds list.
   *
   * @var string
   */
  public $status;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#objectFilter".
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
   * Applicable when status is ASSIGNED. The user has access to objects with
   * these object IDs.
   *
   * @param string[] $objectIds
   */
  public function setObjectIds($objectIds)
  {
    $this->objectIds = $objectIds;
  }
  /**
   * @return string[]
   */
  public function getObjectIds()
  {
    return $this->objectIds;
  }
  /**
   * Status of the filter. NONE means the user has access to none of the
   * objects. ALL means the user has access to all objects. ASSIGNED means the
   * user has access to the objects with IDs in the objectIds list.
   *
   * Accepted values: NONE, ASSIGNED, ALL
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ObjectFilter::class, 'Google_Service_Dfareporting_ObjectFilter');
