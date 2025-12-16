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

namespace Google\Service\Storage;

class BucketLifecycleRuleCondition extends \Google\Collection
{
  protected $collection_key = 'matchesSuffix';
  /**
   * Age of an object (in days). This condition is satisfied when an object
   * reaches the specified age.
   *
   * @var int
   */
  public $age;
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when an object is created before
   * midnight of the specified date in UTC.
   *
   * @var string
   */
  public $createdBefore;
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when the custom time on an
   * object is before this date in UTC.
   *
   * @var string
   */
  public $customTimeBefore;
  /**
   * Number of days elapsed since the user-specified timestamp set on an object.
   * The condition is satisfied if the days elapsed is at least this number. If
   * no custom timestamp is specified on an object, the condition does not
   * apply.
   *
   * @var int
   */
  public $daysSinceCustomTime;
  /**
   * Number of days elapsed since the noncurrent timestamp of an object. The
   * condition is satisfied if the days elapsed is at least this number. This
   * condition is relevant only for versioned objects. The value of the field
   * must be a nonnegative integer. If it's zero, the object version will become
   * eligible for Lifecycle action as soon as it becomes noncurrent.
   *
   * @var int
   */
  public $daysSinceNoncurrentTime;
  /**
   * Relevant only for versioned objects. If the value is true, this condition
   * matches live objects; if the value is false, it matches archived objects.
   *
   * @var bool
   */
  public $isLive;
  /**
   * A regular expression that satisfies the RE2 syntax. This condition is
   * satisfied when the name of the object matches the RE2 pattern. Note: This
   * feature is currently in the "Early Access" launch stage and is only
   * available to a whitelisted set of users; that means that this feature may
   * be changed in backward-incompatible ways and that it is not guaranteed to
   * be released.
   *
   * @var string
   */
  public $matchesPattern;
  /**
   * List of object name prefixes. This condition will be satisfied when at
   * least one of the prefixes exactly matches the beginning of the object name.
   *
   * @var string[]
   */
  public $matchesPrefix;
  /**
   * Objects having any of the storage classes specified by this condition will
   * be matched. Values include MULTI_REGIONAL, REGIONAL, NEARLINE, COLDLINE,
   * ARCHIVE, STANDARD, and DURABLE_REDUCED_AVAILABILITY.
   *
   * @var string[]
   */
  public $matchesStorageClass;
  /**
   * List of object name suffixes. This condition will be satisfied when at
   * least one of the suffixes exactly matches the end of the object name.
   *
   * @var string[]
   */
  public $matchesSuffix;
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when the noncurrent time on an
   * object is before this date in UTC. This condition is relevant only for
   * versioned objects.
   *
   * @var string
   */
  public $noncurrentTimeBefore;
  /**
   * Relevant only for versioned objects. If the value is N, this condition is
   * satisfied when there are at least N versions (including the live version)
   * newer than this version of the object.
   *
   * @var int
   */
  public $numNewerVersions;

