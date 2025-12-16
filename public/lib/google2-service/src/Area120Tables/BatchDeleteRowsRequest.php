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

namespace Google\Service\Area120Tables;

class BatchDeleteRowsRequest extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Required. The names of the rows to delete. All rows must belong to the
   * parent table or else the entire batch will fail. A maximum of 500 rows can
   * be deleted in a batch. Format: tables/{table}/rows/{row}
   *
   * @var string[]
   */
  public $names;

  /**
   * Required. The names of the rows to delete. All rows must belong to the
   * parent table or else the entire batch will fail. A maximum of 500 rows can
   * be deleted in a batch. Format: tables/{table}/rows/{row}
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchDeleteRowsRequest::class, 'Google_Service_Area120Tables_BatchDeleteRowsRequest');
