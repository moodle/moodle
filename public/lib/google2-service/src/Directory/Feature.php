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

namespace Google\Service\Directory;

class Feature extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etags;
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the feature.
   *
   * @var string
   */
  public $name;

  /**
   * ETag of the resource.
   *
   * @param string $etags
   */
  public function setEtags($etags)
  {
    $this->etags = $etags;
  }
  /**
   * @return string
   */
  public function getEtags()
  {
    return $this->etags;
  }
  /**
   * Kind of resource this is.
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
   * The name of the feature.
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
class_alias(Feature::class, 'Google_Service_Directory_Feature');