  /**
   * Age of an object (in days). This condition is satisfied when an object
   * reaches the specified age.
   *
   * @param int $age
   */
  public function setAge($age)
  {
    $this->age = $age;
  }
  /**
   * @return int
   */
  public function getAge()
  {
    return $this->age;
  }
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when an object is created before
   * midnight of the specified date in UTC.
   *
   * @param string $createdBefore
   */
  public function setCreatedBefore($createdBefore)
  {
    $this->createdBefore = $createdBefore;
  }
  /**
   * @return string
   */
  public function getCreatedBefore()
  {
    return $this->createdBefore;
  }
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when the custom time on an
   * object is before this date in UTC.
   *
   * @param string $customTimeBefore
   */
  public function setCustomTimeBefore($customTimeBefore)
  {
    $this->customTimeBefore = $customTimeBefore;
  }
  /**
   * @return string
   */
  public function getCustomTimeBefore()
  {
    return $this->customTimeBefore;
  }
  /**
   * Number of days elapsed since the user-specified timestamp set on an object.
   * The condition is satisfied if the days elapsed is at least this number. If
   * no custom timestamp is specified on an object, the condition does not
   * apply.
   *
   * @param int $daysSinceCustomTime
   */
  public function setDaysSinceCustomTime($daysSinceCustomTime)
  {
    $this->daysSinceCustomTime = $daysSinceCustomTime;
  }
  /**
   * @return int
   */
  public function getDaysSinceCustomTime()
  {
    return $this->daysSinceCustomTime;
  }
  /**
   * Number of days elapsed since the noncurrent timestamp of an object. The
   * condition is satisfied if the days elapsed is at least this number. This
   * condition is relevant only for versioned objects. The value of the field
   * must be a nonnegative integer. If it's zero, the object version will become
   * eligible for Lifecycle action as soon as it becomes noncurrent.
   *
   * @param int $daysSinceNoncurrentTime
   */
  public function setDaysSinceNoncurrentTime($daysSinceNoncurrentTime)
  {
    $this->daysSinceNoncurrentTime = $daysSinceNoncurrentTime;
  }
  /**
   * @return int
   */
  public function getDaysSinceNoncurrentTime()
  {
    return $this->daysSinceNoncurrentTime;
  }
  /**
   * Relevant only for versioned objects. If the value is true, this condition
   * matches live objects; if the value is false, it matches archived objects.
   *
   * @param bool $isLive
   */
  public function setIsLive($isLive)
  {
    $this->isLive = $isLive;
  }
  /**
   * @return bool
   */
  public function getIsLive()
  {
    return $this->isLive;
  }
  /**
   * A regular expression that satisfies the RE2 syntax. This condition is
   * satisfied when the name of the object matches the RE2 pattern. Note: This
   * feature is currently in the "Early Access" launch stage and is only
   * available to a whitelisted set of users; that means that this feature may
   * be changed in backward-incompatible ways and that it is not guaranteed to
   * be released.
   *
   * @param string $matchesPattern
   */
  public function setMatchesPattern($matchesPattern)
  {
    $this->matchesPattern = $matchesPattern;
  }
  /**
   * @return string
   */
  public function getMatchesPattern()
  {
    return $this->matchesPattern;
  }
  /**
   * List of object name prefixes. This condition will be satisfied when at
   * least one of the prefixes exactly matches the beginning of the object name.
   *
   * @param string[] $matchesPrefix
   */
  public function setMatchesPrefix($matchesPrefix)
  {
    $this->matchesPrefix = $matchesPrefix;
  }
  /**
   * @return string[]
   */
  public function getMatchesPrefix()
  {
    return $this->matchesPrefix;
  }
  /**
   * Objects having any of the storage classes specified by this condition will
   * be matched. Values include MULTI_REGIONAL, REGIONAL, NEARLINE, COLDLINE,
   * ARCHIVE, STANDARD, and DURABLE_REDUCED_AVAILABILITY.
   *
   * @param string[] $matchesStorageClass
   */
  public function setMatchesStorageClass($matchesStorageClass)
  {
    $this->matchesStorageClass = $matchesStorageClass;
  }
  /**
   * @return string[]
   */
  public function getMatchesStorageClass()
  {
    return $this->matchesStorageClass;
  }
  /**
   * List of object name suffixes. This condition will be satisfied when at
   * least one of the suffixes exactly matches the end of the object name.
   *
   * @param string[] $matchesSuffix
   */
  public function setMatchesSuffix($matchesSuffix)
  {
    $this->matchesSuffix = $matchesSuffix;
  }
  /**
   * @return string[]
   */
  public function getMatchesSuffix()
  {
    return $this->matchesSuffix;
  }
  /**
   * A date in RFC 3339 format with only the date part (for instance,
   * "2013-01-15"). This condition is satisfied when the noncurrent time on an
   * object is before this date in UTC. This condition is relevant only for
   * versioned objects.
   *
   * @param string $noncurrentTimeBefore
   */
  public function setNoncurrentTimeBefore($noncurrentTimeBefore)
  {
    $this->noncurrentTimeBefore = $noncurrentTimeBefore;
  }
  /**
   * @return string
   */
  public function getNoncurrentTimeBefore()
  {
    return $this->noncurrentTimeBefore;
  }
  /**
   * Relevant only for versioned objects. If the value is N, this condition is
   * satisfied when there are at least N versions (including the live version)
   * newer than this version of the object.
   *
   * @param int $numNewerVersions
   */
  public function setNumNewerVersions($numNewerVersions)
  {
    $this->numNewerVersions = $numNewerVersions;
  }
  /**
   * @return int
   */
  public function getNumNewerVersions()
  {
    return $this->numNewerVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketLifecycleRuleCondition::class, 'Google_Service_Storage_BucketLifecycleRuleCondition');
