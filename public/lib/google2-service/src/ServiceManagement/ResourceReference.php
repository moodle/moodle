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

namespace Google\Service\ServiceManagement;

class ResourceReference extends \Google\Model
{
  /**
   * The resource type of a child collection that the annotated field
   * references. This is useful for annotating the `parent` field that doesn't
   * have a fixed resource type. Example: message ListLogEntriesRequest { string
   * parent = 1 [(google.api.resource_reference) = { child_type:
   * "logging.googleapis.com/LogEntry" }; }
   *
   * @var string
   */
  public $childType;
  /**
   * The resource type that the annotated field references. Example: message
   * Subscription { string topic = 2 [(google.api.resource_reference) = { type:
   * "pubsub.googleapis.com/Topic" }]; } Occasionally, a field may reference an
   * arbitrary resource. In this case, APIs use the special value * in their
   * resource reference. Example: message GetIamPolicyRequest { string resource
   * = 2 [(google.api.resource_reference) = { type: "*" }]; }
   *
   * @var string
   */
  public $type;

  /**
   * The resource type of a child collection that the annotated field
   * references. This is useful for annotating the `parent` field that doesn't
   * have a fixed resource type. Example: message ListLogEntriesRequest { string
   * parent = 1 [(google.api.resource_reference) = { child_type:
   * "logging.googleapis.com/LogEntry" }; }
   *
   * @param string $childType
   */
  public function setChildType($childType)
  {
    $this->childType = $childType;
  }
  /**
   * @return string
   */
  public function getChildType()
  {
    return $this->childType;
  }
  /**
   * The resource type that the annotated field references. Example: message
   * Subscription { string topic = 2 [(google.api.resource_reference) = { type:
   * "pubsub.googleapis.com/Topic" }]; } Occasionally, a field may reference an
   * arbitrary resource. In this case, APIs use the special value * in their
   * resource reference. Example: message GetIamPolicyRequest { string resource
   * = 2 [(google.api.resource_reference) = { type: "*" }]; }
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceReference::class, 'Google_Service_ServiceManagement_ResourceReference');
