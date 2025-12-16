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

class SoftwareConfig extends \Google\Collection
{
  protected $collection_key = 'optionalComponents';
  /**
   * Optional. The version of software inside the cluster. It must be one of the
   * supported Dataproc Versions
   * (https://cloud.google.com/dataproc/docs/concepts/versioning/dataproc-
   * versions#supported-dataproc-image-versions), such as "1.2" (including a
   * subminor version, such as "1.2.29"), or the "preview" version
   * (https://cloud.google.com/dataproc/docs/concepts/versioning/dataproc-
   * versions#other_versions). If unspecified, it defaults to the latest Debian
   * version.
   *
   * @var string
   */
  public $imageVersion;
  /**
   * Optional. The set of components to activate on the cluster.
   *
   * @var string[]
   */
  public $optionalComponents;
  /**
   * Optional. The properties to set on daemon config files.Property keys are
   * specified in prefix:property format, for example core:hadoop.tmp.dir. The
   * following are supported prefixes and their mappings: capacity-scheduler:
   * capacity-scheduler.xml core: core-site.xml distcp: distcp-default.xml hdfs:
   * hdfs-site.xml hive: hive-site.xml mapred: mapred-site.xml pig:
   * pig.properties spark: spark-defaults.conf yarn: yarn-site.xmlFor more
   * information, see Cluster properties
   * (https://cloud.google.com/dataproc/docs/concepts/cluster-properties).
   *
   * @var string[]
   */
  public $properties;

  /**
   * Optional. The version of software inside the cluster. It must be one of the
   * supported Dataproc Versions
   * (https://cloud.google.com/dataproc/docs/concepts/versioning/dataproc-
   * versions#supported-dataproc-image-versions), such as "1.2" (including a
   * subminor version, such as "1.2.29"), or the "preview" version
   * (https://cloud.google.com/dataproc/docs/concepts/versioning/dataproc-
   * versions#other_versions). If unspecified, it defaults to the latest Debian
   * version.
   *
   * @param string $imageVersion
   */
  public function setImageVersion($imageVersion)
  {
    $this->imageVersion = $imageVersion;
  }
  /**
   * @return string
   */
  public function getImageVersion()
  {
    return $this->imageVersion;
  }
  /**
   * Optional. The set of components to activate on the cluster.
   *
   * @param string[] $optionalComponents
   */
  public function setOptionalComponents($optionalComponents)
  {
    $this->optionalComponents = $optionalComponents;
  }
  /**
   * @return string[]
   */
  public function getOptionalComponents()
  {
    return $this->optionalComponents;
  }
  /**
   * Optional. The properties to set on daemon config files.Property keys are
   * specified in prefix:property format, for example core:hadoop.tmp.dir. The
   * following are supported prefixes and their mappings: capacity-scheduler:
   * capacity-scheduler.xml core: core-site.xml distcp: distcp-default.xml hdfs:
   * hdfs-site.xml hive: hive-site.xml mapred: mapred-site.xml pig:
   * pig.properties spark: spark-defaults.conf yarn: yarn-site.xmlFor more
   * information, see Cluster properties
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
class_alias(SoftwareConfig::class, 'Google_Service_Dataproc_SoftwareConfig');
