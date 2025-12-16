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

class SearchJobsRequest extends \Google\Collection
{
  /**
   * The diversification level isn't specified.
   */
  public const DIVERSIFICATION_LEVEL_DIVERSIFICATION_LEVEL_UNSPECIFIED = 'DIVERSIFICATION_LEVEL_UNSPECIFIED';
  /**
   * Disables diversification. Jobs that would normally be pushed to the last
   * page would not have their positions altered. This may result in highly
   * similar jobs appearing in sequence in the search results.
   */
  public const DIVERSIFICATION_LEVEL_DISABLED = 'DISABLED';
  /**
   * Default diversifying behavior. The result list is ordered so that highly
   * similar results are pushed to the end of the last page of search results.
   */
  public const DIVERSIFICATION_LEVEL_SIMPLE = 'SIMPLE';
  /**
   * Only one job from the same company will be shown at once, other jobs under
   * same company are pushed to the end of the last page of search result.
   */
  public const DIVERSIFICATION_LEVEL_ONE_PER_COMPANY = 'ONE_PER_COMPANY';
  /**
   * Similar to ONE_PER_COMPANY, but it allows at most two jobs in the same
   * company to be shown at once, the other jobs under same company are pushed
   * to the end of the last page of search result.
   */
  public const DIVERSIFICATION_LEVEL_TWO_PER_COMPANY = 'TWO_PER_COMPANY';
  /**
   * Similar to ONE_PER_COMPANY, but it allows at most three jobs in the same
   * company to be shown at once, the other jobs under same company are dropped.
   */
  public const DIVERSIFICATION_LEVEL_MAX_THREE_PER_COMPANY = 'MAX_THREE_PER_COMPANY';
  /**
   * The result list is ordered such that somewhat similar results are pushed to
   * the end of the last page of the search results. This option is recommended
   * if SIMPLE diversification does not diversify enough.
   */
  public const DIVERSIFICATION_LEVEL_DIVERSIFY_BY_LOOSER_SIMILARITY = 'DIVERSIFY_BY_LOOSER_SIMILARITY';
  /**
   * Default value.
   */
  public const JOB_VIEW_JOB_VIEW_UNSPECIFIED = 'JOB_VIEW_UNSPECIFIED';
  /**
   * A ID only view of job, with following attributes: Job.name,
   * Job.requisition_id, Job.language_code.
   */
  public const JOB_VIEW_JOB_VIEW_ID_ONLY = 'JOB_VIEW_ID_ONLY';
  /**
   * A minimal view of the job, with the following attributes: Job.name,
   * Job.requisition_id, Job.title, Job.company, Job.DerivedInfo.locations,
   * Job.language_code.
   */
  public const JOB_VIEW_JOB_VIEW_MINIMAL = 'JOB_VIEW_MINIMAL';
  /**
   * A small view of the job, with the following attributes in the search
   * results: Job.name, Job.requisition_id, Job.title, Job.company,
   * Job.DerivedInfo.locations, Job.visibility, Job.language_code,
   * Job.description.
   */
  public const JOB_VIEW_JOB_VIEW_SMALL = 'JOB_VIEW_SMALL';
  /**
   * All available attributes are included in the search results.
   */
  public const JOB_VIEW_JOB_VIEW_FULL = 'JOB_VIEW_FULL';
  /**
   * The keyword match option isn't specified. Defaults to
   * KeywordMatchMode.KEYWORD_MATCH_ALL behavior.
   */
  public const KEYWORD_MATCH_MODE_KEYWORD_MATCH_MODE_UNSPECIFIED = 'KEYWORD_MATCH_MODE_UNSPECIFIED';
  /**
   * Disables keyword matching.
   */
  public const KEYWORD_MATCH_MODE_KEYWORD_MATCH_DISABLED = 'KEYWORD_MATCH_DISABLED';
  /**
   * Enable keyword matching over Job.title, Job.description,
   * Job.company_display_name, Job.addresses, Job.qualifications, and keyword
   * searchable Job.custom_attributes fields.
   */
  public const KEYWORD_MATCH_MODE_KEYWORD_MATCH_ALL = 'KEYWORD_MATCH_ALL';
  /**
   * Only enable keyword matching over Job.title.
   */
  public const KEYWORD_MATCH_MODE_KEYWORD_MATCH_TITLE_ONLY = 'KEYWORD_MATCH_TITLE_ONLY';
  /**
   * Default value. In this case, server behavior defaults to Google defined
   * threshold.
   */
  public const RELEVANCE_THRESHOLD_RELEVANCE_THRESHOLD_UNSPECIFIED = 'RELEVANCE_THRESHOLD_UNSPECIFIED';
  /**
   * Lowest relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_LOWEST = 'LOWEST';
  /**
   * Low relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_LOW = 'LOW';
  /**
   * Medium relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_MEDIUM = 'MEDIUM';
  /**
   * High relevance threshold.
   */
  public const RELEVANCE_THRESHOLD_HIGH = 'HIGH';
  /**
   * The mode of the search method isn't specified. The default search behavior
   * is identical to JOB_SEARCH search behavior.
   */
  public const SEARCH_MODE_SEARCH_MODE_UNSPECIFIED = 'SEARCH_MODE_UNSPECIFIED';
  /**
   * The job search matches against all jobs, and featured jobs (jobs with
   * promotionValue > 0) are not specially handled.
   */
  public const SEARCH_MODE_JOB_SEARCH = 'JOB_SEARCH';
  /**
   * The job search matches only against featured jobs (jobs with a
   * promotionValue > 0). This method doesn't return any jobs having a
   * promotionValue <= 0. The search results order is determined by the
   * promotionValue (jobs with a higher promotionValue are returned higher up in
   * the search results), with relevance being used as a tiebreaker.
   */
  public const SEARCH_MODE_FEATURED_JOB_SEARCH = 'FEATURED_JOB_SEARCH';
  protected $collection_key = 'histogramQueries';
  protected $customRankingInfoType = CustomRankingInfo::class;
  protected $customRankingInfoDataType = '';
  /**
   * This field is deprecated. Please use SearchJobsRequest.keyword_match_mode
   * going forward. To migrate, disable_keyword_match set to false maps to
   * KeywordMatchMode.KEYWORD_MATCH_ALL, and disable_keyword_match set to true
   * maps to KeywordMatchMode.KEYWORD_MATCH_DISABLED. If
   * SearchJobsRequest.keyword_match_mode is set, this field is ignored.
   * Controls whether to disable exact keyword match on Job.title,
   * Job.description, Job.company_display_name, Job.addresses,
   * Job.qualifications. When disable keyword match is turned off, a keyword
   * match returns jobs that do not match given category filters when there are
   * matching keywords. For example, for the query "program manager," a result
   * is returned even if the job posting has the title "software developer,"
   * which doesn't fall into "program manager" ontology, but does have "program
   * manager" appearing in its description. For queries like "cloud" that don't
   * contain title or location specific ontology, jobs with "cloud" keyword
   * matches are returned regardless of this flag's value. Use
   * Company.keyword_searchable_job_custom_attributes if company-specific
   * globally matched custom field/attribute string values are needed. Enabling
   * keyword match improves recall of subsequent search requests. Defaults to
   * false.
   *
   * @deprecated
   * @var bool
   */
  public $disableKeywordMatch;
  /**
   * Controls whether highly similar jobs are returned next to each other in the
   * search results. Jobs are identified as highly similar based on their
   * titles, job categories, and locations. Highly similar results are clustered
   * so that only one representative job of the cluster is displayed to the job
   * seeker higher up in the results, with the other jobs being displayed lower
   * down in the results. Defaults to DiversificationLevel.SIMPLE if no value is
   * specified.
   *
   * @var string
   */
  public $diversificationLevel;
  /**
   * Controls whether to broaden the search when it produces sparse results.
   * Broadened queries append results to the end of the matching results list.
   * Defaults to false.
   *
   * @var bool
   */
  public $enableBroadening;
  protected $histogramQueriesType = HistogramQuery::class;
  protected $histogramQueriesDataType = 'array';
  protected $jobQueryType = JobQuery::class;
  protected $jobQueryDataType = '';
  /**
   * The desired job attributes returned for jobs in the search response.
   * Defaults to JobView.JOB_VIEW_SMALL if no value is specified.
   *
   * @var string
   */
  public $jobView;
  /**
   * Controls what keyword match options to use. If both keyword_match_mode and
   * disable_keyword_match are set, keyword_match_mode will take precedence.
   * Defaults to KeywordMatchMode.KEYWORD_MATCH_ALL if no value is specified.
   *
   * @var string
   */
  public $keywordMatchMode;
  /**
   * A limit on the number of jobs returned in the search results. Increasing
   * this value above the default value of 10 can increase search response time.
   * The value can be between 1 and 100.
   *
   * @var int
   */
  public $maxPageSize;
  /**
   * An integer that specifies the current offset (that is, starting result
   * location, amongst the jobs deemed by the API as relevant) in search
   * results. This field is only considered if page_token is unset. The maximum
   * allowed value is 5000. Otherwise an error is thrown. For example, 0 means
   * to return results starting from the first matching job, and 10 means to
   * return from the 11th job. This can be used for pagination, (for example,
   * pageSize = 10 and offset = 10 means to return from the second page).
   *
   * @var int
   */
  public $offset;
  /**
   * The criteria determining how search results are sorted. Default is
   * `"relevance desc"`. Supported options are: * `"relevance desc"`: By
   * relevance descending, as determined by the API algorithms. Relevance
   * thresholding of query results is only available with this ordering. *
   * `"posting_publish_time desc"`: By Job.posting_publish_time descending. *
   * `"posting_update_time desc"`: By Job.posting_update_time descending. *
   * `"title"`: By Job.title ascending. * `"title desc"`: By Job.title
   * descending. * `"annualized_base_compensation"`: By job's
   * CompensationInfo.annualized_base_compensation_range ascending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_base_compensation desc"`: By job's
   * CompensationInfo.annualized_base_compensation_range descending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_total_compensation"`: By job's
   * CompensationInfo.annualized_total_compensation_range ascending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_total_compensation desc"`: By job's
   * CompensationInfo.annualized_total_compensation_range descending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"custom_ranking desc"`: By the relevance score adjusted to the
   * SearchJobsRequest.CustomRankingInfo.ranking_expression with weight factor
   * assigned by SearchJobsRequest.CustomRankingInfo.importance_level in
   * descending order. * Location sorting: Use the special syntax to order jobs
   * by distance: `"distance_from('Hawaii')"`: Order by distance from Hawaii.
   * `"distance_from(19.89, 155.5)"`: Order by distance from a coordinate.
   * `"distance_from('Hawaii'), distance_from('Puerto Rico')"`: Order by
   * multiple locations. See details below. `"distance_from('Hawaii'),
   * distance_from(19.89, 155.5)"`: Order by multiple locations. See details
   * below. The string can have a maximum of 256 characters. When multiple
   * distance centers are provided, a job that is close to any of the distance
   * centers would have a high rank. When a job has multiple locations, the job
   * location closest to one of the distance centers will be used. Jobs that
   * don't have locations will be ranked at the bottom. Distance is calculated
   * with a precision of 11.3 meters (37.4 feet). Diversification strategy is
   * still applied unless explicitly disabled in diversification_level.
   *
   * @var string
   */
  public $orderBy;
  /**
   * The token specifying the current offset within search results. See
   * SearchJobsResponse.next_page_token for an explanation of how to obtain the
   * next set of query results.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. The relevance threshold of the search results. Default to Google
   * defined threshold, leveraging a balance of precision and recall to deliver
   * both highly accurate results and comprehensive coverage of relevant
   * information.
   *
   * @var string
   */
  public $relevanceThreshold;
  protected $requestMetadataType = RequestMetadata::class;
  protected $requestMetadataDataType = '';
  /**
   * Mode of a search. Defaults to SearchMode.JOB_SEARCH.
   *
   * @var string
   */
  public $searchMode;

