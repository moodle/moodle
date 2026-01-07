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

class ModifyColumnFamiliesRequest extends \Google\Collection
{
  protected $collection_key = 'modifications';
  /**
   * Optional. If true, ignore safety checks when modifying the column families.
   *
   * @var bool
   */
  public $ignoreWarnings;
  protected $modificationsType = Modification::class;
  protected $modificationsDataType = 'array';

  /**
   * Optional. If true, ignore safety checks when modifying the column families.
   *
   * @param bool $ignoreWarnings
   */
  public function setIgnoreWarnings($ignoreWarnings)
  {
    $this->ignoreWarnings = $ignoreWarnings;
  }
  /**
   * @return bool
   */
  public function getIgnoreWarnings()
  {
    return $this->ignoreWarnings;
  }
  /**
   * Required. Modifications to be atomically applied to the specified table's
   * families. Entries are applied in order, meaning that earlier modifications
   * can be masked by later ones (in the case of repeated updates to the same
   * family, for example).
   *
   * @param Modification[] $modifications
   */
  public function setModifications($modifications)
  {
    $this->modifications = $modifications;
  }
  /**
   * @return Modification[]
   */
  public function getModifications()
  {
    return $this->modifications;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModifyColumnFamiliesRequest::class, 'Google_Service_BigtableAdmin_ModifyColumnFamiliesRequest');
