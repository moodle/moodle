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

namespace Google\Service\Dataproc;

class ProvisioningModelMix extends \Google\Model
{
  /**
   * Optional. The base capacity that will always use Standard VMs to avoid risk
   * of more preemption than the minimum capacity you need. Dataproc will create
   * only standard VMs until it reaches standard_capacity_base, then it will
   * start using standard_capacity_percent_above_base to mix Spot with Standard
   * VMs. eg. If 15 instances are requested and standard_capacity_base is 5,
   * Dataproc will create 5 standard VMs and then start mixing spot and standard
   * VMs for remaining 10 instances.
   *
   * @var int
   */
  public $standardCapacityBase;
  /**
   * Optional. The percentage of target capacity that should use Standard VM.
   * The remaining percentage will use Spot VMs. The percentage applies only to
   * the capacity above standard_capacity_base. eg. If 15 instances are
   * requested and standard_capacity_base is 5 and
   * standard_capacity_percent_above_base is 30, Dataproc will create 5 standard
   * VMs and then start mixing spot and standard VMs for remaining 10 instances.
   * The mix will be 30% standard and 70% spot.
   *
   * @var int
   */
  public $standardCapacityPercentAboveBase;

  /**
   * Optional. The base capacity that will always use Standard VMs to avoid risk
   * of more preemption than the minimum capacity you need. Dataproc will create
   * only standard VMs until it reaches standard_capacity_base, then it will
   * start using standard_capacity_percent_above_base to mix Spot with Standard
   * VMs. eg. If 15 instances are requested and standard_capacity_base is 5,
   * Dataproc will create 5 standard VMs and then start mixing spot and standard
   * VMs for remaining 10 instances.
   *
   * @param int $standardCapacityBase
   */
  public function setStandardCapacityBase($standardCapacityBase)
  {
    $this->standardCapacityBase = $standardCapacityBase;
  }
  /**
   * @return int
   */
  public function getStandardCapacityBase()
  {
    return $this->standardCapacityBase;
  }
  /**
   * Optional. The percentage of target capacity that should use Standard VM.
   * The remaining percentage will use Spot VMs. The percentage applies only to
   * the capacity above standard_capacity_base. eg. If 15 instances are
   * requested and standard_capacity_base is 5 and
   * standard_capacity_percent_above_base is 30, Dataproc will create 5 standard
   * VMs and then start mixing spot and standard VMs for remaining 10 instances.
   * The mix will be 30% standard and 70% spot.
   *
   * @param int $standardCapacityPercentAboveBase
   */
  public function setStandardCapacityPercentAboveBase($standardCapacityPercentAboveBase)
  {
    $this->standardCapacityPercentAboveBase = $standardCapacityPercentAboveBase;
  }
  /**
   * @return int
   */
  public function getStandardCapacityPercentAboveBase()
  {
    return $this->standardCapacityPercentAboveBase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProvisioningModelMix::class, 'Google_Service_Dataproc_ProvisioningModelMix');
