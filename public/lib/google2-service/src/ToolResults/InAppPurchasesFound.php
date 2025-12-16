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

namespace Google\Service\ToolResults;

class InAppPurchasesFound extends \Google\Model
{
  /**
   * The total number of in-app purchases flows explored: how many times the
   * robo tries to buy a SKU.
   *
   * @var int
   */
  public $inAppPurchasesFlowsExplored;
  /**
   * The total number of in-app purchases flows started.
   *
   * @var int
   */
  public $inAppPurchasesFlowsStarted;

  /**
   * The total number of in-app purchases flows explored: how many times the
   * robo tries to buy a SKU.
   *
   * @param int $inAppPurchasesFlowsExplored
   */
  public function setInAppPurchasesFlowsExplored($inAppPurchasesFlowsExplored)
  {
    $this->inAppPurchasesFlowsExplored = $inAppPurchasesFlowsExplored;
  }
  /**
   * @return int
   */
  public function getInAppPurchasesFlowsExplored()
  {
    return $this->inAppPurchasesFlowsExplored;
  }
  /**
   * The total number of in-app purchases flows started.
   *
   * @param int $inAppPurchasesFlowsStarted
   */
  public function setInAppPurchasesFlowsStarted($inAppPurchasesFlowsStarted)
  {
    $this->inAppPurchasesFlowsStarted = $inAppPurchasesFlowsStarted;
  }
  /**
   * @return int
   */
  public function getInAppPurchasesFlowsStarted()
  {
    return $this->inAppPurchasesFlowsStarted;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InAppPurchasesFound::class, 'Google_Service_ToolResults_InAppPurchasesFound');
