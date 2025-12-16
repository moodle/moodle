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

namespace Google\Service\GKEHub;

class BinaryAuthorizationConfig extends \Google\Collection
{
  protected $collection_key = 'policyBindings';
  /**
   * @var string
   */
  public $evaluationMode;
  protected $policyBindingsType = PolicyBinding::class;
  protected $policyBindingsDataType = 'array';

  /**
   * @param string
   */
  public function setEvaluationMode($evaluationMode)
  {
    $this->evaluationMode = $evaluationMode;
  }
  /**
   * @return string
   */
  public function getEvaluationMode()
  {
    return $this->evaluationMode;
  }
  /**
   * @param PolicyBinding[]
   */
  public function setPolicyBindings($policyBindings)
  {
    $this->policyBindings = $policyBindings;
  }
  /**
   * @return PolicyBinding[]
   */
  public function getPolicyBindings()
  {
    return $this->policyBindings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BinaryAuthorizationConfig::class, 'Google_Service_GKEHub_BinaryAuthorizationConfig');
