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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1PeripheralsReport extends \Google\Collection
{
  protected $collection_key = 'usbPeripheralReport';
  /**
   * Output only. Timestamp of when the report was collected.
   *
   * @var string
   */
  public $reportTime;
  protected $usbPeripheralReportType = GoogleChromeManagementV1UsbPeripheralReport::class;
  protected $usbPeripheralReportDataType = 'array';

  /**
   * Output only. Timestamp of when the report was collected.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Reports of all usb connected devices.
   *
   * @param GoogleChromeManagementV1UsbPeripheralReport[] $usbPeripheralReport
   */
  public function setUsbPeripheralReport($usbPeripheralReport)
  {
    $this->usbPeripheralReport = $usbPeripheralReport;
  }
  /**
   * @return GoogleChromeManagementV1UsbPeripheralReport[]
   */
  public function getUsbPeripheralReport()
  {
    return $this->usbPeripheralReport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1PeripheralsReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1PeripheralsReport');
