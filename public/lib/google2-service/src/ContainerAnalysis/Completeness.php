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

class Completeness extends \Google\Model
{
  /**
   * If true, the builder claims that recipe.arguments is complete, meaning that
   * all external inputs are properly captured in the recipe.
   *
   * @var bool
   */
  public $arguments;
  /**
   * If true, the builder claims that recipe.environment is claimed to be
   * complete.
   *
   * @var bool
   */
  public $environment;
  /**
   * If true, the builder claims that materials are complete, usually through
   * some controls to prevent network access. Sometimes called "hermetic".
   *
   * @var bool
   */
  public $materials;

  /**
   * If true, the builder claims that recipe.arguments is complete, meaning that
   * all external inputs are properly captured in the recipe.
   *
   * @param bool $arguments
   */
  public function setArguments($arguments)
  {
    $this->arguments = $arguments;
  }
  /**
   * @return bool
   */
  public function getArguments()
  {
    return $this->arguments;
  }
  /**
   * If true, the builder claims that recipe.environment is claimed to be
   * complete.
   *
   * @param bool $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return bool
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * If true, the builder claims that materials are complete, usually through
   * some controls to prevent network access. Sometimes called "hermetic".
   *
   * @param bool $materials
   */
  public function setMaterials($materials)
  {
    $this->materials = $materials;
  }
  /**
   * @return bool
   */
  public function getMaterials()
  {
    return $this->materials;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Completeness::class, 'Google_Service_ContainerAnalysis_Completeness');
