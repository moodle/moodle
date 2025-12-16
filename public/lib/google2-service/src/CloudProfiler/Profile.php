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

namespace Google\Service\CloudProfiler;

class Profile extends \Google\Model
{
  /**
   * Unspecified profile type.
   */
  public const PROFILE_TYPE_PROFILE_TYPE_UNSPECIFIED = 'PROFILE_TYPE_UNSPECIFIED';
  /**
   * Thread CPU time sampling.
   */
  public const PROFILE_TYPE_CPU = 'CPU';
  /**
   * Wallclock time sampling. More expensive as stops all threads.
   */
  public const PROFILE_TYPE_WALL = 'WALL';
  /**
   * In-use heap profile. Represents a snapshot of the allocations that are live
   * at the time of the profiling.
   */
  public const PROFILE_TYPE_HEAP = 'HEAP';
  /**
   * Single-shot collection of all thread stacks.
   */
  public const PROFILE_TYPE_THREADS = 'THREADS';
  /**
   * Synchronization contention profile.
   */
  public const PROFILE_TYPE_CONTENTION = 'CONTENTION';
  /**
   * Peak heap profile.
   */
  public const PROFILE_TYPE_PEAK_HEAP = 'PEAK_HEAP';
  /**
   * Heap allocation profile. It represents the aggregation of all allocations
   * made over the duration of the profile. All allocations are included,
   * including those that might have been freed by the end of the profiling
   * interval. The profile is in particular useful for garbage collecting
   * languages to understand which parts of the code create most of the garbage
   * collection pressure to see if those can be optimized.
   */
  public const PROFILE_TYPE_HEAP_ALLOC = 'HEAP_ALLOC';
  protected $deploymentType = Deployment::class;
  protected $deploymentDataType = '';
  /**
   * Duration of the profiling session. Input (for the offline mode) or output
   * (for the online mode). The field represents requested profiling duration.
   * It may slightly differ from the effective profiling duration, which is
   * recorded in the profile data, in case the profiling can't be stopped
   * immediately (e.g. in case stopping the profiling is handled
   * asynchronously).
   *
   * @var string
   */
  public $duration;
  /**
   * Input only. Labels associated to this specific profile. These labels will
   * get merged with the deployment labels for the final data set. See
   * documentation on deployment labels for validation rules and limits.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Opaque, server-assigned, unique ID for this profile.
   *
   * @var string
   */
  public $name;
  /**
   * Input only. Profile bytes, as a gzip compressed serialized proto, the
   * format is https://github.com/google/pprof/blob/master/proto/profile.proto.
   *
   * @var string
   */
  public $profileBytes;
  /**
   * Type of profile. For offline mode, this must be specified when creating the
   * profile. For online mode it is assigned and returned by the server.
   *
   * @var string
   */
  public $profileType;
  /**
   * Output only. Start time for the profile. This output is only present in
   * response from the ListProfiles method.
   *
   * @var string
   */
  public $startTime;

  /**
   * Deployment this profile corresponds to.
   *
   * @param Deployment $deployment
   */
  public function setDeployment(Deployment $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return Deployment
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * Duration of the profiling session. Input (for the offline mode) or output
   * (for the online mode). The field represents requested profiling duration.
   * It may slightly differ from the effective profiling duration, which is
   * recorded in the profile data, in case the profiling can't be stopped
   * immediately (e.g. in case stopping the profiling is handled
   * asynchronously).
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Input only. Labels associated to this specific profile. These labels will
   * get merged with the deployment labels for the final data set. See
   * documentation on deployment labels for validation rules and limits.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Opaque, server-assigned, unique ID for this profile.
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
   * Input only. Profile bytes, as a gzip compressed serialized proto, the
   * format is https://github.com/google/pprof/blob/master/proto/profile.proto.
   *
   * @param string $profileBytes
   */
  public function setProfileBytes($profileBytes)
  {
    $this->profileBytes = $profileBytes;
  }
  /**
   * @return string
   */
  public function getProfileBytes()
  {
    return $this->profileBytes;
  }
  /**
   * Type of profile. For offline mode, this must be specified when creating the
   * profile. For online mode it is assigned and returned by the server.
   *
   * Accepted values: PROFILE_TYPE_UNSPECIFIED, CPU, WALL, HEAP, THREADS,
   * CONTENTION, PEAK_HEAP, HEAP_ALLOC
   *
   * @param self::PROFILE_TYPE_* $profileType
   */
  public function setProfileType($profileType)
  {
    $this->profileType = $profileType;
  }
  /**
   * @return self::PROFILE_TYPE_*
   */
  public function getProfileType()
  {
    return $this->profileType;
  }
  /**
   * Output only. Start time for the profile. This output is only present in
   * response from the ListProfiles method.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Profile::class, 'Google_Service_CloudProfiler_Profile');
