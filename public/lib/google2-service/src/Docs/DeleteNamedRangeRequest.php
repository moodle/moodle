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

class DeleteNamedRangeRequest extends \Google\Model
{
  /**
   * The name of the range(s) to delete. All named ranges with the given name
   * will be deleted.
   *
   * @var string
   */
  public $name;
  /**
   * The ID of the named range to delete.
   *
   * @var string
   */
  public $namedRangeId;
  protected $tabsCriteriaType = TabsCriteria::class;
  protected $tabsCriteriaDataType = '';

  /**
   * The name of the range(s) to delete. All named ranges with the given name
   * will be deleted.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The ID of the named range to delete.
   *
   * @param string $namedRangeId
   */
  public function setNamedRangeId($namedRangeId)
  {
    $this->namedRangeId = $namedRangeId;
  }
  /**
   * @return string
   */
  public function getNamedRangeId()
  {
    return $this->namedRangeId;
  }
  /**
   * Optional. The criteria used to specify which tab(s) the range deletion
   * should occur in. When omitted, the range deletion is applied to all tabs.
   * In a document containing a single tab: - If provided, must match the
   * singular tab's ID. - If omitted, the range deletion applies to the singular
   * tab. In a document containing multiple tabs: - If provided, the range
   * deletion applies to the specified tabs. - If not provided, the range
   * deletion applies to all tabs.
   *
   * @param TabsCriteria $tabsCriteria
   */
  public function setTabsCriteria(TabsCriteria $tabsCriteria)
  {
    $this->tabsCriteria = $tabsCriteria;
  }
  /**
   * @return TabsCriteria
   */
  public function getTabsCriteria()
  {
    return $this->tabsCriteria;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeleteNamedRangeRequest::class, 'Google_Service_Docs_DeleteNamedRangeRequest');
