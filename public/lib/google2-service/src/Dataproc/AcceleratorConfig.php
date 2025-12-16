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

class AcceleratorConfig extends \Google\Model
{
  /**
   * The number of the accelerator cards of this type exposed to this instance.
   *
   * @var int
   */
  public $acceleratorCount;
  /**
   * Full URL, partial URI, or short name of the accelerator type resource to
   * expose to this instance. See Compute Engine AcceleratorTypes (https://cloud
   * .google.com/compute/docs/reference/v1/acceleratorTypes).Examples: https://w
   * ww.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]/accelerator
   * Types/nvidia-tesla-t4
   * projects/[project_id]/zones/[zone]/acceleratorTypes/nvidia-tesla-t4 nvidia-
   * tesla-t4Auto Zone Exception: If you are using the Dataproc Auto Zone
   * Placement (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/auto-zone#using_auto_zone_placement) feature, you must use the
   * short name of the accelerator type resource, for example, nvidia-tesla-t4.
   *
   * @var string
   */
  public $acceleratorTypeUri;

  /**
   * The number of the accelerator cards of this type exposed to this instance.
   *
   * @param int $acceleratorCount
   */
  public function setAcceleratorCount($acceleratorCount)
  {
    $this->acceleratorCount = $acceleratorCount;
  }
  /**
   * @return int
   */
  public function getAcceleratorCount()
  {
    return $this->acceleratorCount;
  }
  /**
   * Full URL, partial URI, or short name of the accelerator type resource to
   * expose to this instance. See Compute Engine AcceleratorTypes (https://cloud
   * .google.com/compute/docs/reference/v1/acceleratorTypes).Examples: https://w
   * ww.googleapis.com/compute/v1/projects/[project_id]/zones/[zone]/accelerator
   * Types/nvidia-tesla-t4
   * projects/[project_id]/zones/[zone]/acceleratorTypes/nvidia-tesla-t4 nvidia-
   * tesla-t4Auto Zone Exception: If you are using the Dataproc Auto Zone
   * Placement (https://cloud.google.com/dataproc/docs/concepts/configuring-
   * clusters/auto-zone#using_auto_zone_placement) feature, you must use the
   * short name of the accelerator type resource, for example, nvidia-tesla-t4.
   *
   * @param string $acceleratorTypeUri
   */
  public function setAcceleratorTypeUri($acceleratorTypeUri)
  {
    $this->acceleratorTypeUri = $acceleratorTypeUri;
  }
  /**
   * @return string
   */
  public function getAcceleratorTypeUri()
  {
    return $this->acceleratorTypeUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AcceleratorConfig::class, 'Google_Service_Dataproc_AcceleratorConfig');
