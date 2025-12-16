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

namespace Google\Service\CloudKMS;

class EkmConfig extends \Google\Model
{
  /**
   * Optional. Resource name of the default EkmConnection. Setting this field to
   * the empty string removes the default.
   *
   * @var string
   */
  public $defaultEkmConnection;
  /**
   * Output only. The resource name for the EkmConfig in the format
   * `projects/locations/ekmConfig`.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. Resource name of the default EkmConnection. Setting this field to
   * the empty string removes the default.
   *
   * @param string $defaultEkmConnection
   */
  public function setDefaultEkmConnection($defaultEkmConnection)
  {
    $this->defaultEkmConnection = $defaultEkmConnection;
  }
  /**
   * @return string
   */
  public function getDefaultEkmConnection()
  {
    return $this->defaultEkmConnection;
  }
  /**
   * Output only. The resource name for the EkmConfig in the format
   * `projects/locations/ekmConfig`.
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
class_alias(EkmConfig::class, 'Google_Service_CloudKMS_EkmConfig');
