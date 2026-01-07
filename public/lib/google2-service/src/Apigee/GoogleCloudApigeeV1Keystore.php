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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Keystore extends \Google\Collection
{
  protected $collection_key = 'aliases';
  /**
   * Output only. Aliases in this keystore.
   *
   * @var string[]
   */
  public $aliases;
  /**
   * Required. Resource ID for this keystore. Values must match the regular
   * expression `[\w[:space:].-]{1,255}`.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. Aliases in this keystore.
   *
   * @param string[] $aliases
   */
  public function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  /**
   * @return string[]
   */
  public function getAliases()
  {
    return $this->aliases;
  }
  /**
   * Required. Resource ID for this keystore. Values must match the regular
   * expression `[\w[:space:].-]{1,255}`.
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
class_alias(GoogleCloudApigeeV1Keystore::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Keystore');
