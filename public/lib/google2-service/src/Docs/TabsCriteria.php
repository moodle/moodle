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

namespace Google\Service\Docs;

class TabsCriteria extends \Google\Collection
{
  protected $collection_key = 'tabIds';
  /**
   * The list of tab IDs in which the request executes.
   *
   * @var string[]
   */
  public $tabIds;

  /**
   * The list of tab IDs in which the request executes.
   *
   * @param string[] $tabIds
   */
  public function setTabIds($tabIds)
  {
    $this->tabIds = $tabIds;
  }
  /**
   * @return string[]
   */
  public function getTabIds()
  {
    return $this->tabIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TabsCriteria::class, 'Google_Service_Docs_TabsCriteria');
