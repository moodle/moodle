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

namespace Google\Service\AdMob;

class ReportFooter extends \Google\Collection
{
  protected $collection_key = 'warnings';
  /**
   * Total number of rows that matched the request. Warning: This count does NOT
   * always match the number of rows in the response. Do not make that
   * assumption when processing the response.
   *
   * @var string
   */
  public $matchingRowCount;
  protected $warningsType = ReportWarning::class;
  protected $warningsDataType = 'array';

  /**
   * Total number of rows that matched the request. Warning: This count does NOT
   * always match the number of rows in the response. Do not make that
   * assumption when processing the response.
   *
   * @param string $matchingRowCount
   */
  public function setMatchingRowCount($matchingRowCount)
  {
    $this->matchingRowCount = $matchingRowCount;
  }
  /**
   * @return string
   */
  public function getMatchingRowCount()
  {
    return $this->matchingRowCount;
  }
  /**
   * Warnings associated with generation of the report.
   *
   * @param ReportWarning[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return ReportWarning[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportFooter::class, 'Google_Service_AdMob_ReportFooter');
