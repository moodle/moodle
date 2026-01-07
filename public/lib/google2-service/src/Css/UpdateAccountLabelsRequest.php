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

namespace Google\Service\Css;

class UpdateAccountLabelsRequest extends \Google\Collection
{
  protected $collection_key = 'labelIds';
  /**
   * The list of label IDs to overwrite the existing account label IDs. If the
   * list is empty, all currently assigned label IDs will be deleted.
   *
   * @var string[]
   */
  public $labelIds;
  /**
   * Optional. Only required when updating MC account labels. The CSS domain
   * that is the parent resource of the MC account. Format: accounts/{account}
   *
   * @var string
   */
  public $parent;

  /**
   * The list of label IDs to overwrite the existing account label IDs. If the
   * list is empty, all currently assigned label IDs will be deleted.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * Optional. Only required when updating MC account labels. The CSS domain
   * that is the parent resource of the MC account. Format: accounts/{account}
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateAccountLabelsRequest::class, 'Google_Service_Css_UpdateAccountLabelsRequest');
