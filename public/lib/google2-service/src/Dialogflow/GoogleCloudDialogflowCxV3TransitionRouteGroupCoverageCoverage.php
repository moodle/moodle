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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverage extends \Google\Collection
{
  protected $collection_key = 'transitions';
  /**
   * The percent of transition routes in the transition route group that are
   * covered.
   *
   * @var float
   */
  public $coverageScore;
  protected $routeGroupType = GoogleCloudDialogflowCxV3TransitionRouteGroup::class;
  protected $routeGroupDataType = '';
  protected $transitionsType = GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverageTransition::class;
  protected $transitionsDataType = 'array';

  /**
   * The percent of transition routes in the transition route group that are
   * covered.
   *
   * @param float $coverageScore
   */
  public function setCoverageScore($coverageScore)
  {
    $this->coverageScore = $coverageScore;
  }
  /**
   * @return float
   */
  public function getCoverageScore()
  {
    return $this->coverageScore;
  }
  /**
   * Transition route group metadata. Only name and displayName will be set.
   *
   * @param GoogleCloudDialogflowCxV3TransitionRouteGroup $routeGroup
   */
  public function setRouteGroup(GoogleCloudDialogflowCxV3TransitionRouteGroup $routeGroup)
  {
    $this->routeGroup = $routeGroup;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TransitionRouteGroup
   */
  public function getRouteGroup()
  {
    return $this->routeGroup;
  }
  /**
   * The list of transition routes and coverage in the transition route group.
   *
   * @param GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverageTransition[] $transitions
   */
  public function setTransitions($transitions)
  {
    $this->transitions = $transitions;
  }
  /**
   * @return GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverageTransition[]
   */
  public function getTransitions()
  {
    return $this->transitions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverage::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3TransitionRouteGroupCoverageCoverage');
