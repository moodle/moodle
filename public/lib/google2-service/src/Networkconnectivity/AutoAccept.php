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

namespace Google\Service\Networkconnectivity;

class AutoAccept extends \Google\Collection
{
  protected $collection_key = 'autoAcceptProjects';
  /**
   * Optional. A list of project ids or project numbers for which you want to
   * enable auto-accept. The auto-accept setting is applied to spokes being
   * created or updated in these projects.
   *
   * @var string[]
   */
  public $autoAcceptProjects;

  /**
   * Optional. A list of project ids or project numbers for which you want to
   * enable auto-accept. The auto-accept setting is applied to spokes being
   * created or updated in these projects.
   *
   * @param string[] $autoAcceptProjects
   */
  public function setAutoAcceptProjects($autoAcceptProjects)
  {
    $this->autoAcceptProjects = $autoAcceptProjects;
  }
  /**
   * @return string[]
   */
  public function getAutoAcceptProjects()
  {
    return $this->autoAcceptProjects;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoAccept::class, 'Google_Service_Networkconnectivity_AutoAccept');
