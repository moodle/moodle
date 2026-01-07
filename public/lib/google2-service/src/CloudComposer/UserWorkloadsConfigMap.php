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

namespace Google\Service\CloudComposer;

class UserWorkloadsConfigMap extends \Google\Model
{
  /**
   * Optional. The "data" field of Kubernetes ConfigMap, organized in key-value
   * pairs. For details see:
   * https://kubernetes.io/docs/concepts/configuration/configmap/ Example: {
   * "example_key": "example_value", "another_key": "another_value" }
   *
   * @var string[]
   */
  public $data;
  /**
   * Identifier. The resource name of the ConfigMap, in the form: "projects/{pro
   * jectId}/locations/{locationId}/environments/{environmentId}/userWorkloadsCo
   * nfigMaps/{userWorkloadsConfigMapId}"
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The "data" field of Kubernetes ConfigMap, organized in key-value
   * pairs. For details see:
   * https://kubernetes.io/docs/concepts/configuration/configmap/ Example: {
   * "example_key": "example_value", "another_key": "another_value" }
   *
   * @param string[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Identifier. The resource name of the ConfigMap, in the form: "projects/{pro
   * jectId}/locations/{locationId}/environments/{environmentId}/userWorkloadsCo
   * nfigMaps/{userWorkloadsConfigMapId}"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserWorkloadsConfigMap::class, 'Google_Service_CloudComposer_UserWorkloadsConfigMap');
