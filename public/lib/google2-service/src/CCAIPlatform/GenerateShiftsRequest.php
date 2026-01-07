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

namespace Google\Service\CCAIPlatform;

class GenerateShiftsRequest extends \Google\Collection
{
  protected $collection_key = 'shiftTemplates';
  protected $employeeInfoType = EmployeeInfo::class;
  protected $employeeInfoDataType = 'array';
  protected $planningHorizonType = PlanningHorizon::class;
  protected $planningHorizonDataType = '';
  protected $shiftTemplatesType = ShiftTemplate::class;
  protected $shiftTemplatesDataType = 'array';
  protected $solverConfigType = SolverConfig::class;
  protected $solverConfigDataType = '';
  protected $workforceDemandsType = WorkforceDemandList::class;
  protected $workforceDemandsDataType = '';

  /**
   * Optional. Employee information that should be considered when generating
   * shifts.
   *
   * @param EmployeeInfo[] $employeeInfo
   */
  public function setEmployeeInfo($employeeInfo)
  {
    $this->employeeInfo = $employeeInfo;
  }
  /**
   * @return EmployeeInfo[]
   */
  public function getEmployeeInfo()
  {
    return $this->employeeInfo;
  }
  /**
   * Required. The solver will generate the maximum number of shifts per shift
   * template.
   *
   * @param PlanningHorizon $planningHorizon
   */
  public function setPlanningHorizon(PlanningHorizon $planningHorizon)
  {
    $this->planningHorizon = $planningHorizon;
  }
  /**
   * @return PlanningHorizon
   */
  public function getPlanningHorizon()
  {
    return $this->planningHorizon;
  }
  /**
   * Required. Set of shift templates specifying rules for generating shifts. A
   * shift template can be used for generating multiple shifts.
   *
   * @param ShiftTemplate[] $shiftTemplates
   */
  public function setShiftTemplates($shiftTemplates)
  {
    $this->shiftTemplates = $shiftTemplates;
  }
  /**
   * @return ShiftTemplate[]
   */
  public function getShiftTemplates()
  {
    return $this->shiftTemplates;
  }
  /**
   * Optional. Parameters for the solver.
   *
   * @param SolverConfig $solverConfig
   */
  public function setSolverConfig(SolverConfig $solverConfig)
  {
    $this->solverConfig = $solverConfig;
  }
  /**
   * @return SolverConfig
   */
  public function getSolverConfig()
  {
    return $this->solverConfig;
  }
  /**
   * Required. All the workforce demands that the generated shifts need to
   * cover. The planning horizon is defined between the earliest start time and
   * the latest end time across all the entries. This field cannot be empty.
   *
   * @param WorkforceDemandList $workforceDemands
   */
  public function setWorkforceDemands(WorkforceDemandList $workforceDemands)
  {
    $this->workforceDemands = $workforceDemands;
  }
  /**
   * @return WorkforceDemandList
   */
  public function getWorkforceDemands()
  {
    return $this->workforceDemands;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateShiftsRequest::class, 'Google_Service_CCAIPlatform_GenerateShiftsRequest');
