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

class SlsaRecipe extends \Google\Model
{
  /**
   * Collection of all external inputs that influenced the build on top of
   * recipe.definedInMaterial and recipe.entryPoint. For example, if the recipe
   * type were "make", then this might be the flags passed to make aside from
   * the target, which is captured in recipe.entryPoint. Depending on the recipe
   * Type, the structure may be different.
   *
   * @var array[]
   */
  public $arguments;
  /**
   * Index in materials containing the recipe steps that are not implied by
   * recipe.type. For example, if the recipe type were "make", then this would
   * point to the source containing the Makefile, not the make program itself.
   * Set to -1 if the recipe doesn't come from a material, as zero is default
   * unset value for int64.
   *
   * @var string
   */
  public $definedInMaterial;
  /**
   * String identifying the entry point into the build. This is often a path to
   * a configuration file and/or a target label within that file. The syntax and
   * meaning are defined by recipe.type. For example, if the recipe type were
   * "make", then this would reference the directory in which to run make as
   * well as which target to use.
   *
   * @var string
   */
  public $entryPoint;
  /**
   * Any other builder-controlled inputs necessary for correctly evaluating the
   * recipe. Usually only needed for reproducing the build but not evaluated as
   * part of policy. Depending on the recipe Type, the structure may be
   * different.
   *
   * @var array[]
   */
  public $environment;
  /**
   * URI indicating what type of recipe was performed. It determines the meaning
   * of recipe.entryPoint, recipe.arguments, recipe.environment, and materials.
   *
   * @var string
   */
  public $type;

  /**
   * Collection of all external inputs that influenced the build on top of
   * recipe.definedInMaterial and recipe.entryPoint. For example, if the recipe
   * type were "make", then this might be the flags passed to make aside from
   * the target, which is captured in recipe.entryPoint. Depending on the recipe
   * Type, the structure may be different.
   *
   * @param array[] $arguments
   */
  public function setArguments($arguments)
  {
    $this->arguments = $arguments;
  }
  /**
   * @return array[]
   */
  public function getArguments()
  {
    return $this->arguments;
  }
  /**
   * Index in materials containing the recipe steps that are not implied by
   * recipe.type. For example, if the recipe type were "make", then this would
   * point to the source containing the Makefile, not the make program itself.
   * Set to -1 if the recipe doesn't come from a material, as zero is default
   * unset value for int64.
   *
   * @param string $definedInMaterial
   */
  public function setDefinedInMaterial($definedInMaterial)
  {
    $this->definedInMaterial = $definedInMaterial;
  }
  /**
   * @return string
   */
  public function getDefinedInMaterial()
  {
    return $this->definedInMaterial;
  }
  /**
   * String identifying the entry point into the build. This is often a path to
   * a configuration file and/or a target label within that file. The syntax and
   * meaning are defined by recipe.type. For example, if the recipe type were
   * "make", then this would reference the directory in which to run make as
   * well as which target to use.
   *
   * @param string $entryPoint
   */
  public function setEntryPoint($entryPoint)
  {
    $this->entryPoint = $entryPoint;
  }
  /**
   * @return string
   */
  public function getEntryPoint()
  {
    return $this->entryPoint;
  }
  /**
   * Any other builder-controlled inputs necessary for correctly evaluating the
   * recipe. Usually only needed for reproducing the build but not evaluated as
   * part of policy. Depending on the recipe Type, the structure may be
   * different.
   *
   * @param array[] $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return array[]
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * URI indicating what type of recipe was performed. It determines the meaning
   * of recipe.entryPoint, recipe.arguments, recipe.environment, and materials.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SlsaRecipe::class, 'Google_Service_OnDemandScanning_SlsaRecipe');
