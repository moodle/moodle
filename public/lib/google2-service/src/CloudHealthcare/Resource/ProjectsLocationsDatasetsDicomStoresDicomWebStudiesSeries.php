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

namespace Google\Service\CloudHealthcare\Resource;

use Google\Service\CloudHealthcare\SeriesMetrics;

/**
 * The "series" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $series = $healthcareService->projects_locations_datasets_dicomStores_dicomWeb_studies_series;
 *  </code>
 */
class ProjectsLocationsDatasetsDicomStoresDicomWebStudiesSeries extends \Google\Service\Resource
{
  /**
   * GetSeriesMetrics returns metrics for a series. (series.getSeriesMetrics)
   *
   * @param string $series Required. The series resource path. For example, `proje
   * cts/{project_id}/locations/{location_id}/datasets/{dataset_id}/dicomStores/{d
   * icom_store_id}/dicomWeb/studies/{study_uid}/series/{series_uid}`.
   * @param array $optParams Optional parameters.
   * @return SeriesMetrics
   * @throws \Google\Service\Exception
   */
  public function getSeriesMetrics($series, $optParams = [])
  {
    $params = ['series' => $series];
    $params = array_merge($params, $optParams);
    return $this->call('getSeriesMetrics', [$params], SeriesMetrics::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsDicomStoresDicomWebStudiesSeries::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsDicomStoresDicomWebStudiesSeries');
