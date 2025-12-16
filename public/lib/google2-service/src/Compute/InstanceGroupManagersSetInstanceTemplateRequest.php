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

class InstanceGroupManagersSetInstanceTemplateRequest extends \Google\Model
{
  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create all new instances in
   * the managed instance group. The templates for existing instances in the
   * group do not change unless you run recreateInstances,
   * runapplyUpdatesToInstances, or set the group'supdatePolicy.type to
   * PROACTIVE.
   *
   * @var string
   */
  public $instanceTemplate;

  /**
   * The URL of the instance template that is specified for this managed
   * instance group. The group uses this template to create all new instances in
   * the managed instance group. The templates for existing instances in the
   * group do not change unless you run recreateInstances,
   * runapplyUpdatesToInstances, or set the group'supdatePolicy.type to
   * PROACTIVE.
   *
   * @param string $instanceTemplate
   */
  public function setInstanceTemplate($instanceTemplate)
  {
    $this->instanceTemplate = $instanceTemplate;
  }
  /**
   * @return string
   */
  public function getInstanceTemplate()
  {
    return $this->instanceTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagersSetInstanceTemplateRequest::class, 'Google_Service_Compute_InstanceGroupManagersSetInstanceTemplateRequest');
