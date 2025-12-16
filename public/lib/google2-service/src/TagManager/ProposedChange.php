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

namespace Google\Service\TagManager;

class ProposedChange extends \Google\Collection
{
  protected $collection_key = 'changes';
  protected $changesType = Entity::class;
  protected $changesDataType = 'array';

  /**
   * The list of workspace changes to be applied.
   *
   * @param Entity[] $changes
   */
  public function setChanges($changes)
  {
    $this->changes = $changes;
  }
  /**
   * @return Entity[]
   */
  public function getChanges()
  {
    return $this->changes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProposedChange::class, 'Google_Service_TagManager_ProposedChange');
