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

namespace Google\Service\SecurityPosture;

class Report extends \Google\Model
{
  /**
   * Output only. The time at which the report was created.
   *
   * @var string
   */
  public $createTime;
  protected $iacValidationReportType = IaCValidationReport::class;
  protected $iacValidationReportDataType = '';
  /**
   * Required. The name of the report, in the format
   * `organizations/{organization}/locations/global/reports/{report_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time at which the report was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the report was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. An infrastructure-as-code (IaC) validation report.
   *
   * @param IaCValidationReport $iacValidationReport
   */
  public function setIacValidationReport(IaCValidationReport $iacValidationReport)
  {
    $this->iacValidationReport = $iacValidationReport;
  }
  /**
   * @return IaCValidationReport
   */
  public function getIacValidationReport()
  {
    return $this->iacValidationReport;
  }
  /**
   * Required. The name of the report, in the format
   * `organizations/{organization}/locations/global/reports/{report_id}`.
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
   * Output only. The time at which the report was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Report::class, 'Google_Service_SecurityPosture_Report');
