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

namespace Google\Service\Dfareporting;

class RuleBlock extends \Google\Collection
{
  protected $collection_key = 'fieldFilter';
  protected $fieldFilterType = FieldFilter::class;
  protected $fieldFilterDataType = 'array';

  /**
   * Optional. A list of non-auto field filters
   *
   * @param FieldFilter[] $fieldFilter
   */
  public function setFieldFilter($fieldFilter)
  {
    $this->fieldFilter = $fieldFilter;
  }
  /**
   * @return FieldFilter[]
   */
  public function getFieldFilter()
  {
    return $this->fieldFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuleBlock::class, 'Google_Service_Dfareporting_RuleBlock');
