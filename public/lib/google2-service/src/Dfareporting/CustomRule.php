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

namespace Google\Service\Dfareporting;

class CustomRule extends \Google\Collection
{
  protected $collection_key = 'ruleBlocks';
  /**
   * Optional. Name of this custom rule.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Priority of the custom rule.
   *
   * @var int
   */
  public $priority;
  protected $ruleBlocksType = RuleBlock::class;
  protected $ruleBlocksDataType = 'array';

  /**
   * Optional. Name of this custom rule.
   *
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
  /**
   * Optional. Priority of the custom rule.
   *
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * Optional. A list of field filter, the custom rule will apply.
   *
   * @param RuleBlock[] $ruleBlocks
   */
  public function setRuleBlocks($ruleBlocks)
  {
    $this->ruleBlocks = $ruleBlocks;
  }
  /**
   * @return RuleBlock[]
   */
  public function getRuleBlocks()
  {
    return $this->ruleBlocks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomRule::class, 'Google_Service_Dfareporting_CustomRule');
