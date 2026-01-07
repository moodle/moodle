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

class GoogleChromeManagementV1PrinterReport extends \Google\Model
{
  /**
   * Number of chrome devices that have been used to send print jobs to the
   * specified printer.
   *
   * @var string
   */
  public $deviceCount;
  /**
   * Number of print jobs sent to the printer.
   *
   * @var string
   */
  public $jobCount;
  /**
   * Printer name.
   *
   * @var string
   */
  public $printer;
  /**
   * Printer API ID.
   *
   * @var string
   */
  public $printerId;
  /**
   * Printer model.
   *
   * @var string
   */
  public $printerModel;
  /**
   * Number of users that have sent print jobs to the printer.
   *
   * @var string
   */
  public $userCount;

  /**
   * Number of chrome devices that have been used to send print jobs to the
   * specified printer.
   *
   * @param string $deviceCount
   */
  public function setDeviceCount($deviceCount)
  {
    $this->deviceCount = $deviceCount;
  }
  /**
   * @return string
   */
  public function getDeviceCount()
  {
    return $this->deviceCount;
  }
  /**
   * Number of print jobs sent to the printer.
   *
   * @param string $jobCount
   */
  public function setJobCount($jobCount)
  {
    $this->jobCount = $jobCount;
  }
  /**
   * @return string
   */
  public function getJobCount()
  {
    return $this->jobCount;
  }
  /**
   * Printer name.
   *
   * @param string $printer
   */
  public function setPrinter($printer)
  {
    $this->printer = $printer;
  }
  /**
   * @return string
   */
  public function getPrinter()
  {
    return $this->printer;
  }
  /**
   * Printer API ID.
   *
   * @param string $printerId
   */
  public function setPrinterId($printerId)
  {
    $this->printerId = $printerId;
  }
  /**
   * @return string
   */
  public function getPrinterId()
  {
    return $this->printerId;
  }
  /**
   * Printer model.
   *
   * @param string $printerModel
   */
  public function setPrinterModel($printerModel)
  {
    $this->printerModel = $printerModel;
  }
  /**
   * @return string
   */
  public function getPrinterModel()
  {
    return $this->printerModel;
  }
  /**
   * Number of users that have sent print jobs to the printer.
   *
   * @param string $userCount
   */
  public function setUserCount($userCount)
  {
    $this->userCount = $userCount;
  }
  /**
   * @return string
   */
  public function getUserCount()
  {
    return $this->userCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1PrinterReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1PrinterReport');
