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

class Job extends \Google\Collection
{
  /**
   * The default value if the level isn't specified.
   */
  public const JOB_LEVEL_JOB_LEVEL_UNSPECIFIED = 'JOB_LEVEL_UNSPECIFIED';
  /**
   * Entry-level individual contributors, typically with less than 2 years of
   * experience in a similar role. Includes interns.
   */
  public const JOB_LEVEL_ENTRY_LEVEL = 'ENTRY_LEVEL';
  /**
   * Experienced individual contributors, typically with 2+ years of experience
   * in a similar role.
   */
  public const JOB_LEVEL_EXPERIENCED = 'EXPERIENCED';
  /**
   * Entry- to mid-level managers responsible for managing a team of people.
   */
  public const JOB_LEVEL_MANAGER = 'MANAGER';
  /**
   * Senior-level managers responsible for managing teams of managers.
   */
  public const JOB_LEVEL_DIRECTOR = 'DIRECTOR';
  /**
   * Executive-level managers and above, including C-level positions.
   */
  public const JOB_LEVEL_EXECUTIVE = 'EXECUTIVE';
  /**
   * If the region is unspecified, the job is only returned if it matches the
   * LocationFilter.
   */
  public const POSTING_REGION_POSTING_REGION_UNSPECIFIED = 'POSTING_REGION_UNSPECIFIED';
  /**
   * In addition to exact location matching, job posting is returned when the
   * LocationFilter in the search query is in the same administrative area as
   * the returned job posting. For example, if a `ADMINISTRATIVE_AREA` job is
   * posted in "CA, USA", it's returned if LocationFilter has "Mountain View".
   * Administrative area refers to top-level administrative subdivision of this
   * country. For example, US state, IT region, UK constituent nation and JP
   * prefecture.
   */
  public const POSTING_REGION_ADMINISTRATIVE_AREA = 'ADMINISTRATIVE_AREA';
  /**
   * In addition to exact location matching, job is returned when LocationFilter
   * in search query is in the same country as this job. For example, if a
   * `NATION_WIDE` job is posted in "USA", it's returned if LocationFilter has
   * 'Mountain View'.
   */
  public const POSTING_REGION_NATION = 'NATION';
  /**
   * Job allows employees to work remotely (telecommute). If locations are
   * provided with this value, the job is considered as having a location, but
   * telecommuting is allowed.
   */
  public const POSTING_REGION_TELECOMMUTE = 'TELECOMMUTE';
  /**
   * Default value.
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * The resource is only visible to the Google Cloud account who owns it.
   */
  public const VISIBILITY_ACCOUNT_ONLY = 'ACCOUNT_ONLY';
  /**
   * The resource is visible to the owner and may be visible to other
   * applications and processes at Google.
   */
  public const VISIBILITY_SHARED_WITH_GOOGLE = 'SHARED_WITH_GOOGLE';
  /**
   * The resource is visible to the owner and may be visible to all other API
   * clients.
   */
  public const VISIBILITY_SHARED_WITH_PUBLIC = 'SHARED_WITH_PUBLIC';
  protected $collection_key = 'jobBenefits';
  /**
   * Strongly recommended for the best service experience. Location(s) where the
   * employer is looking to hire for this job posting. Specifying the full
   * street address(es) of the hiring location enables better API results,
   * especially job searches by commute time. At most 50 locations are allowed
   * for best search performance. If a job has more locations, it is suggested
   * to split it into multiple jobs with unique requisition_ids (e.g. 'ReqA'
   * becomes 'ReqA-1', 'ReqA-2', and so on.) as multiple jobs with the same
   * company, language_code and requisition_id are not allowed. If the original
   * requisition_id must be preserved, a custom field should be used for
   * storage. It is also suggested to group the locations that close to each
   * other in the same job for better search experience. Jobs with multiple
   * addresses must have their addresses with the same LocationType to allow
   * location filtering to work properly. (For example, a Job with addresses
   * "1600 Amphitheatre Parkway, Mountain View, CA, USA" and "London, UK" may
   * not have location filters applied correctly at search time since the first
   * is a LocationType.STREET_ADDRESS and the second is a
   * LocationType.LOCALITY.) If a job needs to have multiple addresses, it is
   * suggested to split it into multiple jobs with same LocationTypes. The
   * maximum number of allowed characters is 500.
   *
   * @var string[]
   */
  public $addresses;
  protected $applicationInfoType = ApplicationInfo::class;
  protected $applicationInfoDataType = '';
  /**
   * Required. The resource name of the company listing the job. The format is
   * "projects/{project_id}/tenants/{tenant_id}/companies/{company_id}". For
   * example, "projects/foo/tenants/bar/companies/baz".
   *
   * @var string
   */
  public $company;
  /**
   * Output only. Display name of the company listing the job.
   *
   * @var string
   */
  public $companyDisplayName;
  protected $compensationInfoType = CompensationInfo::class;
  protected $compensationInfoDataType = '';
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'map';
  /**
   * The desired education degrees for the job, such as Bachelors, Masters.
   *
   * @var string[]
   */
  public $degreeTypes;
  /**
   * The department or functional area within the company with the open
   * position. The maximum number of allowed characters is 255.
   *
   * @var string
   */
  public $department;
  protected $derivedInfoType = JobDerivedInfo::class;
  protected $derivedInfoDataType = '';
  /**
   * Required. The description of the job, which typically includes a multi-
   * paragraph description of the company and related information. Separate
   * fields are provided on the job object for responsibilities, qualifications,
   * and other job characteristics. Use of these separate job fields is
   * recommended. This field accepts and sanitizes HTML input, and also accepts
   * bold, italic, ordered list, and unordered list markup tags. The maximum
   * number of allowed characters is 100,000.
   *
   * @var string
   */
  public $description;
  /**
   * The employment type(s) of a job, for example, full time or part time.
   *
   * @var string[]
   */
  public $employmentTypes;
  /**
   * A description of bonus, commission, and other compensation incentives
   * associated with the job not including salary or pay. The maximum number of
   * allowed characters is 10,000.
   *
   * @var string
   */
  public $incentives;
  /**
   * The benefits included with the job.
   *
   * @var string[]
   */
  public $jobBenefits;
  /**
   * The end timestamp of the job. Typically this field is used for contracting
   * engagements. Invalid timestamps are ignored.
   *
   * @var string
   */
  public $jobEndTime;
  /**
   * The experience level associated with the job, such as "Entry Level".
   *
   * @var string
   */
  public $jobLevel;
  /**
   * The start timestamp of the job in UTC time zone. Typically this field is
   * used for contracting engagements. Invalid timestamps are ignored.
   *
   * @var string
   */
  public $jobStartTime;
  /**
   * The language of the posting. This field is distinct from any requirements
   * for fluency that are associated with the job. Language codes must be in
   * BCP-47 format, such as "en-US" or "sr-Latn". For more information, see
   * [Tags for Identifying Languages](https://tools.ietf.org/html/bcp47){:
   * class="external" target="_blank" }. If this field is unspecified and
   * Job.description is present, detected language code based on Job.description
   * is assigned, otherwise defaults to 'en_US'.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Required during job update. The resource name for the job. This is
   * generated by the service when a job is created. The format is
   * "projects/{project_id}/tenants/{tenant_id}/jobs/{job_id}". For example,
   * "projects/foo/tenants/bar/jobs/baz". Use of this field in job queries and
   * API calls is preferred over the use of requisition_id since this value is
   * unique.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when this job posting was created.
   *
   * @var string
   */
  public $postingCreateTime;
  /**
   * Strongly recommended for the best service experience. The expiration
   * timestamp of the job. After this timestamp, the job is marked as expired,
   * and it no longer appears in search results. The expired job can't be listed
   * by the ListJobs API, but it can be retrieved with the GetJob API or updated
   * with the UpdateJob API or deleted with the DeleteJob API. An expired job
   * can be updated and opened again by using a future expiration timestamp.
   * Updating an expired job fails if there is another existing open job with
   * same company, language_code and requisition_id. The expired jobs are
   * retained in our system for 90 days. However, the overall expired job count
   * cannot exceed 3 times the maximum number of open jobs over previous 7 days.
   * If this threshold is exceeded, expired jobs are cleaned out in order of
   * earliest expire time. Expired jobs are no longer accessible after they are
   * cleaned out. Invalid timestamps are ignored, and treated as expire time not
   * provided. If the timestamp is before the instant request is made, the job
   * is treated as expired immediately on creation. This kind of job can not be
   * updated. And when creating a job with past timestamp, the
   * posting_publish_time must be set before posting_expire_time. The purpose of
   * this feature is to allow other objects, such as ApplicationInfo, to refer a
   * job that didn't exist in the system prior to becoming expired. If you want
   * to modify a job that was expired on creation, delete it and create a new
   * one. If this value isn't provided at the time of job creation or is
   * invalid, the job posting expires after 30 days from the job's creation
   * time. For example, if the job was created on 2017/01/01 13:00AM UTC with an
   * unspecified expiration date, the job expires after 2017/01/31 13:00AM UTC.
   * If this value isn't provided on job update, it depends on the field masks
   * set by UpdateJobRequest.update_mask. If the field masks include
   * job_end_time, or the masks are empty meaning that every field is updated,
   * the job posting expires after 30 days from the job's last update time.
   * Otherwise the expiration date isn't updated.
   *
   * @var string
   */
  public $postingExpireTime;
  /**
   * The timestamp this job posting was most recently published. The default
   * value is the time the request arrives at the server. Invalid timestamps are
   * ignored.
   *
   * @var string
   */
  public $postingPublishTime;
  /**
   * The job PostingRegion (for example, state, country) throughout which the
   * job is available. If this field is set, a LocationFilter in a search query
   * within the job region finds this job posting if an exact location match
   * isn't specified. If this field is set to PostingRegion.NATION or
   * PostingRegion.ADMINISTRATIVE_AREA, setting job Job.addresses to the same
   * location level as this field is strongly recommended.
   *
   * @var string
   */
  public $postingRegion;
  /**
   * Output only. The timestamp when this job posting was last updated.
   *
   * @var string
   */
  public $postingUpdateTime;
  protected $processingOptionsType = ProcessingOptions::class;
  protected $processingOptionsDataType = '';
  /**
   * A promotion value of the job, as determined by the client. The value
   * determines the sort order of the jobs returned when searching for jobs
   * using the featured jobs search call, with higher promotional values being
   * returned first and ties being resolved by relevance sort. Only the jobs
   * with a promotionValue >0 are returned in a FEATURED_JOB_SEARCH. Default
   * value is 0, and negative values are treated as 0.
   *
   * @var int
   */
  public $promotionValue;
  /**
   * A description of the qualifications required to perform the job. The use of
   * this field is recommended as an alternative to using the more general
   * description field. This field accepts and sanitizes HTML input, and also
   * accepts bold, italic, ordered list, and unordered list markup tags. The
   * maximum number of allowed characters is 10,000.
   *
   * @var string
   */
  public $qualifications;
  /**
   * Required. The requisition ID, also referred to as the posting ID, is
   * assigned by the client to identify a job. This field is intended to be used
   * by clients for client identification and tracking of postings. A job isn't
   * allowed to be created if there is another job with the same company,
   * language_code and requisition_id. The maximum number of allowed characters
   * is 255.
   *
   * @var string
   */
  public $requisitionId;
  /**
   * A description of job responsibilities. The use of this field is recommended
   * as an alternative to using the more general description field. This field
   * accepts and sanitizes HTML input, and also accepts bold, italic, ordered
   * list, and unordered list markup tags. The maximum number of allowed
   * characters is 10,000.
   *
   * @var string
   */
  public $responsibilities;
  /**
   * Required. The title of the job, such as "Software Engineer" The maximum
   * number of allowed characters is 500.
   *
   * @var string
   */
  public $title;
  /**
   * Deprecated. The job is only visible to the owner. The visibility of the
   * job. Defaults to Visibility.ACCOUNT_ONLY if not specified.
   *
   * @deprecated
   * @var string
   */
  public $visibility;

