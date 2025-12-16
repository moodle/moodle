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

namespace Google\Service\ServiceDirectory;

class ServicedirectoryNamespace extends \Google\Model
{
  /**
   * Optional. Resource labels associated with this namespace. No more than 64
   * user labels can be associated with a given resource. Label keys and values
   * can be no longer than 63 characters.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The resource name for the namespace in the format
   * `projects/locations/namespaces`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The globally unique identifier of the namespace in the UUID4
   * format.
   *
   * @var string
   */
  public $uid;

  /**
   * Optional. Resource labels associated with this namespace. No more than 64
   * user labels can be associated with a given resource. Label keys and values
   * can be no longer than 63 characters.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. The resource name for the namespace in the format
   * `projects/locations/namespaces`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The globally unique identifier of the namespace in the UUID4
   * format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServicedirectoryNamespace::class, 'Google_Service_ServiceDirectory_ServicedirectoryNamespace');
