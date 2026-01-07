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

namespace Google\Service\SecurityPosture;

class IaCValidationReport extends \Google\Collection
{
  protected $collection_key = 'violations';
  /**
   * Additional information about the report.
   *
   * @var string
   */
  public $note;
  protected $violationsType = Violation::class;
  protected $violationsDataType = 'array';

  /**
   * Additional information about the report.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * A list of every Violation found in the IaC configuration.
   *
   * @param Violation[] $violations
   */
  public function setViolations($violations)
  {
    $this->violations = $violations;
  }
  /**
   * @return Violation[]
   */
  public function getViolations()
  {
    return $this->violations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IaCValidationReport::class, 'Google_Service_SecurityPosture_IaCValidationReport');
