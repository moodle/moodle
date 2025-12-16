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

namespace Google\Service\GKEOnPrem;

class BareMetalApiServerArgument extends \Google\Model
{
  /**
   * Required. The argument name as it appears on the API Server command line,
   * make sure to remove the leading dashes.
   *
   * @var string
   */
  public $argument;
  /**
   * Required. The value of the arg as it will be passed to the API Server
   * command line.
   *
   * @var string
   */
  public $value;

  /**
   * Required. The argument name as it appears on the API Server command line,
   * make sure to remove the leading dashes.
   *
   * @param string $argument
   */
  public function setArgument($argument)
  {
    $this->argument = $argument;
  }
  /**
   * @return string
   */
  public function getArgument()
  {
    return $this->argument;
  }
  /**
   * Required. The value of the arg as it will be passed to the API Server
   * command line.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalApiServerArgument::class, 'Google_Service_GKEOnPrem_BareMetalApiServerArgument');
