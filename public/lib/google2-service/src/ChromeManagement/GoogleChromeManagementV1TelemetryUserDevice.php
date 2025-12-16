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

class GoogleChromeManagementV1TelemetryUserDevice extends \Google\Collection
{
  protected $collection_key = 'peripheralsReport';
  protected $appReportType = GoogleChromeManagementV1AppReport::class;
  protected $appReportDataType = 'array';
  protected $audioStatusReportType = GoogleChromeManagementV1AudioStatusReport::class;
  protected $audioStatusReportDataType = 'array';
  protected $deviceActivityReportType = GoogleChromeManagementV1DeviceActivityReport::class;
  protected $deviceActivityReportDataType = 'array';
  /**
   * The unique Directory API ID of the device. This value is the same as the
   * Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @var string
   */
  public $deviceId;
  protected $networkBandwidthReportType = GoogleChromeManagementV1NetworkBandwidthReport::class;
  protected $networkBandwidthReportDataType = 'array';
  protected $peripheralsReportType = GoogleChromeManagementV1PeripheralsReport::class;
  protected $peripheralsReportDataType = 'array';

  /**
   * Output only. App reports collected periodically sorted in a decreasing
   * order of report_time.
   *
   * @param GoogleChromeManagementV1AppReport[] $appReport
   */
  public function setAppReport($appReport)
  {
    $this->appReport = $appReport;
  }
  /**
   * @return GoogleChromeManagementV1AppReport[]
   */
  public function getAppReport()
  {
    return $this->appReport;
  }
  /**
   * Output only. Audio reports collected periodically sorted in a decreasing
   * order of report_time.
   *
   * @param GoogleChromeManagementV1AudioStatusReport[] $audioStatusReport
   */
  public function setAudioStatusReport($audioStatusReport)
  {
    $this->audioStatusReport = $audioStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1AudioStatusReport[]
   */
  public function getAudioStatusReport()
  {
    return $this->audioStatusReport;
  }
  /**
   * Output only. Device activity reports collected periodically sorted in a
   * decreasing order of report_time.
   *
   * @param GoogleChromeManagementV1DeviceActivityReport[] $deviceActivityReport
   */
  public function setDeviceActivityReport($deviceActivityReport)
  {
    $this->deviceActivityReport = $deviceActivityReport;
  }
  /**
   * @return GoogleChromeManagementV1DeviceActivityReport[]
   */
  public function getDeviceActivityReport()
  {
    return $this->deviceActivityReport;
  }
  /**
   * The unique Directory API ID of the device. This value is the same as the
   * Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Output only. Network bandwidth reports collected periodically sorted in a
   * decreasing order of report_time.
   *
   * @param GoogleChromeManagementV1NetworkBandwidthReport[] $networkBandwidthReport
   */
  public function setNetworkBandwidthReport($networkBandwidthReport)
  {
    $this->networkBandwidthReport = $networkBandwidthReport;
  }
  /**
   * @return GoogleChromeManagementV1NetworkBandwidthReport[]
   */
  public function getNetworkBandwidthReport()
  {
    return $this->networkBandwidthReport;
  }
  /**
   * Output only. Peripherals reports collected periodically sorted in a
   * decreasing order of report_time.
   *
   * @param GoogleChromeManagementV1PeripheralsReport[] $peripheralsReport
   */
  public function setPeripheralsReport($peripheralsReport)
  {
    $this->peripheralsReport = $peripheralsReport;
  }
  /**
   * @return GoogleChromeManagementV1PeripheralsReport[]
   */
  public function getPeripheralsReport()
  {
    return $this->peripheralsReport;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryUserDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryUserDevice');
