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

namespace Google\Service\Config;

class ExportDeploymentStatefileRequest extends \Google\Model
{
  /**
   * Optional. If this flag is set to true, the exported deployment state file
   * will be the draft state. This will enable the draft file to be validated
   * before copying it over to the working state on unlock.
   *
   * @var bool
   */
  public $draft;

  /**
   * Optional. If this flag is set to true, the exported deployment state file
   * will be the draft state. This will enable the draft file to be validated
   * before copying it over to the working state on unlock.
   *
   * @param bool $draft
   */
  public function setDraft($draft)
  {
    $this->draft = $draft;
  }
  /**
   * @return bool
   */
  public function getDraft()
  {
    return $this->draft;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportDeploymentStatefileRequest::class, 'Google_Service_Config_ExportDeploymentStatefileRequest');
