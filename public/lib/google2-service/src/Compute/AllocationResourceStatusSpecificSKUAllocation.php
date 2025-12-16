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

namespace Google\Service\Compute;

class AllocationResourceStatusSpecificSKUAllocation extends \Google\Model
{
  /**
   * ID of the instance template used to populate reservation properties.
   *
   * @var string
   */
  public $sourceInstanceTemplateId;
  /**
   * Per service utilization breakdown. The Key is the Google Cloud managed
   * service name.
   *
   * @var string[]
   */
  public $utilizations;

  /**
   * ID of the instance template used to populate reservation properties.
   *
   * @param string $sourceInstanceTemplateId
   */
  public function setSourceInstanceTemplateId($sourceInstanceTemplateId)
  {
    $this->sourceInstanceTemplateId = $sourceInstanceTemplateId;
  }
  /**
   * @return string
   */
  public function getSourceInstanceTemplateId()
  {
    return $this->sourceInstanceTemplateId;
  }
  /**
   * Per service utilization breakdown. The Key is the Google Cloud managed
   * service name.
   *
   * @param string[] $utilizations
   */
  public function setUtilizations($utilizations)
  {
    $this->utilizations = $utilizations;
  }
  /**
   * @return string[]
   */
  public function getUtilizations()
  {
    return $this->utilizations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationResourceStatusSpecificSKUAllocation::class, 'Google_Service_Compute_AllocationResourceStatusSpecificSKUAllocation');
