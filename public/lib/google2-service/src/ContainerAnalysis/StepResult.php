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

namespace Google\Service\ContainerAnalysis;

class StepResult extends \Google\Model
{
  /**
   * @var string
   */
  public $attestationContentName;
  /**
   * @var string
   */
  public $attestationType;
  /**
   * @var string
   */
  public $name;

  /**
   * @param string $attestationContentName
   */
  public function setAttestationContentName($attestationContentName)
  {
    $this->attestationContentName = $attestationContentName;
  }
  /**
   * @return string
   */
  public function getAttestationContentName()
  {
    return $this->attestationContentName;
  }
  /**
   * @param string $attestationType
   */
  public function setAttestationType($attestationType)
  {
    $this->attestationType = $attestationType;
  }
  /**
   * @return string
   */
  public function getAttestationType()
  {
    return $this->attestationType;
  }
  /**
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
class_alias(StepResult::class, 'Google_Service_ContainerAnalysis_StepResult');
