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

namespace Google\Service\CloudKMS;

class ShowEffectiveAutokeyConfigResponse extends \Google\Model
{
  /**
   * Name of the key project configured in the resource project's folder
   * ancestry.
   *
   * @var string
   */
  public $keyProject;

  /**
   * Name of the key project configured in the resource project's folder
   * ancestry.
   *
   * @param string $keyProject
   */
  public function setKeyProject($keyProject)
  {
    $this->keyProject = $keyProject;
  }
  /**
   * @return string
   */
  public function getKeyProject()
  {
    return $this->keyProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShowEffectiveAutokeyConfigResponse::class, 'Google_Service_CloudKMS_ShowEffectiveAutokeyConfigResponse');
