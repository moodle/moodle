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

namespace Google\Service\CloudTalentSolution;

class JobQuery extends \Google\Collection
{
  protected $collection_key = 'locationFilters';
  protected $commuteFilterType = CommuteFilter::class;
  protected $commuteFilterDataType = '';
  /**
   * This filter specifies the company entities to search against. If a value
   * isn't specified, jobs are searched for against all companies. If multiple
   * values are specified, jobs are searched against the companies specified.
   * The format is
   * "projects/{project_id}/tenants/{tenant_id}/companies/{company_id}". For
   * example, "projects/foo/tenants/bar/companies/baz". At most 20 company
   * filters are allowed.
   *
   * @var string[]
   */
  public $companies;
  /**
   * This filter specifies the company Company.display_name of the jobs to
   * search against. The company name must match the value exactly.
   * Alternatively, the value being searched for can be wrapped in different
   * match operators. `SUBSTRING_MATCH([value])` The company name must contain a
   * case insensitive substring match of the value. Using this function may
   * increase latency. Sample Value: `SUBSTRING_MATCH(google)`
   * `MULTI_WORD_TOKEN_MATCH([value])` The value will be treated as a multi word
   * token and the company name must contain a case insensitive match of the
   * value. Using this function may increase latency. Sample Value:
   * `MULTI_WORD_TOKEN_MATCH(google)` If a value isn't specified, jobs within
   * the search results are associated with any company. If multiple values are
   * specified, jobs within the search results may be associated with any of the
   * specified companies. At most 20 company display name filters are allowed.
   *
   * @var string[]
   */
  public $companyDisplayNames;
  protected $compensationFilterType = CompensationFilter::class;
  protected $compensationFilterDataType = '';
  /**
   * This filter specifies a structured syntax to match against the
   * Job.custom_attributes marked as `filterable`. The syntax for this
   * expression is a subset of SQL syntax. Supported operators are: `=`, `!=`,
   * `<`, `<=`, `>`, and `>=` where the left of the operator is a custom field
   * key and the right of the operator is a number or a quoted string. You must
   * escape backslash (\\) and quote (\") characters. Supported functions are
   * `LOWER([field_name])` to perform a case insensitive match and
   * `EMPTY([field_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 10000
   * bytes in length. Sample Query: `(LOWER(driving_license)="class \"a\"" OR
   * EMPTY(driving_license)) AND driving_years > 10`
   *
   * @var string
   */
  public $customAttributeFilter;
  /**
   * This flag controls the spell-check feature. If false, the service attempts
   * to correct a misspelled query, for example, "enginee" is corrected to
   * "engineer". Defaults to false: a spell check is performed.
   *
   * @var bool
   */
  public $disableSpellCheck;
  /**
   * The employment type filter specifies the employment type of jobs to search
   * against, such as EmploymentType.FULL_TIME. If a value isn't specified, jobs
   * in the search results includes any employment type. If multiple values are
   * specified, jobs in the search results include any of the specified
   * employment types.
   *
   * @var string[]
   */
  public $employmentTypes;
  /**
   * This filter specifies a list of job names to be excluded during search. At
   * most 400 excluded job names are allowed.
   *
   * @var string[]
   */
  public $excludedJobs;
  /**
   * The category filter specifies the categories of jobs to search against. See
   * JobCategory for more information. If a value isn't specified, jobs from any
   * category are searched against. If multiple values are specified, jobs from
   * any of the specified categories are searched against.
   *
   * @var string[]
   */
  public $jobCategories;
  /**
   * This filter specifies the locale of jobs to search against, for example,
   * "en-US". If a value isn't specified, the search results can contain jobs in
   * any locale. Language codes should be in BCP-47 format, such as "en-US" or
   * "sr-Latn". For more information, see [Tags for Identifying
   * Languages](https://tools.ietf.org/html/bcp47). At most 10 language code
   * filters are allowed.
   *
   * @var string[]
   */
  public $languageCodes;
  protected $locationFiltersType = LocationFilter::class;
  protected $locationFiltersDataType = 'array';
  protected $publishTimeRangeType = TimestampRange::class;
  protected $publishTimeRangeDataType = '';
  /**
   * The query string that matches against the job title, description, and
   * location fields. The maximum number of allowed characters is 255.
   *
   * @var string
   */
  public $query;
  /**
   * The language code of query. For example, "en-US". This field helps to
   * better interpret the query. If a value isn't specified, the query language
   * code is automatically detected, which may not be accurate. Language code
   * should be in BCP-47 format, such as "en-US" or "sr-Latn". For more
   * information, see [Tags for Identifying
   * Languages](https://tools.ietf.org/html/bcp47).
   *
   * @var string
   */
  public $queryLanguageCode;

