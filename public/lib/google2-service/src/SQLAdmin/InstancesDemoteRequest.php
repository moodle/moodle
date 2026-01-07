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

namespace Google\Service\SQLAdmin;

class InstancesDemoteRequest extends \Google\Model
{
  protected $demoteContextType = DemoteContext::class;
  protected $demoteContextDataType = '';

  /**
   * Required. Contains details about the demote operation.
   *
   * @param DemoteContext $demoteContext
   */
  public function setDemoteContext(DemoteContext $demoteContext)
  {
    $this->demoteContext = $demoteContext;
  }
  /**
   * @return DemoteContext
   */
  public function getDemoteContext()
  {
    return $this->demoteContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesDemoteRequest::class, 'Google_Service_SQLAdmin_InstancesDemoteRequest');
