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

namespace Google\Service\Spanner;

class AsymmetricAutoscalingOption extends \Google\Model
{
  protected $overridesType = AutoscalingConfigOverrides::class;
  protected $overridesDataType = '';
  protected $replicaSelectionType = InstanceReplicaSelection::class;
  protected $replicaSelectionDataType = '';

  /**
   * Optional. Overrides applied to the top-level autoscaling configuration for
   * the selected replicas.
   *
   * @param AutoscalingConfigOverrides $overrides
   */
  public function setOverrides(AutoscalingConfigOverrides $overrides)
  {
    $this->overrides = $overrides;
  }
  /**
   * @return AutoscalingConfigOverrides
   */
  public function getOverrides()
  {
    return $this->overrides;
  }
  /**
   * Required. Selects the replicas to which this AsymmetricAutoscalingOption
   * applies. Only read-only replicas are supported.
   *
   * @param InstanceReplicaSelection $replicaSelection
   */
  public function setReplicaSelection(InstanceReplicaSelection $replicaSelection)
  {
    $this->replicaSelection = $replicaSelection;
  }
  /**
   * @return InstanceReplicaSelection
   */
  public function getReplicaSelection()
  {
    return $this->replicaSelection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AsymmetricAutoscalingOption::class, 'Google_Service_Spanner_AsymmetricAutoscalingOption');
