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

class GoogleCloudDataplexV1EnvironmentInfrastructureSpecOsImageRuntime extends \Google\Collection
{
  protected $collection_key = 'pythonPackages';
  /**
   * Required. Dataplex Universal Catalog Image version.
   *
   * @var string
   */
  public $imageVersion;
  /**
   * Optional. List of Java jars to be included in the runtime environment.
   * Valid input includes Cloud Storage URIs to Jar binaries. For example,
   * gs://bucket-name/my/path/to/file.jar
   *
   * @var string[]
   */
  public $javaLibraries;
  /**
   * Optional. Spark properties to provide configuration for use in sessions
   * created for this environment. The properties to set on daemon config files.
   * Property keys are specified in prefix:property format. The prefix must be
   * "spark".
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
   * Required. Dataplex Universal Catalog Image version.
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
   * Optional. List of Java jars to be included in the runtime environment.
   * Valid input includes Cloud Storage URIs to Jar binaries. For example,
   * gs://bucket-name/my/path/to/file.jar
   *
   * @param string[] $javaLibraries
   */
  public function setJavaLibraries($javaLibraries)
  {
    $this->javaLibraries = $javaLibraries;
  }
  /**
   * @return string[]
   */
  public function getJavaLibraries()
  {
    return $this->javaLibraries;
  }
  /**
   * Optional. Spark properties to provide configuration for use in sessions
   * created for this environment. The properties to set on daemon config files.
   * Property keys are specified in prefix:property format. The prefix must be
   * "spark".
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
class_alias(GoogleCloudDataplexV1EnvironmentInfrastructureSpecOsImageRuntime::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1EnvironmentInfrastructureSpecOsImageRuntime');
