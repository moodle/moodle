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

namespace Google\Service\Testing;

class SmartSharding extends \Google\Model
{
  /**
   * The amount of time tests within a shard should take. Default: 300 seconds
   * (5 minutes). The minimum allowed: 120 seconds (2 minutes). The shard count
   * is dynamically set based on time, up to the maximum shard limit (described
   * below). To guarantee at least one test case for each shard, the number of
   * shards will not exceed the number of test cases. Shard duration will be
   * exceeded if: - The maximum shard limit is reached and there is more
   * calculated test time remaining to allocate into shards. - Any individual
   * test is estimated to be longer than the targeted shard duration. Shard
   * duration is not guaranteed because smart sharding uses test case history
   * and default durations which may not be accurate. The rules for finding the
   * test case timing records are: - If the service has processed a test case in
   * the last 30 days, the record of the latest successful test case will be
   * used. - For new test cases, the average duration of other known test cases
   * will be used. - If there are no previous test case timing records
   * available, the default test case duration is 15 seconds. Because the actual
   * shard duration can exceed the targeted shard duration, we recommend that
   * you set the targeted value at least 5 minutes less than the maximum allowed
   * test timeout (45 minutes for physical devices and 60 minutes for virtual),
   * or that you use the custom test timeout value that you set. This approach
   * avoids cancelling the shard before all tests can finish. Note that there is
   * a limit for maximum number of shards. When you select one or more physical
   * devices, the number of shards must be <= 50. When you select one or more
   * ARM virtual devices, it must be <= 200. When you select only x86 virtual
   * devices, it must be <= 500. To guarantee at least one test case for per
   * shard, the number of shards will not exceed the number of test cases. Each
   * shard created counts toward daily test quota.
   *
   * @var string
   */
  public $targetedShardDuration;

  /**
   * The amount of time tests within a shard should take. Default: 300 seconds
   * (5 minutes). The minimum allowed: 120 seconds (2 minutes). The shard count
   * is dynamically set based on time, up to the maximum shard limit (described
   * below). To guarantee at least one test case for each shard, the number of
   * shards will not exceed the number of test cases. Shard duration will be
   * exceeded if: - The maximum shard limit is reached and there is more
   * calculated test time remaining to allocate into shards. - Any individual
   * test is estimated to be longer than the targeted shard duration. Shard
   * duration is not guaranteed because smart sharding uses test case history
   * and default durations which may not be accurate. The rules for finding the
   * test case timing records are: - If the service has processed a test case in
   * the last 30 days, the record of the latest successful test case will be
   * used. - For new test cases, the average duration of other known test cases
   * will be used. - If there are no previous test case timing records
   * available, the default test case duration is 15 seconds. Because the actual
   * shard duration can exceed the targeted shard duration, we recommend that
   * you set the targeted value at least 5 minutes less than the maximum allowed
   * test timeout (45 minutes for physical devices and 60 minutes for virtual),
   * or that you use the custom test timeout value that you set. This approach
   * avoids cancelling the shard before all tests can finish. Note that there is
   * a limit for maximum number of shards. When you select one or more physical
   * devices, the number of shards must be <= 50. When you select one or more
   * ARM virtual devices, it must be <= 200. When you select only x86 virtual
   * devices, it must be <= 500. To guarantee at least one test case for per
   * shard, the number of shards will not exceed the number of test cases. Each
   * shard created counts toward daily test quota.
   *
   * @param string $targetedShardDuration
   */
  public function setTargetedShardDuration($targetedShardDuration)
  {
    $this->targetedShardDuration = $targetedShardDuration;
  }
  /**
   * @return string
   */
  public function getTargetedShardDuration()
  {
    return $this->targetedShardDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SmartSharding::class, 'Google_Service_Testing_SmartSharding');
