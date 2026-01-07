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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1NetworkSetting extends \Google\Model
{
  /**
   * The fully qualified name of the network setting.
   *
   * @var string
   */
  public $policySchema;
  /**
   * The value of the network setting.
   *
   * @var array[]
   */
  public $value;

  /**
   * The fully qualified name of the network setting.
   *
   * @param string $policySchema
   */
  public function setPolicySchema($policySchema)
  {
    $this->policySchema = $policySchema;
  }
  /**
   * @return string
   */
  public function getPolicySchema()
  {
    return $this->policySchema;
  }
  /**
   * The value of the network setting.
   *
   * @param array[] $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array[]
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1NetworkSetting::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1NetworkSetting');
