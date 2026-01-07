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

namespace Google\Service\CloudDeploy;

class CustomTargetSkaffoldActions extends \Google\Collection
{
  protected $collection_key = 'includeSkaffoldModules';
  /**
   * Required. The Skaffold custom action responsible for deploy operations.
   *
   * @var string
   */
  public $deployAction;
  protected $includeSkaffoldModulesType = SkaffoldModules::class;
  protected $includeSkaffoldModulesDataType = 'array';
  /**
   * Optional. The Skaffold custom action responsible for render operations. If
   * not provided then Cloud Deploy will perform the render operations via
   * `skaffold render`.
   *
   * @var string
   */
  public $renderAction;

  /**
   * Required. The Skaffold custom action responsible for deploy operations.
   *
   * @param string $deployAction
   */
  public function setDeployAction($deployAction)
  {
    $this->deployAction = $deployAction;
  }
  /**
   * @return string
   */
  public function getDeployAction()
  {
    return $this->deployAction;
  }
  /**
   * Optional. List of Skaffold modules Cloud Deploy will include in the
   * Skaffold Config as required before performing diagnose.
   *
   * @param SkaffoldModules[] $includeSkaffoldModules
   */
  public function setIncludeSkaffoldModules($includeSkaffoldModules)
  {
    $this->includeSkaffoldModules = $includeSkaffoldModules;
  }
  /**
   * @return SkaffoldModules[]
   */
  public function getIncludeSkaffoldModules()
  {
    return $this->includeSkaffoldModules;
  }
  /**
   * Optional. The Skaffold custom action responsible for render operations. If
   * not provided then Cloud Deploy will perform the render operations via
   * `skaffold render`.
   *
   * @param string $renderAction
   */
  public function setRenderAction($renderAction)
  {
    $this->renderAction = $renderAction;
  }
  /**
   * @return string
   */
  public function getRenderAction()
  {
    return $this->renderAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomTargetSkaffoldActions::class, 'Google_Service_CloudDeploy_CustomTargetSkaffoldActions');
