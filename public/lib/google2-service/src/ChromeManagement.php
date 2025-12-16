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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for ChromeManagement (v1).
 *
 * <p>
 * The Chrome Management API is a suite of services that allows Chrome
 * administrators to view, manage and gain insights on their Chrome OS and
 * Chrome Browser devices.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/chrome/management/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class ChromeManagement extends \Google\Service
{
  /** See detailed information about apps installed on Chrome browsers and devices managed by your organization. */
  const CHROME_MANAGEMENT_APPDETAILS_READONLY =
      "https://www.googleapis.com/auth/chrome.management.appdetails.readonly";
  /** See, edit, delete, and take other necessary actions on Chrome browser profiles managed by your organization. */
  const CHROME_MANAGEMENT_PROFILES =
      "https://www.googleapis.com/auth/chrome.management.profiles";
  /** See Chrome browser profiles managed by your organization. */
  const CHROME_MANAGEMENT_PROFILES_READONLY =
      "https://www.googleapis.com/auth/chrome.management.profiles.readonly";
  /** See reports about devices and Chrome browsers managed within your organization. */
  const CHROME_MANAGEMENT_REPORTS_READONLY =
      "https://www.googleapis.com/auth/chrome.management.reports.readonly";
  /** See basic device and telemetry information collected from ChromeOS devices or users managed within your organization. */
  const CHROME_MANAGEMENT_TELEMETRY_READONLY =
      "https://www.googleapis.com/auth/chrome.management.telemetry.readonly";

  public $customers_apps;
  public $customers_apps_android;
  public $customers_apps_chrome;
  public $customers_apps_web;
  public $customers_certificateProvisioningProcesses;
  public $customers_certificateProvisioningProcesses_operations;
  public $customers_profiles;
  public $customers_profiles_commands;
  public $customers_reports;
  public $customers_telemetry_devices;
  public $customers_telemetry_events;
  public $customers_telemetry_notificationConfigs;
  public $customers_telemetry_users;
  public $customers_thirdPartyProfileUsers;
  public $operations;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the ChromeManagement service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://chromemanagement.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://chromemanagement.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'chromemanagement';

    $this->customers_apps = new ChromeManagement\Resource\CustomersApps(
        $this,
        $this->serviceName,
        'apps',
        [
          'methods' => [
            'countChromeAppRequests' => [
              'path' => 'v1/{+customer}/apps:countChromeAppRequests',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'fetchDevicesRequestingExtension' => [
              'path' => 'v1/{+customer}/apps:fetchDevicesRequestingExtension',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'extensionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'fetchUsersRequestingExtension' => [
              'path' => 'v1/{+customer}/apps:fetchUsersRequestingExtension',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'extensionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_apps_android = new ChromeManagement\Resource\CustomersAppsAndroid(
        $this,
        $this->serviceName,
        'android',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_apps_chrome = new ChromeManagement\Resource\CustomersAppsChrome(
        $this,
        $this->serviceName,
        'chrome',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_apps_web = new ChromeManagement\Resource\CustomersAppsWeb(
        $this,
        $this->serviceName,
        'web',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_certificateProvisioningProcesses = new ChromeManagement\Resource\CustomersCertificateProvisioningProcesses(
        $this,
        $this->serviceName,
        'certificateProvisioningProcesses',
        [
          'methods' => [
            'claim' => [
              'path' => 'v1/{+name}:claim',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'setFailure' => [
              'path' => 'v1/{+name}:setFailure',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'signData' => [
              'path' => 'v1/{+name}:signData',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'uploadCertificate' => [
              'path' => 'v1/{+name}:uploadCertificate',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_certificateProvisioningProcesses_operations = new ChromeManagement\Resource\CustomersCertificateProvisioningProcessesOperations(
        $this,
        $this->serviceName,
        'operations',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_profiles = new ChromeManagement\Resource\CustomersProfiles(
        $this,
        $this->serviceName,
        'profiles',
        [
          'methods' => [
            'delete' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/profiles',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_profiles_commands = new ChromeManagement\Resource\CustomersProfilesCommands(
        $this,
        $this->serviceName,
        'commands',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/{+parent}/commands',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/commands',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_reports = new ChromeManagement\Resource\CustomersReports(
        $this,
        $this->serviceName,
        'reports',
        [
          'methods' => [
            'countActiveDevices' => [
              'path' => 'v1/{+customer}/reports:countActiveDevices',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'date.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.year' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
              ],
            ],'countChromeBrowsersNeedingAttention' => [
              'path' => 'v1/{+customer}/reports:countChromeBrowsersNeedingAttention',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countChromeCrashEvents' => [
              'path' => 'v1/{+customer}/reports:countChromeCrashEvents',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countChromeDevicesReachingAutoExpirationDate' => [
              'path' => 'v1/{+customer}/reports:countChromeDevicesReachingAutoExpirationDate',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'maxAueDate' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'minAueDate' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countChromeDevicesThatNeedAttention' => [
              'path' => 'v1/{+customer}/reports:countChromeDevicesThatNeedAttention',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countChromeHardwareFleetDevices' => [
              'path' => 'v1/{+customer}/reports:countChromeHardwareFleetDevices',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countChromeVersions' => [
              'path' => 'v1/{+customer}/reports:countChromeVersions',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countDevicesPerBootType' => [
              'path' => 'v1/{+customer}/reports:countDevicesPerBootType',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'date.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.year' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
              ],
            ],'countDevicesPerReleaseChannel' => [
              'path' => 'v1/{+customer}/reports:countDevicesPerReleaseChannel',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'date.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'date.year' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
              ],
            ],'countInstalledApps' => [
              'path' => 'v1/{+customer}/reports:countInstalledApps',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countPrintJobsByPrinter' => [
              'path' => 'v1/{+customer}/reports:countPrintJobsByPrinter',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'printerOrgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'countPrintJobsByUser' => [
              'path' => 'v1/{+customer}/reports:countPrintJobsByUser',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'printerOrgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'enumeratePrintJobs' => [
              'path' => 'v1/{+customer}/reports:enumeratePrintJobs',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'printerOrgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'findInstalledAppDevices' => [
              'path' => 'v1/{+customer}/reports:findInstalledAppDevices',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'appId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'appType' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orderBy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'orgUnitId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_telemetry_devices = new ChromeManagement\Resource\CustomersTelemetryDevices(
        $this,
        $this->serviceName,
        'devices',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/telemetry/devices',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_telemetry_events = new ChromeManagement\Resource\CustomersTelemetryEvents(
        $this,
        $this->serviceName,
        'events',
        [
          'methods' => [
            'list' => [
              'path' => 'v1/{+parent}/telemetry/events',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_telemetry_notificationConfigs = new ChromeManagement\Resource\CustomersTelemetryNotificationConfigs(
        $this,
        $this->serviceName,
        'notificationConfigs',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/{+parent}/telemetry/notificationConfigs',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/telemetry/notificationConfigs',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_telemetry_users = new ChromeManagement\Resource\CustomersTelemetryUsers(
        $this,
        $this->serviceName,
        'users',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+parent}/telemetry/users',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'readMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->customers_thirdPartyProfileUsers = new ChromeManagement\Resource\CustomersThirdPartyProfileUsers(
        $this,
        $this->serviceName,
        'thirdPartyProfileUsers',
        [
          'methods' => [
            'move' => [
              'path' => 'v1/{+name}:move',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->operations = new ChromeManagement\Resource\Operations(
        $this,
        $this->serviceName,
        'operations',
        [
          'methods' => [
            'cancel' => [
              'path' => 'v1/{+name}:cancel',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'returnPartialSuccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeManagement::class, 'Google_Service_ChromeManagement');
