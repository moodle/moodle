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

namespace Google\Service\Sheets;

class ChartSourceRange extends \Google\Collection
{
  protected $collection_key = 'sources';
  protected $sourcesType = GridRange::class;
  protected $sourcesDataType = 'array';

  /**
   * The ranges of data for a series or domain. Exactly one dimension must have
   * a length of 1, and all sources in the list must have the same dimension
   * with length 1. The domain (if it exists) & all series must have the same
   * number of source ranges. If using more than one source range, then the
   * source range at a given offset must be in order and contiguous across the
   * domain and series. For example, these are valid configurations: domain
   * sources: A1:A5 series1 sources: B1:B5 series2 sources: D6:D10 domain
   * sources: A1:A5, C10:C12 series1 sources: B1:B5, D10:D12 series2 sources:
   * C1:C5, E10:E12
   *
   * @param GridRange[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return GridRange[]
   */
  public function getSources()
  {
    return $this->sources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChartSourceRange::class, 'Google_Service_Sheets_ChartSourceRange');