  /**
   * Allows filtering jobs by commute time with different travel methods (for
   * example, driving or public transit). Note: This only works when you specify
   * a CommuteMethod. In this case, location_filters is ignored. Currently we
   * don't support sorting by commute time.
   *
   * @param CommuteFilter $commuteFilter
   */
  public function setCommuteFilter(CommuteFilter $commuteFilter)
  {
    $this->commuteFilter = $commuteFilter;
  }
  /**
   * @return CommuteFilter
   */
  public function getCommuteFilter()
  {
    return $this->commuteFilter;
  }
  /**
   * This filter specifies the company entities to search against. If a value
   * isn't specified, jobs are searched for against all companies. If multiple
   * values are specified, jobs are searched against the companies specified.
   * The format is
   * "projects/{project_id}/tenants/{tenant_id}/companies/{company_id}". For
   * example, "projects/foo/tenants/bar/companies/baz". At most 20 company
   * filters are allowed.
   *
   * @param string[] $companies
   */
  public function setCompanies($companies)
  {
    $this->companies = $companies;
  }
  /**
   * @return string[]
   */
  public function getCompanies()
  {
    return $this->companies;
  }
  /**
   * This filter specifies the company Company.display_name of the jobs to
   * search against. The company name must match the value exactly.
   * Alternatively, the value being searched for can be wrapped in different
   * match operators. `SUBSTRING_MATCH([value])` The company name must contain a
   * case insensitive substring match of the value. Using this function may
   * increase latency. Sample Value: `SUBSTRING_MATCH(google)`
   * `MULTI_WORD_TOKEN_MATCH([value])` The value will be treated as a multi word
   * token and the company name must contain a case insensitive match of the
   * value. Using this function may increase latency. Sample Value:
   * `MULTI_WORD_TOKEN_MATCH(google)` If a value isn't specified, jobs within
   * the search results are associated with any company. If multiple values are
   * specified, jobs within the search results may be associated with any of the
   * specified companies. At most 20 company display name filters are allowed.
   *
   * @param string[] $companyDisplayNames
   */
  public function setCompanyDisplayNames($companyDisplayNames)
  {
    $this->companyDisplayNames = $companyDisplayNames;
  }
  /**
   * @return string[]
   */
  public function getCompanyDisplayNames()
  {
    return $this->companyDisplayNames;
  }
  /**
   * This search filter is applied only to Job.compensation_info. For example,
   * if the filter is specified as "Hourly job with per-hour compensation >
   * $15", only jobs meeting these criteria are searched. If a filter isn't
   * defined, all open jobs are searched.
   *
   * @param CompensationFilter $compensationFilter
   */
  public function setCompensationFilter(CompensationFilter $compensationFilter)
  {
    $this->compensationFilter = $compensationFilter;
  }
  /**
   * @return CompensationFilter
   */
  public function getCompensationFilter()
  {
    return $this->compensationFilter;
  }
  /**
   * This filter specifies a structured syntax to match against the
   * Job.custom_attributes marked as `filterable`. The syntax for this
   * expression is a subset of SQL syntax. Supported operators are: `=`, `!=`,
   * `<`, `<=`, `>`, and `>=` where the left of the operator is a custom field
   * key and the right of the operator is a number or a quoted string. You must
   * escape backslash (\\) and quote (\") characters. Supported functions are
   * `LOWER([field_name])` to perform a case insensitive match and
   * `EMPTY([field_name])` to filter on the existence of a key. Boolean
   * expressions (AND/OR/NOT) are supported up to 3 levels of nesting (for
   * example, "((A AND B AND C) OR NOT D) AND E"), a maximum of 100 comparisons
   * or functions are allowed in the expression. The expression must be < 10000
   * bytes in length. Sample Query: `(LOWER(driving_license)="class \"a\"" OR
   * EMPTY(driving_license)) AND driving_years > 10`
   *
   * @param string $customAttributeFilter
   */
  public function setCustomAttributeFilter($customAttributeFilter)
  {
    $this->customAttributeFilter = $customAttributeFilter;
  }
  /**
   * @return string
   */
  public function getCustomAttributeFilter()
  {
    return $this->customAttributeFilter;
  }
  /**
   * This flag controls the spell-check feature. If false, the service attempts
   * to correct a misspelled query, for example, "enginee" is corrected to
   * "engineer". Defaults to false: a spell check is performed.
   *
   * @param bool $disableSpellCheck
   */
  public function setDisableSpellCheck($disableSpellCheck)
  {
    $this->disableSpellCheck = $disableSpellCheck;
  }
  /**
   * @return bool
   */
  public function getDisableSpellCheck()
  {
    return $this->disableSpellCheck;
  }
  /**
   * The employment type filter specifies the employment type of jobs to search
   * against, such as EmploymentType.FULL_TIME. If a value isn't specified, jobs
   * in the search results includes any employment type. If multiple values are
   * specified, jobs in the search results include any of the specified
   * employment types.
   *
   * @param string[] $employmentTypes
   */
  public function setEmploymentTypes($employmentTypes)
  {
    $this->employmentTypes = $employmentTypes;
  }
  /**
   * @return string[]
   */
  public function getEmploymentTypes()
  {
    return $this->employmentTypes;
  }
  /**
   * This filter specifies a list of job names to be excluded during search. At
   * most 400 excluded job names are allowed.
   *
   * @param string[] $excludedJobs
   */
  public function setExcludedJobs($excludedJobs)
  {
    $this->excludedJobs = $excludedJobs;
  }
  /**
   * @return string[]
   */
  public function getExcludedJobs()
  {
    return $this->excludedJobs;
  }
  /**
   * The category filter specifies the categories of jobs to search against. See
   * JobCategory for more information. If a value isn't specified, jobs from any
   * category are searched against. If multiple values are specified, jobs from
   * any of the specified categories are searched against.
   *
   * @param string[] $jobCategories
   */
  public function setJobCategories($jobCategories)
  {
    $this->jobCategories = $jobCategories;
  }
  /**
   * @return string[]
   */
  public function getJobCategories()
  {
    return $this->jobCategories;
  }
  /**
   * This filter specifies the locale of jobs to search against, for example,
   * "en-US". If a value isn't specified, the search results can contain jobs in
   * any locale. Language codes should be in BCP-47 format, such as "en-US" or
   * "sr-Latn". For more information, see [Tags for Identifying
   * Languages](https://tools.ietf.org/html/bcp47). At most 10 language code
   * filters are allowed.
   *
   * @param string[] $languageCodes
   */
  public function setLanguageCodes($languageCodes)
  {
    $this->languageCodes = $languageCodes;
  }
  /**
   * @return string[]
   */
  public function getLanguageCodes()
  {
    return $this->languageCodes;
  }
  /**
   * The location filter specifies geo-regions containing the jobs to search
   * against. See LocationFilter for more information. If a location value isn't
   * specified, jobs fitting the other search criteria are retrieved regardless
   * of where they're located. If multiple values are specified, jobs are
   * retrieved from any of the specified locations. If different values are
   * specified for the LocationFilter.distance_in_miles parameter, the maximum
   * provided distance is used for all locations. At most 5 location filters are
   * allowed.
   *
   * @param LocationFilter[] $locationFilters
   */
  public function setLocationFilters($locationFilters)
  {
    $this->locationFilters = $locationFilters;
  }
  /**
   * @return LocationFilter[]
   */
  public function getLocationFilters()
  {
    return $this->locationFilters;
  }
  /**
   * Jobs published within a range specified by this filter are searched
   * against.
   *
   * @param TimestampRange $publishTimeRange
   */
  public function setPublishTimeRange(TimestampRange $publishTimeRange)
  {
    $this->publishTimeRange = $publishTimeRange;
  }
  /**
   * @return TimestampRange
   */
  public function getPublishTimeRange()
  {
    return $this->publishTimeRange;
  }
  /**
   * The query string that matches against the job title, description, and
   * location fields. The maximum number of allowed characters is 255.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The language code of query. For example, "en-US". This field helps to
   * better interpret the query. If a value isn't specified, the query language
   * code is automatically detected, which may not be accurate. Language code
   * should be in BCP-47 format, such as "en-US" or "sr-Latn". For more
   * information, see [Tags for Identifying
   * Languages](https://tools.ietf.org/html/bcp47).
   *
   * @param string $queryLanguageCode
   */
  public function setQueryLanguageCode($queryLanguageCode)
  {
    $this->queryLanguageCode = $queryLanguageCode;
  }
  /**
   * @return string
   */
  public function getQueryLanguageCode()
  {
    return $this->queryLanguageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobQuery::class, 'Google_Service_CloudTalentSolution_JobQuery');
