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

class BackendBucketParams extends \Google\Model
{
  /**
   * Tag keys/values directly bound to this resource. Tag keys and values have
   * the same definition as resource manager tags. The field is allowed for
   * INSERT only. The keys/values to set on the resource should be specified in
   * either ID { : } or Namespaced format { : }. For example the following are
   * valid inputs: * {"tagKeys/333" : "tagValues/444", "tagKeys/123" :
   * "tagValues/456"} * {"123/environment" : "production", "345/abc" : "xyz"}
   * Note: * Invalid combinations of ID & namespaced format is not supported.
   * For   instance: {"123/environment" : "tagValues/444"} is invalid.
   *
   * @var string[]
   */
  public $resourceManagerTags;

  /**
   * Tag keys/values directly bound to this resource. Tag keys and values have
   * the same definition as resource manager tags. The field is allowed for
   * INSERT only. The keys/values to set on the resource should be specified in
   * either ID { : } or Namespaced format { : }. For example the following are
   * valid inputs: * {"tagKeys/333" : "tagValues/444", "tagKeys/123" :
   * "tagValues/456"} * {"123/environment" : "production", "345/abc" : "xyz"}
   * Note: * Invalid combinations of ID & namespaced format is not supported.
   * For   instance: {"123/environment" : "tagValues/444"} is invalid.
   *
   * @param string[] $resourceManagerTags
   */
  public function setResourceManagerTags($resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return string[]
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendBucketParams::class, 'Google_Service_Compute_BackendBucketParams');
