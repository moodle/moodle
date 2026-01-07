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

namespace Google\Service\Spanner;

class BatchWriteRequest extends \Google\Collection
{
  protected $collection_key = 'mutationGroups';
  /**
   * Optional. If you don't set the `exclude_txn_from_change_streams` option or
   * if it's set to `false`, then any change streams monitoring columns modified
   * by transactions will capture the updates made within that transaction.
   *
   * @var bool
   */
  public $excludeTxnFromChangeStreams;
  protected $mutationGroupsType = MutationGroup::class;
  protected $mutationGroupsDataType = 'array';
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';

  /**
   * Optional. If you don't set the `exclude_txn_from_change_streams` option or
   * if it's set to `false`, then any change streams monitoring columns modified
   * by transactions will capture the updates made within that transaction.
   *
   * @param bool $excludeTxnFromChangeStreams
   */
  public function setExcludeTxnFromChangeStreams($excludeTxnFromChangeStreams)
  {
    $this->excludeTxnFromChangeStreams = $excludeTxnFromChangeStreams;
  }
  /**
   * @return bool
   */
  public function getExcludeTxnFromChangeStreams()
  {
    return $this->excludeTxnFromChangeStreams;
  }
  /**
   * Required. The groups of mutations to be applied.
   *
   * @param MutationGroup[] $mutationGroups
   */
  public function setMutationGroups($mutationGroups)
  {
    $this->mutationGroups = $mutationGroups;
  }
  /**
   * @return MutationGroup[]
   */
  public function getMutationGroups()
  {
    return $this->mutationGroups;
  }
  /**
   * Common options for this request.
   *
   * @param RequestOptions $requestOptions
   */
  public function setRequestOptions(RequestOptions $requestOptions)
  {
    $this->requestOptions = $requestOptions;
  }
  /**
   * @return RequestOptions
   */
  public function getRequestOptions()
  {
    return $this->requestOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchWriteRequest::class, 'Google_Service_Spanner_BatchWriteRequest');
