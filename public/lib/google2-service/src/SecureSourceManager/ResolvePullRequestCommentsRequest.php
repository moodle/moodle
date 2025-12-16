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

namespace Google\Service\SecureSourceManager;

class ResolvePullRequestCommentsRequest extends \Google\Collection
{
  protected $collection_key = 'names';
  /**
   * Optional. If set, at least one comment in a thread is required, rest of the
   * comments in the same thread will be automatically updated to resolved. If
   * unset, all comments in the same thread need be present.
   *
   * @var bool
   */
  public $autoFill;
  /**
   * Required. The names of the pull request comments to resolve. Format: `proje
   * cts/{project_number}/locations/{location_id}/repositories/{repository_id}/p
   * ullRequests/{pull_request_id}/pullRequestComments/{comment_id}` Only
   * comments from the same threads are allowed in the same request.
   *
   * @var string[]
   */
  public $names;

  /**
   * Optional. If set, at least one comment in a thread is required, rest of the
   * comments in the same thread will be automatically updated to resolved. If
   * unset, all comments in the same thread need be present.
   *
   * @param bool $autoFill
   */
  public function setAutoFill($autoFill)
  {
    $this->autoFill = $autoFill;
  }
  /**
   * @return bool
   */
  public function getAutoFill()
  {
    return $this->autoFill;
  }
  /**
   * Required. The names of the pull request comments to resolve. Format: `proje
   * cts/{project_number}/locations/{location_id}/repositories/{repository_id}/p
   * ullRequests/{pull_request_id}/pullRequestComments/{comment_id}` Only
   * comments from the same threads are allowed in the same request.
   *
   * @param string[] $names
   */
  public function setNames($names)
  {
    $this->names = $names;
  }
  /**
   * @return string[]
   */
  public function getNames()
  {
    return $this->names;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResolvePullRequestCommentsRequest::class, 'Google_Service_SecureSourceManager_ResolvePullRequestCommentsRequest');
