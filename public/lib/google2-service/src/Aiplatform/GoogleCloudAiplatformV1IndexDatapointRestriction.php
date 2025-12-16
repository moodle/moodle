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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1IndexDatapointRestriction extends \Google\Collection
{
  protected $collection_key = 'denyList';
  /**
   * The attributes to allow in this namespace. e.g.: 'red'
   *
   * @var string[]
   */
  public $allowList;
  /**
   * The attributes to deny in this namespace. e.g.: 'blue'
   *
   * @var string[]
   */
  public $denyList;
  /**
   * The namespace of this restriction. e.g.: color.
   *
   * @var string
   */
  public $namespace;

  /**
   * The attributes to allow in this namespace. e.g.: 'red'
   *
   * @param string[] $allowList
   */
  public function setAllowList($allowList)
  {
    $this->allowList = $allowList;
  }
  /**
   * @return string[]
   */
  public function getAllowList()
  {
    return $this->allowList;
  }
  /**
   * The attributes to deny in this namespace. e.g.: 'blue'
   *
   * @param string[] $denyList
   */
  public function setDenyList($denyList)
  {
    $this->denyList = $denyList;
  }
  /**
   * @return string[]
   */
  public function getDenyList()
  {
    return $this->denyList;
  }
  /**
   * The namespace of this restriction. e.g.: color.
   *
   * @param string $namespace
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }
  /**
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1IndexDatapointRestriction::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1IndexDatapointRestriction');