  /**
   * Strongly recommended for the best service experience. Location(s) where the
   * employer is looking to hire for this job posting. Specifying the full
   * street address(es) of the hiring location enables better API results,
   * especially job searches by commute time. At most 50 locations are allowed
   * for best search performance. If a job has more locations, it is suggested
   * to split it into multiple jobs with unique requisition_ids (e.g. 'ReqA'
   * becomes 'ReqA-1', 'ReqA-2', and so on.) as multiple jobs with the same
   * company, language_code and requisition_id are not allowed. If the original
   * requisition_id must be preserved, a custom field should be used for
   * storage. It is also suggested to group the locations that close to each
   * other in the same job for better search experience. Jobs with multiple
   * addresses must have their addresses with the same LocationType to allow
   * location filtering to work properly. (For example, a Job with addresses
   * "1600 Amphitheatre Parkway, Mountain View, CA, USA" and "London, UK" may
   * not have location filters applied correctly at search time since the first
   * is a LocationType.STREET_ADDRESS and the second is a
   * LocationType.LOCALITY.) If a job needs to have multiple addresses, it is
   * suggested to split it into multiple jobs with same LocationTypes. The
   * maximum number of allowed characters is 500.
   *
   * @param string[] $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return string[]
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * Job application information.
   *
   * @param ApplicationInfo $applicationInfo
   */
  public function setApplicationInfo(ApplicationInfo $applicationInfo)
  {
    $this->applicationInfo = $applicationInfo;
  }
  /**
   * @return ApplicationInfo
   */
  public function getApplicationInfo()
  {
    return $this->applicationInfo;
  }
  /**
   * Required. The resource name of the company listing the job. The format is
   * "projects/{project_id}/tenants/{tenant_id}/companies/{company_id}". For
   * example, "projects/foo/tenants/bar/companies/baz".
   *
   * @param string $company
   */
  public function setCompany($company)
  {
    $this->company = $company;
  }
  /**
   * @return string
   */
  public function getCompany()
  {
    return $this->company;
  }
  /**
   * Output only. Display name of the company listing the job.
   *
   * @param string $companyDisplayName
   */
  public function setCompanyDisplayName($companyDisplayName)
  {
    $this->companyDisplayName = $companyDisplayName;
  }
  /**
   * @return string
   */
  public function getCompanyDisplayName()
  {
    return $this->companyDisplayName;
  }
  /**
   * Job compensation information (a.k.a. "pay rate") i.e., the compensation
   * that will paid to the employee.
   *
   * @param CompensationInfo $compensationInfo
   */
  public function setCompensationInfo(CompensationInfo $compensationInfo)
  {
    $this->compensationInfo = $compensationInfo;
  }
  /**
   * @return CompensationInfo
   */
  public function getCompensationInfo()
  {
    return $this->compensationInfo;
  }
  /**
   * A map of fields to hold both filterable and non-filterable custom job
   * attributes that are not covered by the provided structured fields. The keys
   * of the map are strings up to 64 bytes and must match the pattern:
   * `a-zA-Z*`. For example, key0LikeThis or KEY_1_LIKE_THIS. At most 100
   * filterable and at most 100 unfilterable keys are supported. For filterable
   * `string_values`, across all keys at most 200 values are allowed, with each
   * string no more than 255 characters. For unfilterable `string_values`, the
   * maximum total size of `string_values` across all keys is 50KB.
   *
   * @param CustomAttribute[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return CustomAttribute[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * The desired education degrees for the job, such as Bachelors, Masters.
   *
   * @param string[] $degreeTypes
   */
  public function setDegreeTypes($degreeTypes)
  {
    $this->degreeTypes = $degreeTypes;
  }
  /**
   * @return string[]
   */
  public function getDegreeTypes()
  {
    return $this->degreeTypes;
  }
  /**
   * The department or functional area within the company with the open
   * position. The maximum number of allowed characters is 255.
   *
   * @param string $department
   */
  public function setDepartment($department)
  {
    $this->department = $department;
  }
  /**
   * @return string
   */
  public function getDepartment()
  {
    return $this->department;
  }
  /**
   * Output only. Derived details about the job posting.
   *
   * @param JobDerivedInfo $derivedInfo
   */
  public function setDerivedInfo(JobDerivedInfo $derivedInfo)
  {
    $this->derivedInfo = $derivedInfo;
  }
  /**
   * @return JobDerivedInfo
   */
  public function getDerivedInfo()
  {
    return $this->derivedInfo;
  }
  /**
   * Required. The description of the job, which typically includes a multi-
   * paragraph description of the company and related information. Separate
   * fields are provided on the job object for responsibilities, qualifications,
   * and other job characteristics. Use of these separate job fields is
   * recommended. This field accepts and sanitizes HTML input, and also accepts
   * bold, italic, ordered list, and unordered list markup tags. The maximum
   * number of allowed characters is 100,000.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The employment type(s) of a job, for example, full time or part time.
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
   * A description of bonus, commission, and other compensation incentives
   * associated with the job not including salary or pay. The maximum number of
   * allowed characters is 10,000.
   *
   * @param string $incentives
   */
  public function setIncentives($incentives)
  {
    $this->incentives = $incentives;
  }
  /**
   * @return string
   */
  public function getIncentives()
  {
    return $this->incentives;
  }
  /**
   * The benefits included with the job.
   *
   * @param string[] $jobBenefits
   */
  public function setJobBenefits($jobBenefits)
  {
    $this->jobBenefits = $jobBenefits;
  }
  /**
   * @return string[]
   */
  public function getJobBenefits()
  {
    return $this->jobBenefits;
  }
  /**
   * The end timestamp of the job. Typically this field is used for contracting
   * engagements. Invalid timestamps are ignored.
   *
   * @param string $jobEndTime
   */
  public function setJobEndTime($jobEndTime)
  {
    $this->jobEndTime = $jobEndTime;
  }
  /**
   * @return string
   */
  public function getJobEndTime()
  {
    return $this->jobEndTime;
  }
  /**
   * The experience level associated with the job, such as "Entry Level".
   *
   * Accepted values: JOB_LEVEL_UNSPECIFIED, ENTRY_LEVEL, EXPERIENCED, MANAGER,
   * DIRECTOR, EXECUTIVE
   *
   * @param self::JOB_LEVEL_* $jobLevel
   */
  public function setJobLevel($jobLevel)
  {
    $this->jobLevel = $jobLevel;
  }
  /**
   * @return self::JOB_LEVEL_*
   */
  public function getJobLevel()
  {
    return $this->jobLevel;
  }
  /**
   * The start timestamp of the job in UTC time zone. Typically this field is
   * used for contracting engagements. Invalid timestamps are ignored.
   *
   * @param string $jobStartTime
   */
  public function setJobStartTime($jobStartTime)
  {
    $this->jobStartTime = $jobStartTime;
  }
  /**
   * @return string
   */
  public function getJobStartTime()
  {
    return $this->jobStartTime;
  }
  /**
   * The language of the posting. This field is distinct from any requirements
   * for fluency that are associated with the job. Language codes must be in
   * BCP-47 format, such as "en-US" or "sr-Latn". For more information, see
   * [Tags for Identifying Languages](https://tools.ietf.org/html/bcp47){:
   * class="external" target="_blank" }. If this field is unspecified and
   * Job.description is present, detected language code based on Job.description
   * is assigned, otherwise defaults to 'en_US'.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Required during job update. The resource name for the job. This is
   * generated by the service when a job is created. The format is
   * "projects/{project_id}/tenants/{tenant_id}/jobs/{job_id}". For example,
   * "projects/foo/tenants/bar/jobs/baz". Use of this field in job queries and
   * API calls is preferred over the use of requisition_id since this value is
   * unique.
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
   * Output only. The timestamp when this job posting was created.
   *
   * @param string $postingCreateTime
   */
  public function setPostingCreateTime($postingCreateTime)
  {
    $this->postingCreateTime = $postingCreateTime;
  }
  /**
   * @return string
   */
  public function getPostingCreateTime()
  {
    return $this->postingCreateTime;
  }
  /**
   * Strongly recommended for the best service experience. The expiration
   * timestamp of the job. After this timestamp, the job is marked as expired,
   * and it no longer appears in search results. The expired job can't be listed
   * by the ListJobs API, but it can be retrieved with the GetJob API or updated
   * with the UpdateJob API or deleted with the DeleteJob API. An expired job
   * can be updated and opened again by using a future expiration timestamp.
   * Updating an expired job fails if there is another existing open job with
   * same company, language_code and requisition_id. The expired jobs are
   * retained in our system for 90 days. However, the overall expired job count
   * cannot exceed 3 times the maximum number of open jobs over previous 7 days.
   * If this threshold is exceeded, expired jobs are cleaned out in order of
   * earliest expire time. Expired jobs are no longer accessible after they are
   * cleaned out. Invalid timestamps are ignored, and treated as expire time not
   * provided. If the timestamp is before the instant request is made, the job
   * is treated as expired immediately on creation. This kind of job can not be
   * updated. And when creating a job with past timestamp, the
   * posting_publish_time must be set before posting_expire_time. The purpose of
   * this feature is to allow other objects, such as ApplicationInfo, to refer a
   * job that didn't exist in the system prior to becoming expired. If you want
   * to modify a job that was expired on creation, delete it and create a new
   * one. If this value isn't provided at the time of job creation or is
   * invalid, the job posting expires after 30 days from the job's creation
   * time. For example, if the job was created on 2017/01/01 13:00AM UTC with an
   * unspecified expiration date, the job expires after 2017/01/31 13:00AM UTC.
   * If this value isn't provided on job update, it depends on the field masks
   * set by UpdateJobRequest.update_mask. If the field masks include
   * job_end_time, or the masks are empty meaning that every field is updated,
   * the job posting expires after 30 days from the job's last update time.
   * Otherwise the expiration date isn't updated.
   *
   * @param string $postingExpireTime
   */
  public function setPostingExpireTime($postingExpireTime)
  {
    $this->postingExpireTime = $postingExpireTime;
  }
  /**
   * @return string
   */
  public function getPostingExpireTime()
  {
    return $this->postingExpireTime;
  }
  /**
   * The timestamp this job posting was most recently published. The default
   * value is the time the request arrives at the server. Invalid timestamps are
   * ignored.
   *
   * @param string $postingPublishTime
   */
  public function setPostingPublishTime($postingPublishTime)
  {
    $this->postingPublishTime = $postingPublishTime;
  }
  /**
   * @return string
   */
  public function getPostingPublishTime()
  {
    return $this->postingPublishTime;
  }
  /**
   * The job PostingRegion (for example, state, country) throughout which the
   * job is available. If this field is set, a LocationFilter in a search query
   * within the job region finds this job posting if an exact location match
   * isn't specified. If this field is set to PostingRegion.NATION or
   * PostingRegion.ADMINISTRATIVE_AREA, setting job Job.addresses to the same
   * location level as this field is strongly recommended.
   *
   * Accepted values: POSTING_REGION_UNSPECIFIED, ADMINISTRATIVE_AREA, NATION,
   * TELECOMMUTE
   *
   * @param self::POSTING_REGION_* $postingRegion
   */
  public function setPostingRegion($postingRegion)
  {
    $this->postingRegion = $postingRegion;
  }
  /**
   * @return self::POSTING_REGION_*
   */
  public function getPostingRegion()
  {
    return $this->postingRegion;
  }
  /**
   * Output only. The timestamp when this job posting was last updated.
   *
   * @param string $postingUpdateTime
   */
  public function setPostingUpdateTime($postingUpdateTime)
  {
    $this->postingUpdateTime = $postingUpdateTime;
  }
  /**
   * @return string
   */
  public function getPostingUpdateTime()
  {
    return $this->postingUpdateTime;
  }
  /**
   * Options for job processing.
   *
   * @param ProcessingOptions $processingOptions
   */
  public function setProcessingOptions(ProcessingOptions $processingOptions)
  {
    $this->processingOptions = $processingOptions;
  }
  /**
   * @return ProcessingOptions
   */
  public function getProcessingOptions()
  {
    return $this->processingOptions;
  }
  /**
   * A promotion value of the job, as determined by the client. The value
   * determines the sort order of the jobs returned when searching for jobs
   * using the featured jobs search call, with higher promotional values being
   * returned first and ties being resolved by relevance sort. Only the jobs
   * with a promotionValue >0 are returned in a FEATURED_JOB_SEARCH. Default
   * value is 0, and negative values are treated as 0.
   *
   * @param int $promotionValue
   */
  public function setPromotionValue($promotionValue)
  {
    $this->promotionValue = $promotionValue;
  }
  /**
   * @return int
   */
  public function getPromotionValue()
  {
    return $this->promotionValue;
  }
  /**
   * A description of the qualifications required to perform the job. The use of
   * this field is recommended as an alternative to using the more general
   * description field. This field accepts and sanitizes HTML input, and also
   * accepts bold, italic, ordered list, and unordered list markup tags. The
   * maximum number of allowed characters is 10,000.
   *
   * @param string $qualifications
   */
  public function setQualifications($qualifications)
  {
    $this->qualifications = $qualifications;
  }
  /**
   * @return string
   */
  public function getQualifications()
  {
    return $this->qualifications;
  }
  /**
   * Required. The requisition ID, also referred to as the posting ID, is
   * assigned by the client to identify a job. This field is intended to be used
   * by clients for client identification and tracking of postings. A job isn't
   * allowed to be created if there is another job with the same company,
   * language_code and requisition_id. The maximum number of allowed characters
   * is 255.
   *
   * @param string $requisitionId
   */
  public function setRequisitionId($requisitionId)
  {
    $this->requisitionId = $requisitionId;
  }
  /**
   * @return string
   */
  public function getRequisitionId()
  {
    return $this->requisitionId;
  }
  /**
   * A description of job responsibilities. The use of this field is recommended
   * as an alternative to using the more general description field. This field
   * accepts and sanitizes HTML input, and also accepts bold, italic, ordered
   * list, and unordered list markup tags. The maximum number of allowed
   * characters is 10,000.
   *
   * @param string $responsibilities
   */
  public function setResponsibilities($responsibilities)
  {
    $this->responsibilities = $responsibilities;
  }
  /**
   * @return string
   */
  public function getResponsibilities()
  {
    return $this->responsibilities;
  }
  /**
   * Required. The title of the job, such as "Software Engineer" The maximum
   * number of allowed characters is 500.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Deprecated. The job is only visible to the owner. The visibility of the
   * job. Defaults to Visibility.ACCOUNT_ONLY if not specified.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, ACCOUNT_ONLY, SHARED_WITH_GOOGLE,
   * SHARED_WITH_PUBLIC
   *
   * @deprecated
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @deprecated
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Job::class, 'Google_Service_CloudTalentSolution_Job');
