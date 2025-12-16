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

namespace Google\Service\ServiceManagement;

class Mixin extends \Google\Model
{
  /**
   * The fully qualified name of the interface which is included.
   *
   * @var string
   */
  public $name;
  /**
   * If non-empty specifies a path under which inherited HTTP paths are rooted.
   *
   * @var string
   */
  public $root;

  /**
   * The fully qualified name of the interface which is included.
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
   * If non-empty specifies a path under which inherited HTTP paths are rooted.
   *
   * @param string $root
   */
  public function setRoot($root)
  {
    $this->root = $root;
  }
  /**
   * @return string
   */
  public function getRoot()
  {
    return $this->root;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Mixin::class, 'Google_Service_ServiceManagement_Mixin');
