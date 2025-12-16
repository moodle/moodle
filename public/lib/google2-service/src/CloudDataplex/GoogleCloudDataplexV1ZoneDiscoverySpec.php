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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1ZoneDiscoverySpec extends \Google\Collection
{
  protected $collection_key = 'includePatterns';
  protected $csvOptionsType = GoogleCloudDataplexV1ZoneDiscoverySpecCsvOptions::class;
  protected $csvOptionsDataType = '';
  /**
   * Required. Whether discovery is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. The list of patterns to apply for selecting data to exclude
   * during discovery. For Cloud Storage bucket assets, these are interpreted as
   * glob patterns used to match object names. For BigQuery dataset assets,
   * these are interpreted as patterns to match table names.
   *
   * @var string[]
   */
  public $excludePatterns;
  /**
   * Optional. The list of patterns to apply for selecting data to include
   * during discovery if only a subset of the data should considered. For Cloud
   * Storage bucket assets, these are interpreted as glob patterns used to match
   * object names. For BigQuery dataset assets, these are interpreted as
   * patterns to match table names.
   *
   * @var string[]
   */
  public $includePatterns;
  protected $jsonOptionsType = GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions::class;
  protected $jsonOptionsDataType = '';
  /**
   * Optional. Cron schedule (https://en.wikipedia.org/wiki/Cron) for running
   * discovery periodically. Successive discovery runs must be scheduled at
   * least 60 minutes apart. The default value is to run discovery every 60
   * minutes.To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, CRON_TZ=America/New_York 1 * * * *, or TZ=America/New_York 1 *
   * * * *.
   *
   * @var string
   */
  public $schedule;

  /**
   * Optional. Configuration for CSV data.
   *
   * @param GoogleCloudDataplexV1ZoneDiscoverySpecCsvOptions $csvOptions
   */
  public function setCsvOptions(GoogleCloudDataplexV1ZoneDiscoverySpecCsvOptions $csvOptions)
  {
    $this->csvOptions = $csvOptions;
  }
  /**
   * @return GoogleCloudDataplexV1ZoneDiscoverySpecCsvOptions
   */
  public function getCsvOptions()
  {
    return $this->csvOptions;
  }
  /**
   * Required. Whether discovery is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. The list of patterns to apply for selecting data to exclude
   * during discovery. For Cloud Storage bucket assets, these are interpreted as
   * glob patterns used to match object names. For BigQuery dataset assets,
   * these are interpreted as patterns to match table names.
   *
   * @param string[] $excludePatterns
   */
  public function setExcludePatterns($excludePatterns)
  {
    $this->excludePatterns = $excludePatterns;
  }
  /**
   * @return string[]
   */
  public function getExcludePatterns()
  {
    return $this->excludePatterns;
  }
  /**
   * Optional. The list of patterns to apply for selecting data to include
   * during discovery if only a subset of the data should considered. For Cloud
   * Storage bucket assets, these are interpreted as glob patterns used to match
   * object names. For BigQuery dataset assets, these are interpreted as
   * patterns to match table names.
   *
   * @param string[] $includePatterns
   */
  public function setIncludePatterns($includePatterns)
  {
    $this->includePatterns = $includePatterns;
  }
  /**
   * @return string[]
   */
  public function getIncludePatterns()
  {
    return $this->includePatterns;
  }
  /**
   * Optional. Configuration for Json data.
   *
   * @param GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions $jsonOptions
   */
  public function setJsonOptions(GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions $jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
  }
  /**
   * @return GoogleCloudDataplexV1ZoneDiscoverySpecJsonOptions
   */
  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }
  /**
   * Optional. Cron schedule (https://en.wikipedia.org/wiki/Cron) for running
   * discovery periodically. Successive discovery runs must be scheduled at
   * least 60 minutes apart. The default value is to run discovery every 60
   * minutes.To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, CRON_TZ=America/New_York 1 * * * *, or TZ=America/New_York 1 *
   * * * *.
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1ZoneDiscoverySpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1ZoneDiscoverySpec');
