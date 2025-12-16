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

class GoogleChromeManagementV1TelemetryDevice extends \Google\Collection
{
  protected $collection_key = 'thunderboltInfo';
  protected $appReportType = GoogleChromeManagementV1AppReport::class;
  protected $appReportDataType = 'array';
  protected $audioStatusReportType = GoogleChromeManagementV1AudioStatusReport::class;
  protected $audioStatusReportDataType = 'array';
  protected $batteryInfoType = GoogleChromeManagementV1BatteryInfo::class;
  protected $batteryInfoDataType = 'array';
  protected $batteryStatusReportType = GoogleChromeManagementV1BatteryStatusReport::class;
  protected $batteryStatusReportDataType = 'array';
  protected $bootPerformanceReportType = GoogleChromeManagementV1BootPerformanceReport::class;
  protected $bootPerformanceReportDataType = 'array';
  protected $cpuInfoType = GoogleChromeManagementV1CpuInfo::class;
  protected $cpuInfoDataType = 'array';
  protected $cpuStatusReportType = GoogleChromeManagementV1CpuStatusReport::class;
  protected $cpuStatusReportDataType = 'array';
  /**
   * Output only. Google Workspace Customer whose enterprise enrolled the
   * device.
   *
   * @var string
   */
  public $customer;
  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab
   *
   * @var string
   */
  public $deviceId;
  protected $graphicsInfoType = GoogleChromeManagementV1GraphicsInfo::class;
  protected $graphicsInfoDataType = '';
  protected $graphicsStatusReportType = GoogleChromeManagementV1GraphicsStatusReport::class;
  protected $graphicsStatusReportDataType = 'array';
  protected $heartbeatStatusReportType = GoogleChromeManagementV1HeartbeatStatusReport::class;
  protected $heartbeatStatusReportDataType = 'array';
  protected $kioskAppStatusReportType = GoogleChromeManagementV1KioskAppStatusReport::class;
  protected $kioskAppStatusReportDataType = 'array';
  protected $memoryInfoType = GoogleChromeManagementV1MemoryInfo::class;
  protected $memoryInfoDataType = '';
  protected $memoryStatusReportType = GoogleChromeManagementV1MemoryStatusReport::class;
  protected $memoryStatusReportDataType = 'array';
  /**
   * Output only. Resource name of the device.
   *
   * @var string
   */
  public $name;
  protected $networkBandwidthReportType = GoogleChromeManagementV1NetworkBandwidthReport::class;
  protected $networkBandwidthReportDataType = 'array';
  protected $networkDiagnosticsReportType = GoogleChromeManagementV1NetworkDiagnosticsReport::class;
  protected $networkDiagnosticsReportDataType = 'array';
  protected $networkInfoType = GoogleChromeManagementV1NetworkInfo::class;
  protected $networkInfoDataType = '';
  protected $networkStatusReportType = GoogleChromeManagementV1NetworkStatusReport::class;
  protected $networkStatusReportDataType = 'array';
  /**
   * Output only. Organization unit ID of the device.
   *
   * @var string
   */
  public $orgUnitId;
  protected $osUpdateStatusType = GoogleChromeManagementV1OsUpdateStatus::class;
  protected $osUpdateStatusDataType = 'array';
  protected $peripheralsReportType = GoogleChromeManagementV1PeripheralsReport::class;
  protected $peripheralsReportDataType = 'array';
  protected $runtimeCountersReportType = GoogleChromeManagementV1RuntimeCountersReport::class;
  protected $runtimeCountersReportDataType = 'array';
  /**
   * Output only. Device serial number. This value is the same as the Admin
   * Console's Serial Number in the ChromeOS Devices tab.
   *
   * @var string
   */
  public $serialNumber;
  protected $storageInfoType = GoogleChromeManagementV1StorageInfo::class;
  protected $storageInfoDataType = '';
  protected $storageStatusReportType = GoogleChromeManagementV1StorageStatusReport::class;
  protected $storageStatusReportDataType = 'array';
  protected $thunderboltInfoType = GoogleChromeManagementV1ThunderboltInfo::class;
  protected $thunderboltInfoDataType = 'array';

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
   * Output only. Information on battery specs for the device.
   *
   * @param GoogleChromeManagementV1BatteryInfo[] $batteryInfo
   */
  public function setBatteryInfo($batteryInfo)
  {
    $this->batteryInfo = $batteryInfo;
  }
  /**
   * @return GoogleChromeManagementV1BatteryInfo[]
   */
  public function getBatteryInfo()
  {
    return $this->batteryInfo;
  }
  /**
   * Output only. Battery reports collected periodically.
   *
   * @param GoogleChromeManagementV1BatteryStatusReport[] $batteryStatusReport
   */
  public function setBatteryStatusReport($batteryStatusReport)
  {
    $this->batteryStatusReport = $batteryStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1BatteryStatusReport[]
   */
  public function getBatteryStatusReport()
  {
    return $this->batteryStatusReport;
  }
  /**
   * Output only. Boot performance reports of the device.
   *
   * @param GoogleChromeManagementV1BootPerformanceReport[] $bootPerformanceReport
   */
  public function setBootPerformanceReport($bootPerformanceReport)
  {
    $this->bootPerformanceReport = $bootPerformanceReport;
  }
  /**
   * @return GoogleChromeManagementV1BootPerformanceReport[]
   */
  public function getBootPerformanceReport()
  {
    return $this->bootPerformanceReport;
  }
  /**
   * Output only. Information regarding CPU specs for the device.
   *
   * @param GoogleChromeManagementV1CpuInfo[] $cpuInfo
   */
  public function setCpuInfo($cpuInfo)
  {
    $this->cpuInfo = $cpuInfo;
  }
  /**
   * @return GoogleChromeManagementV1CpuInfo[]
   */
  public function getCpuInfo()
  {
    return $this->cpuInfo;
  }
  /**
   * Output only. CPU status reports collected periodically sorted in a
   * decreasing order of report_time.
   *
   * @param GoogleChromeManagementV1CpuStatusReport[] $cpuStatusReport
   */
  public function setCpuStatusReport($cpuStatusReport)
  {
    $this->cpuStatusReport = $cpuStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1CpuStatusReport[]
   */
  public function getCpuStatusReport()
  {
    return $this->cpuStatusReport;
  }
  /**
   * Output only. Google Workspace Customer whose enterprise enrolled the
   * device.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab
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
   * Output only. Contains information regarding Graphic peripherals for the
   * device.
   *
   * @param GoogleChromeManagementV1GraphicsInfo $graphicsInfo
   */
  public function setGraphicsInfo(GoogleChromeManagementV1GraphicsInfo $graphicsInfo)
  {
    $this->graphicsInfo = $graphicsInfo;
  }
  /**
   * @return GoogleChromeManagementV1GraphicsInfo
   */
  public function getGraphicsInfo()
  {
    return $this->graphicsInfo;
  }
  /**
   * Output only. Graphics reports collected periodically.
   *
   * @param GoogleChromeManagementV1GraphicsStatusReport[] $graphicsStatusReport
   */
  public function setGraphicsStatusReport($graphicsStatusReport)
  {
    $this->graphicsStatusReport = $graphicsStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1GraphicsStatusReport[]
   */
  public function getGraphicsStatusReport()
  {
    return $this->graphicsStatusReport;
  }
  /**
   * Output only. Heartbeat status report containing timestamps periodically
   * sorted in decreasing order of report_time
   *
   * @param GoogleChromeManagementV1HeartbeatStatusReport[] $heartbeatStatusReport
   */
  public function setHeartbeatStatusReport($heartbeatStatusReport)
  {
    $this->heartbeatStatusReport = $heartbeatStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1HeartbeatStatusReport[]
   */
  public function getHeartbeatStatusReport()
  {
    return $this->heartbeatStatusReport;
  }
  /**
   * Output only. Kiosk app status report for the kiosk device
   *
   * @param GoogleChromeManagementV1KioskAppStatusReport[] $kioskAppStatusReport
   */
  public function setKioskAppStatusReport($kioskAppStatusReport)
  {
    $this->kioskAppStatusReport = $kioskAppStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1KioskAppStatusReport[]
   */
  public function getKioskAppStatusReport()
  {
    return $this->kioskAppStatusReport;
  }
  /**
   * Output only. Information regarding memory specs for the device.
   *
   * @param GoogleChromeManagementV1MemoryInfo $memoryInfo
   */
  public function setMemoryInfo(GoogleChromeManagementV1MemoryInfo $memoryInfo)
  {
    $this->memoryInfo = $memoryInfo;
  }
  /**
   * @return GoogleChromeManagementV1MemoryInfo
   */
  public function getMemoryInfo()
  {
    return $this->memoryInfo;
  }
  /**
   * Output only. Memory status reports collected periodically sorted decreasing
   * by report_time.
   *
   * @param GoogleChromeManagementV1MemoryStatusReport[] $memoryStatusReport
   */
  public function setMemoryStatusReport($memoryStatusReport)
  {
    $this->memoryStatusReport = $memoryStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1MemoryStatusReport[]
   */
  public function getMemoryStatusReport()
  {
    return $this->memoryStatusReport;
  }
  /**
   * Output only. Resource name of the device.
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
   * Output only. Network diagnostics collected periodically.
   *
   * @param GoogleChromeManagementV1NetworkDiagnosticsReport[] $networkDiagnosticsReport
   */
  public function setNetworkDiagnosticsReport($networkDiagnosticsReport)
  {
    $this->networkDiagnosticsReport = $networkDiagnosticsReport;
  }
  /**
   * @return GoogleChromeManagementV1NetworkDiagnosticsReport[]
   */
  public function getNetworkDiagnosticsReport()
  {
    return $this->networkDiagnosticsReport;
  }
  /**
   * Output only. Network devices information.
   *
   * @param GoogleChromeManagementV1NetworkInfo $networkInfo
   */
  public function setNetworkInfo(GoogleChromeManagementV1NetworkInfo $networkInfo)
  {
    $this->networkInfo = $networkInfo;
  }
  /**
   * @return GoogleChromeManagementV1NetworkInfo
   */
  public function getNetworkInfo()
  {
    return $this->networkInfo;
  }
  /**
   * Output only. Network specs collected periodically.
   *
   * @param GoogleChromeManagementV1NetworkStatusReport[] $networkStatusReport
   */
  public function setNetworkStatusReport($networkStatusReport)
  {
    $this->networkStatusReport = $networkStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1NetworkStatusReport[]
   */
  public function getNetworkStatusReport()
  {
    return $this->networkStatusReport;
  }
  /**
   * Output only. Organization unit ID of the device.
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * Output only. Contains relevant information regarding ChromeOS update
   * status.
   *
   * @param GoogleChromeManagementV1OsUpdateStatus[] $osUpdateStatus
   */
  public function setOsUpdateStatus($osUpdateStatus)
  {
    $this->osUpdateStatus = $osUpdateStatus;
  }
  /**
   * @return GoogleChromeManagementV1OsUpdateStatus[]
   */
  public function getOsUpdateStatus()
  {
    return $this->osUpdateStatus;
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
  /**
   * Output only. Runtime counters reports collected device lifetime runtime, as
   * well as the counts of S0->S3, S0->S4, and S0->S5 transitions, meaning
   * entering into sleep, hibernation, and power-off states
   *
   * @param GoogleChromeManagementV1RuntimeCountersReport[] $runtimeCountersReport
   */
  public function setRuntimeCountersReport($runtimeCountersReport)
  {
    $this->runtimeCountersReport = $runtimeCountersReport;
  }
  /**
   * @return GoogleChromeManagementV1RuntimeCountersReport[]
   */
  public function getRuntimeCountersReport()
  {
    return $this->runtimeCountersReport;
  }
  /**
   * Output only. Device serial number. This value is the same as the Admin
   * Console's Serial Number in the ChromeOS Devices tab.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * Output only. Information of storage specs for the device.
   *
   * @param GoogleChromeManagementV1StorageInfo $storageInfo
   */
  public function setStorageInfo(GoogleChromeManagementV1StorageInfo $storageInfo)
  {
    $this->storageInfo = $storageInfo;
  }
  /**
   * @return GoogleChromeManagementV1StorageInfo
   */
  public function getStorageInfo()
  {
    return $this->storageInfo;
  }
  /**
   * Output only. Storage reports collected periodically.
   *
   * @param GoogleChromeManagementV1StorageStatusReport[] $storageStatusReport
   */
  public function setStorageStatusReport($storageStatusReport)
  {
    $this->storageStatusReport = $storageStatusReport;
  }
  /**
   * @return GoogleChromeManagementV1StorageStatusReport[]
   */
  public function getStorageStatusReport()
  {
    return $this->storageStatusReport;
  }
  /**
   * Output only. Information on Thunderbolt bus.
   *
   * @param GoogleChromeManagementV1ThunderboltInfo[] $thunderboltInfo
   */
  public function setThunderboltInfo($thunderboltInfo)
  {
    $this->thunderboltInfo = $thunderboltInfo;
  }
  /**
   * @return GoogleChromeManagementV1ThunderboltInfo[]
   */
  public function getThunderboltInfo()
  {
    return $this->thunderboltInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryDevice');
