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

namespace Google\Service\ServiceUsage;

class AddEnableRulesResponse extends \Google\Collection
{
  protected $collection_key = 'addedValues';
  /**
   * The values added to the parent consumer policy.
   *
   * @var string[]
   */
  public $addedValues;
  /**
   * The parent consumer policy. It can be
   * `projects/12345/consumerPolicies/default`, or
   * `folders/12345/consumerPolicies/default`, or
   * `organizations/12345/consumerPolicies/default`.
   *
   * @var string
   */
  public $parent;

  /**
   * The values added to the parent consumer policy.
   *
   * @param string[] $addedValues
   */
  public function setAddedValues($addedValues)
  {
    $this->addedValues = $addedValues;
  }
  /**
   * @return string[]
   */
  public function getAddedValues()
  {
    return $this->addedValues;
  }
  /**
   * The parent consumer policy. It can be
   * `projects/12345/consumerPolicies/default`, or
   * `folders/12345/consumerPolicies/default`, or
   * `organizations/12345/consumerPolicies/default`.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddEnableRulesResponse::class, 'Google_Service_ServiceUsage_AddEnableRulesResponse');
