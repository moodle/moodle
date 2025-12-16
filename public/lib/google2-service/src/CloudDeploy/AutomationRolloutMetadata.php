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

namespace Google\Service\CloudDeploy;

class AutomationRolloutMetadata extends \Google\Collection
{
  protected $collection_key = 'repairAutomationRuns';
  /**
   * Output only. The names of the AutomationRuns initiated by an advance
   * rollout rule.
   *
   * @var string[]
   */
  public $advanceAutomationRuns;
  /**
   * Output only. The name of the AutomationRun initiated by a promote release
   * rule.
   *
   * @var string
   */
  public $promoteAutomationRun;
  /**
   * Output only. The names of the AutomationRuns initiated by a repair rollout
   * rule.
   *
   * @var string[]
   */
  public $repairAutomationRuns;

  /**
   * Output only. The names of the AutomationRuns initiated by an advance
   * rollout rule.
   *
   * @param string[] $advanceAutomationRuns
   */
  public function setAdvanceAutomationRuns($advanceAutomationRuns)
  {
    $this->advanceAutomationRuns = $advanceAutomationRuns;
  }
  /**
   * @return string[]
   */
  public function getAdvanceAutomationRuns()
  {
    return $this->advanceAutomationRuns;
  }
  /**
   * Output only. The name of the AutomationRun initiated by a promote release
   * rule.
   *
   * @param string $promoteAutomationRun
   */
  public function setPromoteAutomationRun($promoteAutomationRun)
  {
    $this->promoteAutomationRun = $promoteAutomationRun;
  }
  /**
   * @return string
   */
  public function getPromoteAutomationRun()
  {
    return $this->promoteAutomationRun;
  }
  /**
   * Output only. The names of the AutomationRuns initiated by a repair rollout
   * rule.
   *
   * @param string[] $repairAutomationRuns
   */
  public function setRepairAutomationRuns($repairAutomationRuns)
  {
    $this->repairAutomationRuns = $repairAutomationRuns;
  }
  /**
   * @return string[]
   */
  public function getRepairAutomationRuns()
  {
    return $this->repairAutomationRuns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomationRolloutMetadata::class, 'Google_Service_CloudDeploy_AutomationRolloutMetadata');
