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

namespace Google\Service\Datastore;

class PropertyMask extends \Google\Collection
{
  protected $collection_key = 'paths';
  /**
   * The paths to the properties covered by this mask. A path is a list of
   * property names separated by dots (`.`), for example `foo.bar` means the
   * property `bar` inside the entity property `foo` inside the entity
   * associated with this path. If a property name contains a dot `.` or a
   * backslash `\`, then that name must be escaped. A path must not be empty,
   * and may not reference a value inside an array value.
   *
   * @var string[]
   */
  public $paths;

  /**
   * The paths to the properties covered by this mask. A path is a list of
   * property names separated by dots (`.`), for example `foo.bar` means the
   * property `bar` inside the entity property `foo` inside the entity
   * associated with this path. If a property name contains a dot `.` or a
   * backslash `\`, then that name must be escaped. A path must not be empty,
   * and may not reference a value inside an array value.
   *
   * @param string[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return string[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PropertyMask::class, 'Google_Service_Datastore_PropertyMask');
