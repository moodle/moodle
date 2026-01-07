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

namespace Google\Service\Container;

class K8sBetaAPIConfig extends \Google\Collection
{
  protected $collection_key = 'enabledApis';
  /**
   * Enabled k8s beta APIs.
   *
   * @var string[]
   */
  public $enabledApis;

  /**
   * Enabled k8s beta APIs.
   *
   * @param string[] $enabledApis
   */
  public function setEnabledApis($enabledApis)
  {
    $this->enabledApis = $enabledApis;
  }
  /**
   * @return string[]
   */
  public function getEnabledApis()
  {
    return $this->enabledApis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(K8sBetaAPIConfig::class, 'Google_Service_Container_K8sBetaAPIConfig');
