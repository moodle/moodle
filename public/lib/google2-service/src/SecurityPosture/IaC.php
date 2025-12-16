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

class IaC extends \Google\Model
{
  /**
   * Optional. A Terraform plan file, formatted as a stringified JSON object. To
   * learn how to generate a Terraform plan file in JSON format, see [JSON
   * output format](https://developer.hashicorp.com/terraform/internals/json-
   * format) in the Terraform documentation.
   *
   * @var string
   */
  public $tfPlan;

  /**
   * Optional. A Terraform plan file, formatted as a stringified JSON object. To
   * learn how to generate a Terraform plan file in JSON format, see [JSON
   * output format](https://developer.hashicorp.com/terraform/internals/json-
   * format) in the Terraform documentation.
   *
   * @param string $tfPlan
   */
  public function setTfPlan($tfPlan)
  {
    $this->tfPlan = $tfPlan;
  }
  /**
   * @return string
   */
  public function getTfPlan()
  {
    return $this->tfPlan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IaC::class, 'Google_Service_SecurityPosture_IaC');
