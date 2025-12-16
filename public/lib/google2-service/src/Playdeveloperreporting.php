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
 * Service definition for Playdeveloperreporting (v1beta1).
 *
 * <p>
</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/play/developer/reporting" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Playdeveloperreporting extends \Google\Service
{
  /** See metrics and data about the apps in your Google Play Developer account. */
  const PLAYDEVELOPERREPORTING =
      "https://www.googleapis.com/auth/playdeveloperreporting";

  public $anomalies;
  public $apps;
  public $vitals_anrrate;
  public $vitals_crashrate;
  public $vitals_errors_counts;
  public $vitals_errors_issues;
  public $vitals_errors_reports;
  public $vitals_excessivewakeuprate;
  public $vitals_lmkrate;
  public $vitals_slowrenderingrate;
  public $vitals_slowstartrate;
  public $vitals_stuckbackgroundwakelockrate;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Playdeveloperreporting
   * service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://playdeveloperreporting.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://playdeveloperreporting.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1beta1';
    $this->serviceName = 'playdeveloperreporting';

    $this->anomalies = new Playdeveloperreporting\Resource\Anomalies(
        $this,
        $this->serviceName,
        'anomalies',
        [
          'methods' => [
            'list' => [
              'path' => 'v1beta1/{+parent}/anomalies',
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
              ],
            ],
          ]
        ]
    );
    $this->apps = new Playdeveloperreporting\Resource\Apps(
        $this,
        $this->serviceName,
        'apps',
        [
          'methods' => [
            'fetchReleaseFilterOptions' => [
              'path' => 'v1beta1/{+name}:fetchReleaseFilterOptions',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'search' => [
              'path' => 'v1beta1/apps:search',
              'httpMethod' => 'GET',
              'parameters' => [
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
    $this->vitals_anrrate = new Playdeveloperreporting\Resource\VitalsAnrrate(
        $this,
        $this->serviceName,
        'anrrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_crashrate = new Playdeveloperreporting\Resource\VitalsCrashrate(
        $this,
        $this->serviceName,
        'crashrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_errors_counts = new Playdeveloperreporting\Resource\VitalsErrorsCounts(
        $this,
        $this->serviceName,
        'counts',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_errors_issues = new Playdeveloperreporting\Resource\VitalsErrorsIssues(
        $this,
        $this->serviceName,
        'issues',
        [
          'methods' => [
            'search' => [
              'path' => 'v1beta1/{+parent}/errorIssues:search',
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
                'interval.endTime.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.hours' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.minutes' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.nanos' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.seconds' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.timeZone.id' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.timeZone.version' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.utcOffset' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.year' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.hours' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.minutes' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.nanos' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.seconds' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.timeZone.id' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.timeZone.version' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.utcOffset' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.year' => [
                  'location' => 'query',
                  'type' => 'integer',
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
                'sampleErrorReportLimit' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
              ],
            ],
          ]
        ]
    );
    $this->vitals_errors_reports = new Playdeveloperreporting\Resource\VitalsErrorsReports(
        $this,
        $this->serviceName,
        'reports',
        [
          'methods' => [
            'search' => [
              'path' => 'v1beta1/{+parent}/errorReports:search',
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
                'interval.endTime.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.hours' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.minutes' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.nanos' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.seconds' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.endTime.timeZone.id' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.timeZone.version' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.utcOffset' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.endTime.year' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.day' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.hours' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.minutes' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.month' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.nanos' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.seconds' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'interval.startTime.timeZone.id' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.timeZone.version' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.utcOffset' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'interval.startTime.year' => [
                  'location' => 'query',
                  'type' => 'integer',
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
    $this->vitals_excessivewakeuprate = new Playdeveloperreporting\Resource\VitalsExcessivewakeuprate(
        $this,
        $this->serviceName,
        'excessivewakeuprate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_lmkrate = new Playdeveloperreporting\Resource\VitalsLmkrate(
        $this,
        $this->serviceName,
        'lmkrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_slowrenderingrate = new Playdeveloperreporting\Resource\VitalsSlowrenderingrate(
        $this,
        $this->serviceName,
        'slowrenderingrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_slowstartrate = new Playdeveloperreporting\Resource\VitalsSlowstartrate(
        $this,
        $this->serviceName,
        'slowstartrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
    $this->vitals_stuckbackgroundwakelockrate = new Playdeveloperreporting\Resource\VitalsStuckbackgroundwakelockrate(
        $this,
        $this->serviceName,
        'stuckbackgroundwakelockrate',
        [
          'methods' => [
            'get' => [
              'path' => 'v1beta1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'query' => [
              'path' => 'v1beta1/{+name}:query',
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
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Playdeveloperreporting::class, 'Google_Service_Playdeveloperreporting');
