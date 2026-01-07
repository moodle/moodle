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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1Dependency extends \Google\Model
{
  /**
   * If set to true disable all dependency fetching (ignoring the default source
   * as well).
   *
   * @var bool
   */
  public $empty;
  protected $gitSourceType = ContaineranalysisGoogleDevtoolsCloudbuildV1DependencyGitSourceDependency::class;
  protected $gitSourceDataType = '';

  /**
   * If set to true disable all dependency fetching (ignoring the default source
   * as well).
   *
   * @param bool $empty
   */
  public function setEmpty($empty)
  {
    $this->empty = $empty;
  }
  /**
   * @return bool
   */
  public function getEmpty()
  {
    return $this->empty;
  }
  /**
   * Represents a git repository as a build dependency.
   *
   * @param ContaineranalysisGoogleDevtoolsCloudbuildV1DependencyGitSourceDependency $gitSource
   */
  public function setGitSource(ContaineranalysisGoogleDevtoolsCloudbuildV1DependencyGitSourceDependency $gitSource)
  {
    $this->gitSource = $gitSource;
  }
  /**
   * @return ContaineranalysisGoogleDevtoolsCloudbuildV1DependencyGitSourceDependency
   */
  public function getGitSource()
  {
    return $this->gitSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1Dependency::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1Dependency');
