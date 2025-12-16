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

namespace Google\Service\Config;

class TerraformOutput extends \Google\Model
{
  /**
   * Identifies whether Terraform has set this output as a potential sensitive
   * value.
   *
   * @var bool
   */
  public $sensitive;
  /**
   * Value of output.
   *
   * @var array
   */
  public $value;

  /**
   * Identifies whether Terraform has set this output as a potential sensitive
   * value.
   *
   * @param bool $sensitive
   */
  public function setSensitive($sensitive)
  {
    $this->sensitive = $sensitive;
  }
  /**
   * @return bool
   */
  public function getSensitive()
  {
    return $this->sensitive;
  }
  /**
   * Value of output.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TerraformOutput::class, 'Google_Service_Config_TerraformOutput');
