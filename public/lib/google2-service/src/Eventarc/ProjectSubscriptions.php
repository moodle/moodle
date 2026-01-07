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

namespace Google\Service\Eventarc;

class ProjectSubscriptions extends \Google\Collection
{
  protected $collection_key = 'list';
  /**
   * Required. A list of projects to receive events from. All the projects must
   * be in the same org. The listed projects should have the format
   * project/{identifier} where identifier can be either the project id for
   * project number. A single list may contain both formats. At most 100
   * projects can be listed.
   *
   * @var string[]
   */
  public $list;

  /**
   * Required. A list of projects to receive events from. All the projects must
   * be in the same org. The listed projects should have the format
   * project/{identifier} where identifier can be either the project id for
   * project number. A single list may contain both formats. At most 100
   * projects can be listed.
   *
   * @param string[] $list
   */
  public function setList($list)
  {
    $this->list = $list;
  }
  /**
   * @return string[]
   */
  public function getList()
  {
    return $this->list;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectSubscriptions::class, 'Google_Service_Eventarc_ProjectSubscriptions');
