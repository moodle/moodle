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

class KubernetesSoftwareConfig extends \Google\Model
{
  /**
   * The components that should be installed in this Dataproc cluster. The key
   * must be a string from the KubernetesComponent enumeration. The value is the
   * version of the software to be installed. At least one entry must be
   * specified.
   *
   * @var string[]
   */
  public $componentVersion;
  /**
   * The properties to set on daemon config files.Property keys are specified in
   * prefix:property format, for example spark:spark.kubernetes.container.image.
   * The following are supported prefixes and their mappings: spark: spark-
   * defaults.confFor more information, see Cluster properties
   * (https://cloud.google.com/dataproc/docs/concepts/cluster-properties).
   *
   * @var string[]
   */
  public $properties;

  /**
   * The components that should be installed in this Dataproc cluster. The key
   * must be a string from the KubernetesComponent enumeration. The value is the
   * version of the software to be installed. At least one entry must be
   * specified.
   *
   * @param string[] $componentVersion
   */
  public function setComponentVersion($componentVersion)
  {
    $this->componentVersion = $componentVersion;
  }
  /**
   * @return string[]
   */
  public function getComponentVersion()
  {
    return $this->componentVersion;
  }
  /**
   * The properties to set on daemon config files.Property keys are specified in
   * prefix:property format, for example spark:spark.kubernetes.container.image.
   * The following are supported prefixes and their mappings: spark: spark-
   * defaults.confFor more information, see Cluster properties
   * (https://cloud.google.com/dataproc/docs/concepts/cluster-properties).
   *
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KubernetesSoftwareConfig::class, 'Google_Service_Dataproc_KubernetesSoftwareConfig');
