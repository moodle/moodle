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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TaskInfrastructureSpecContainerImageRuntime extends \Google\Collection
{
  protected $collection_key = 'pythonPackages';
  /**
   * Optional. Container image to use.
   *
   * @var string
   */
  public $image;
  /**
   * Optional. A list of Java JARS to add to the classpath. Valid input includes
   * Cloud Storage URIs to Jar binaries. For example, gs://bucket-
   * name/my/path/to/file.jar
   *
   * @var string[]
   */
  public $javaJars;
  /**
   * Optional. Override to common configuration of open source components
   * installed on the Dataproc cluster. The properties to set on daemon config
   * files. Property keys are specified in prefix:property format, for example
   * core:hadoop.tmp.dir. For more information, see Cluster properties
   * (https://cloud.google.com/dataproc/docs/concepts/cluster-properties).
   *
   * @var string[]
   */
  public $properties;
  /**
   * Optional. A list of python packages to be installed. Valid formats include
   * Cloud Storage URI to a PIP installable library. For example, gs://bucket-
   * name/my/path/to/lib.tar.gz
   *
   * @var string[]
   */
  public $pythonPackages;

  /**
   * Optional. Container image to use.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Optional. A list of Java JARS to add to the classpath. Valid input includes
   * Cloud Storage URIs to Jar binaries. For example, gs://bucket-
   * name/my/path/to/file.jar
   *
   * @param string[] $javaJars
   */
  public function setJavaJars($javaJars)
  {
    $this->javaJars = $javaJars;
  }
  /**
   * @return string[]
   */
  public function getJavaJars()
  {
    return $this->javaJars;
  }
  /**
   * Optional. Override to common configuration of open source components
   * installed on the Dataproc cluster. The properties to set on daemon config
   * files. Property keys are specified in prefix:property format, for example
   * core:hadoop.tmp.dir. For more information, see Cluster properties
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
  /**
   * Optional. A list of python packages to be installed. Valid formats include
   * Cloud Storage URI to a PIP installable library. For example, gs://bucket-
   * name/my/path/to/lib.tar.gz
   *
   * @param string[] $pythonPackages
   */
  public function setPythonPackages($pythonPackages)
  {
    $this->pythonPackages = $pythonPackages;
  }
  /**
   * @return string[]
   */
  public function getPythonPackages()
  {
    return $this->pythonPackages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskInfrastructureSpecContainerImageRuntime::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskInfrastructureSpecContainerImageRuntime');
