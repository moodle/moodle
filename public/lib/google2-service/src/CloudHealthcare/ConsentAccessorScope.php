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

namespace Google\Service\CloudHealthcare;

class ConsentAccessorScope extends \Google\Model
{
  /**
   * An individual, group, or access role that identifies the accessor or a
   * characteristic of the accessor. This can be a resource ID (such as
   * `{resourceType}/{id}`) or an external URI. This value must be present.
   *
   * @var string
   */
  public $actor;
  /**
   * An abstract identifier that describes the environment or conditions under
   * which the accessor is acting. If it's not specified, it applies to all
   * environments.
   *
   * @var string
   */
  public $environment;
  /**
   * The intent of data use. If it's not specified, it applies to all purposes.
   *
   * @var string
   */
  public $purpose;

  /**
   * An individual, group, or access role that identifies the accessor or a
   * characteristic of the accessor. This can be a resource ID (such as
   * `{resourceType}/{id}`) or an external URI. This value must be present.
   *
   * @param string $actor
   */
  public function setActor($actor)
  {
    $this->actor = $actor;
  }
  /**
   * @return string
   */
  public function getActor()
  {
    return $this->actor;
  }
  /**
   * An abstract identifier that describes the environment or conditions under
   * which the accessor is acting. If it's not specified, it applies to all
   * environments.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * The intent of data use. If it's not specified, it applies to all purposes.
   *
   * @param string $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return string
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsentAccessorScope::class, 'Google_Service_CloudHealthcare_ConsentAccessorScope');
