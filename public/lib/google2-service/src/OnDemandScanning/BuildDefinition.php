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

namespace Google\Service\OnDemandScanning;

class BuildDefinition extends \Google\Collection
{
  protected $collection_key = 'resolvedDependencies';
  /**
   * @var string
   */
  public $buildType;
  /**
   * @var array[]
   */
  public $externalParameters;
  /**
   * @var array[]
   */
  public $internalParameters;
  protected $resolvedDependenciesType = ResourceDescriptor::class;
  protected $resolvedDependenciesDataType = 'array';

  /**
   * @param string $buildType
   */
  public function setBuildType($buildType)
  {
    $this->buildType = $buildType;
  }
  /**
   * @return string
   */
  public function getBuildType()
  {
    return $this->buildType;
  }
  /**
   * @param array[] $externalParameters
   */
  public function setExternalParameters($externalParameters)
  {
    $this->externalParameters = $externalParameters;
  }
  /**
   * @return array[]
   */
  public function getExternalParameters()
  {
    return $this->externalParameters;
  }
  /**
   * @param array[] $internalParameters
   */
  public function setInternalParameters($internalParameters)
  {
    $this->internalParameters = $internalParameters;
  }
  /**
   * @return array[]
   */
  public function getInternalParameters()
  {
    return $this->internalParameters;
  }
  /**
   * @param ResourceDescriptor[] $resolvedDependencies
   */
  public function setResolvedDependencies($resolvedDependencies)
  {
    $this->resolvedDependencies = $resolvedDependencies;
  }
  /**
   * @return ResourceDescriptor[]
   */
  public function getResolvedDependencies()
  {
    return $this->resolvedDependencies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildDefinition::class, 'Google_Service_OnDemandScanning_BuildDefinition');
