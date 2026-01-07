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

namespace Google\Service\Dataform;

class ReleaseConfig extends \Google\Collection
{
  protected $collection_key = 'recentScheduledReleaseRecords';
  protected $codeCompilationConfigType = CodeCompilationConfig::class;
  protected $codeCompilationConfigDataType = '';
  /**
   * Optional. Optional schedule (in cron format) for automatic creation of
   * compilation results.
   *
   * @var string
   */
  public $cronSchedule;
  /**
   * Optional. Disables automatic creation of compilation results.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Required. Git commit/tag/branch name at which the repository should be
   * compiled. Must exist in the remote repository. Examples: - a commit SHA:
   * `12ade345` - a tag: `tag1` - a branch name: `branch1`
   *
   * @var string
   */
  public $gitCommitish;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  /**
   * Identifier. The release config's name.
   *
   * @var string
   */
  public $name;
  protected $recentScheduledReleaseRecordsType = ScheduledReleaseRecord::class;
  protected $recentScheduledReleaseRecordsDataType = 'array';
  /**
   * Optional. The name of the currently released compilation result for this
   * release config. This value is updated when a compilation result is
   * automatically created from this release config (using cron_schedule), or
   * when this resource is updated by API call (perhaps to roll back to an
   * earlier release). The compilation result must have been created using this
   * release config. Must be in the format
   * `projects/locations/repositories/compilationResults`.
   *
   * @var string
   */
  public $releaseCompilationResult;
  /**
   * Optional. Specifies the time zone to be used when interpreting
   * cron_schedule. Must be a time zone name from the time zone database
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). If left
   * unspecified, the default is UTC.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Optional. If set, fields of `code_compilation_config` override the default
   * compilation settings that are specified in dataform.json.
   *
   * @param CodeCompilationConfig $codeCompilationConfig
   */
  public function setCodeCompilationConfig(CodeCompilationConfig $codeCompilationConfig)
  {
    $this->codeCompilationConfig = $codeCompilationConfig;
  }
  /**
   * @return CodeCompilationConfig
   */
  public function getCodeCompilationConfig()
  {
    return $this->codeCompilationConfig;
  }
  /**
   * Optional. Optional schedule (in cron format) for automatic creation of
   * compilation results.
   *
   * @param string $cronSchedule
   */
  public function setCronSchedule($cronSchedule)
  {
    $this->cronSchedule = $cronSchedule;
  }
  /**
   * @return string
   */
  public function getCronSchedule()
  {
    return $this->cronSchedule;
  }
  /**
   * Optional. Disables automatic creation of compilation results.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. Git commit/tag/branch name at which the repository should be
   * compiled. Must exist in the remote repository. Examples: - a commit SHA:
   * `12ade345` - a tag: `tag1` - a branch name: `branch1`
   *
   * @param string $gitCommitish
   */
  public function setGitCommitish($gitCommitish)
  {
    $this->gitCommitish = $gitCommitish;
  }
  /**
   * @return string
   */
  public function getGitCommitish()
  {
    return $this->gitCommitish;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Identifier. The release config's name.
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
   * Output only. Records of the 10 most recent scheduled release attempts,
   * ordered in descending order of `release_time`. Updated whenever automatic
   * creation of a compilation result is triggered by cron_schedule.
   *
   * @param ScheduledReleaseRecord[] $recentScheduledReleaseRecords
   */
  public function setRecentScheduledReleaseRecords($recentScheduledReleaseRecords)
  {
    $this->recentScheduledReleaseRecords = $recentScheduledReleaseRecords;
  }
  /**
   * @return ScheduledReleaseRecord[]
   */
  public function getRecentScheduledReleaseRecords()
  {
    return $this->recentScheduledReleaseRecords;
  }
  /**
   * Optional. The name of the currently released compilation result for this
   * release config. This value is updated when a compilation result is
   * automatically created from this release config (using cron_schedule), or
   * when this resource is updated by API call (perhaps to roll back to an
   * earlier release). The compilation result must have been created using this
   * release config. Must be in the format
   * `projects/locations/repositories/compilationResults`.
   *
   * @param string $releaseCompilationResult
   */
  public function setReleaseCompilationResult($releaseCompilationResult)
  {
    $this->releaseCompilationResult = $releaseCompilationResult;
  }
  /**
   * @return string
   */
  public function getReleaseCompilationResult()
  {
    return $this->releaseCompilationResult;
  }
  /**
   * Optional. Specifies the time zone to be used when interpreting
   * cron_schedule. Must be a time zone name from the time zone database
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). If left
   * unspecified, the default is UTC.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReleaseConfig::class, 'Google_Service_Dataform_ReleaseConfig');