  /**
   * Controls over how job documents get ranked on top of existing relevance
   * score (determined by API algorithm).
   *
   * @param CustomRankingInfo $customRankingInfo
   */
  public function setCustomRankingInfo(CustomRankingInfo $customRankingInfo)
  {
    $this->customRankingInfo = $customRankingInfo;
  }
  /**
   * @return CustomRankingInfo
   */
  public function getCustomRankingInfo()
  {
    return $this->customRankingInfo;
  }
  /**
   * This field is deprecated. Please use SearchJobsRequest.keyword_match_mode
   * going forward. To migrate, disable_keyword_match set to false maps to
   * KeywordMatchMode.KEYWORD_MATCH_ALL, and disable_keyword_match set to true
   * maps to KeywordMatchMode.KEYWORD_MATCH_DISABLED. If
   * SearchJobsRequest.keyword_match_mode is set, this field is ignored.
   * Controls whether to disable exact keyword match on Job.title,
   * Job.description, Job.company_display_name, Job.addresses,
   * Job.qualifications. When disable keyword match is turned off, a keyword
   * match returns jobs that do not match given category filters when there are
   * matching keywords. For example, for the query "program manager," a result
   * is returned even if the job posting has the title "software developer,"
   * which doesn't fall into "program manager" ontology, but does have "program
   * manager" appearing in its description. For queries like "cloud" that don't
   * contain title or location specific ontology, jobs with "cloud" keyword
   * matches are returned regardless of this flag's value. Use
   * Company.keyword_searchable_job_custom_attributes if company-specific
   * globally matched custom field/attribute string values are needed. Enabling
   * keyword match improves recall of subsequent search requests. Defaults to
   * false.
   *
   * @deprecated
   * @param bool $disableKeywordMatch
   */
  public function setDisableKeywordMatch($disableKeywordMatch)
  {
    $this->disableKeywordMatch = $disableKeywordMatch;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDisableKeywordMatch()
  {
    return $this->disableKeywordMatch;
  }
  /**
   * Controls whether highly similar jobs are returned next to each other in the
   * search results. Jobs are identified as highly similar based on their
   * titles, job categories, and locations. Highly similar results are clustered
   * so that only one representative job of the cluster is displayed to the job
   * seeker higher up in the results, with the other jobs being displayed lower
   * down in the results. Defaults to DiversificationLevel.SIMPLE if no value is
   * specified.
   *
   * Accepted values: DIVERSIFICATION_LEVEL_UNSPECIFIED, DISABLED, SIMPLE,
   * ONE_PER_COMPANY, TWO_PER_COMPANY, MAX_THREE_PER_COMPANY,
   * DIVERSIFY_BY_LOOSER_SIMILARITY
   *
   * @param self::DIVERSIFICATION_LEVEL_* $diversificationLevel
   */
  public function setDiversificationLevel($diversificationLevel)
  {
    $this->diversificationLevel = $diversificationLevel;
  }
  /**
   * @return self::DIVERSIFICATION_LEVEL_*
   */
  public function getDiversificationLevel()
  {
    return $this->diversificationLevel;
  }
  /**
   * Controls whether to broaden the search when it produces sparse results.
   * Broadened queries append results to the end of the matching results list.
   * Defaults to false.
   *
   * @param bool $enableBroadening
   */
  public function setEnableBroadening($enableBroadening)
  {
    $this->enableBroadening = $enableBroadening;
  }
  /**
   * @return bool
   */
  public function getEnableBroadening()
  {
    return $this->enableBroadening;
  }
  /**
   * An expression specifies a histogram request against matching jobs.
   * Expression syntax is an aggregation function call with histogram facets and
   * other options. Available aggregation function calls are: *
   * `count(string_histogram_facet)`: Count the number of matching entities, for
   * each distinct attribute value. * `count(numeric_histogram_facet, list of
   * buckets)`: Count the number of matching entities within each bucket. A
   * maximum of 200 histogram buckets are supported. Data types: * Histogram
   * facet: facet names with format `a-zA-Z+`. * String: string like "any string
   * with backslash escape for quote(\")." * Number: whole number and floating
   * point number like 10, -1 and -0.01. * List: list of elements with comma(,)
   * separator surrounded by square brackets, for example, [1, 2, 3] and ["one",
   * "two", "three"]. Built-in constants: * MIN (minimum number similar to java
   * Double.MIN_VALUE) * MAX (maximum number similar to java Double.MAX_VALUE)
   * Built-in functions: * bucket(start, end[, label]): bucket built-in function
   * creates a bucket with range of start, end). Note that the end is exclusive,
   * for example, bucket(1, MAX, "positive number") or bucket(1, 10). Job
   * histogram facets: * company_display_name: histogram by
   * [Job.company_display_name. * employment_type: histogram by
   * Job.employment_types, for example, "FULL_TIME", "PART_TIME". * company_size
   * (DEPRECATED): histogram by CompanySize, for example, "SMALL", "MEDIUM",
   * "BIG". * publish_time_in_day: histogram by the Job.posting_publish_time in
   * days. Must specify list of numeric buckets in spec. *
   * publish_time_in_month: histogram by the Job.posting_publish_time in months.
   * Must specify list of numeric buckets in spec. * publish_time_in_year:
   * histogram by the Job.posting_publish_time in years. Must specify list of
   * numeric buckets in spec. * degree_types: histogram by the Job.degree_types,
   * for example, "Bachelors", "Masters". * job_level: histogram by the
   * Job.job_level, for example, "Entry Level". * country: histogram by the
   * country code of jobs, for example, "US", "FR". * admin1: histogram by the
   * admin1 code of jobs, which is a global placeholder referring to the state,
   * province, or the particular term a country uses to define the geographic
   * structure below the country level, for example, "CA", "IL". * city:
   * histogram by a combination of the "city name, admin1 code". For example,
   * "Mountain View, CA", "New York, NY". * admin1_country: histogram by a
   * combination of the "admin1 code, country", for example, "CA, US", "IL, US".
   * * city_coordinate: histogram by the city center's GPS coordinates (latitude
   * and longitude), for example, 37.4038522,-122.0987765. Since the coordinates
   * of a city center can change, customers may need to refresh them
   * periodically. * locale: histogram by the Job.language_code, for example,
   * "en-US", "fr-FR". * language: histogram by the language subtag of the
   * Job.language_code, for example, "en", "fr". * category: histogram by the
   * JobCategory, for example, "COMPUTER_AND_IT", "HEALTHCARE". *
   * base_compensation_unit: histogram by the CompensationInfo.CompensationUnit
   * of base salary, for example, "WEEKLY", "MONTHLY". * base_compensation:
   * histogram by the base salary. Must specify list of numeric buckets to group
   * results by. * annualized_base_compensation: histogram by the base
   * annualized salary. Must specify list of numeric buckets to group results
   * by. * annualized_total_compensation: histogram by the total annualized
   * salary. Must specify list of numeric buckets to group results by. *
   * string_custom_attribute: histogram by string Job.custom_attributes. Values
   * can be accessed via square bracket notations like
   * string_custom_attribute["key1"]. * numeric_custom_attribute: histogram by
   * numeric Job.custom_attributes. Values can be accessed via square bracket
   * notations like numeric_custom_attribute["key1"]. Must specify list of
   * numeric buckets to group results by. Example expressions: * `count(admin1)`
   * * `count(base_compensation, [bucket(1000, 10000), bucket(10000, 100000),
   * bucket(100000, MAX)])` * `count(string_custom_attribute["some-string-
   * custom-attribute"])` * `count(numeric_custom_attribute["some-numeric-
   * custom-attribute"], [bucket(MIN, 0, "negative"), bucket(0, MAX, "non-
   * negative")])`
   *
   * @param HistogramQuery[] $histogramQueries
   */
  public function setHistogramQueries($histogramQueries)
  {
    $this->histogramQueries = $histogramQueries;
  }
  /**
   * @return HistogramQuery[]
   */
  public function getHistogramQueries()
  {
    return $this->histogramQueries;
  }
  /**
   * Query used to search against jobs, such as keyword, location filters, etc.
   *
   * @param JobQuery $jobQuery
   */
  public function setJobQuery(JobQuery $jobQuery)
  {
    $this->jobQuery = $jobQuery;
  }
  /**
   * @return JobQuery
   */
  public function getJobQuery()
  {
    return $this->jobQuery;
  }
  /**
   * The desired job attributes returned for jobs in the search response.
   * Defaults to JobView.JOB_VIEW_SMALL if no value is specified.
   *
   * Accepted values: JOB_VIEW_UNSPECIFIED, JOB_VIEW_ID_ONLY, JOB_VIEW_MINIMAL,
   * JOB_VIEW_SMALL, JOB_VIEW_FULL
   *
   * @param self::JOB_VIEW_* $jobView
   */
  public function setJobView($jobView)
  {
    $this->jobView = $jobView;
  }
  /**
   * @return self::JOB_VIEW_*
   */
  public function getJobView()
  {
    return $this->jobView;
  }
  /**
   * Controls what keyword match options to use. If both keyword_match_mode and
   * disable_keyword_match are set, keyword_match_mode will take precedence.
   * Defaults to KeywordMatchMode.KEYWORD_MATCH_ALL if no value is specified.
   *
   * Accepted values: KEYWORD_MATCH_MODE_UNSPECIFIED, KEYWORD_MATCH_DISABLED,
   * KEYWORD_MATCH_ALL, KEYWORD_MATCH_TITLE_ONLY
   *
   * @param self::KEYWORD_MATCH_MODE_* $keywordMatchMode
   */
  public function setKeywordMatchMode($keywordMatchMode)
  {
    $this->keywordMatchMode = $keywordMatchMode;
  }
  /**
   * @return self::KEYWORD_MATCH_MODE_*
   */
  public function getKeywordMatchMode()
  {
    return $this->keywordMatchMode;
  }
  /**
   * A limit on the number of jobs returned in the search results. Increasing
   * this value above the default value of 10 can increase search response time.
   * The value can be between 1 and 100.
   *
   * @param int $maxPageSize
   */
  public function setMaxPageSize($maxPageSize)
  {
    $this->maxPageSize = $maxPageSize;
  }
  /**
   * @return int
   */
  public function getMaxPageSize()
  {
    return $this->maxPageSize;
  }
  /**
   * An integer that specifies the current offset (that is, starting result
   * location, amongst the jobs deemed by the API as relevant) in search
   * results. This field is only considered if page_token is unset. The maximum
   * allowed value is 5000. Otherwise an error is thrown. For example, 0 means
   * to return results starting from the first matching job, and 10 means to
   * return from the 11th job. This can be used for pagination, (for example,
   * pageSize = 10 and offset = 10 means to return from the second page).
   *
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * The criteria determining how search results are sorted. Default is
   * `"relevance desc"`. Supported options are: * `"relevance desc"`: By
   * relevance descending, as determined by the API algorithms. Relevance
   * thresholding of query results is only available with this ordering. *
   * `"posting_publish_time desc"`: By Job.posting_publish_time descending. *
   * `"posting_update_time desc"`: By Job.posting_update_time descending. *
   * `"title"`: By Job.title ascending. * `"title desc"`: By Job.title
   * descending. * `"annualized_base_compensation"`: By job's
   * CompensationInfo.annualized_base_compensation_range ascending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_base_compensation desc"`: By job's
   * CompensationInfo.annualized_base_compensation_range descending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_total_compensation"`: By job's
   * CompensationInfo.annualized_total_compensation_range ascending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"annualized_total_compensation desc"`: By job's
   * CompensationInfo.annualized_total_compensation_range descending. Jobs whose
   * annualized base compensation is unspecified are put at the end of search
   * results. * `"custom_ranking desc"`: By the relevance score adjusted to the
   * SearchJobsRequest.CustomRankingInfo.ranking_expression with weight factor
   * assigned by SearchJobsRequest.CustomRankingInfo.importance_level in
   * descending order. * Location sorting: Use the special syntax to order jobs
   * by distance: `"distance_from('Hawaii')"`: Order by distance from Hawaii.
   * `"distance_from(19.89, 155.5)"`: Order by distance from a coordinate.
   * `"distance_from('Hawaii'), distance_from('Puerto Rico')"`: Order by
   * multiple locations. See details below. `"distance_from('Hawaii'),
   * distance_from(19.89, 155.5)"`: Order by multiple locations. See details
   * below. The string can have a maximum of 256 characters. When multiple
   * distance centers are provided, a job that is close to any of the distance
   * centers would have a high rank. When a job has multiple locations, the job
   * location closest to one of the distance centers will be used. Jobs that
   * don't have locations will be ranked at the bottom. Distance is calculated
   * with a precision of 11.3 meters (37.4 feet). Diversification strategy is
   * still applied unless explicitly disabled in diversification_level.
   *
   * @param string $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * The token specifying the current offset within search results. See
   * SearchJobsResponse.next_page_token for an explanation of how to obtain the
   * next set of query results.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Optional. The relevance threshold of the search results. Default to Google
   * defined threshold, leveraging a balance of precision and recall to deliver
   * both highly accurate results and comprehensive coverage of relevant
   * information.
   *
   * Accepted values: RELEVANCE_THRESHOLD_UNSPECIFIED, LOWEST, LOW, MEDIUM, HIGH
   *
   * @param self::RELEVANCE_THRESHOLD_* $relevanceThreshold
   */
  public function setRelevanceThreshold($relevanceThreshold)
  {
    $this->relevanceThreshold = $relevanceThreshold;
  }
  /**
   * @return self::RELEVANCE_THRESHOLD_*
   */
  public function getRelevanceThreshold()
  {
    return $this->relevanceThreshold;
  }
  /**
   * Required. The meta information collected about the job searcher, used to
   * improve the search quality of the service. The identifiers (such as
   * `user_id`) are provided by users, and must be unique and consistent.
   *
   * @param RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
  /**
   * Mode of a search. Defaults to SearchMode.JOB_SEARCH.
   *
   * Accepted values: SEARCH_MODE_UNSPECIFIED, JOB_SEARCH, FEATURED_JOB_SEARCH
   *
   * @param self::SEARCH_MODE_* $searchMode
   */
  public function setSearchMode($searchMode)
  {
    $this->searchMode = $searchMode;
  }
  /**
   * @return self::SEARCH_MODE_*
   */
  public function getSearchMode()
  {
    return $this->searchMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchJobsRequest::class, 'Google_Service_CloudTalentSolution_SearchJobsRequest');
