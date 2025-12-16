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

namespace Google\Service\CloudBuild;

class RepositoryEventConfig extends \Google\Model
{
  protected $pullRequestType = PullRequestFilter::class;
  protected $pullRequestDataType = '';
  protected $pushType = PushFilter::class;
  protected $pushDataType = '';
  /**
   * @var string
   */
  public $repository;
  /**
   * @var string
   */
  public $repositoryType;

  /**
   * @param PullRequestFilter
   */
  public function setPullRequest(PullRequestFilter $pullRequest)
  {
    $this->pullRequest = $pullRequest;
  }
  /**
   * @return PullRequestFilter
   */
  public function getPullRequest()
  {
    return $this->pullRequest;
  }
  /**
   * @param PushFilter
   */
  public function setPush(PushFilter $push)
  {
    $this->push = $push;
  }
  /**
   * @return PushFilter
   */
  public function getPush()
  {
    return $this->push;
  }
  /**
   * @param string
   */
  public function setRepository($repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return string
   */
  public function getRepository()
  {
    return $this->repository;
  }
  /**
   * @param string
   */
  public function setRepositoryType($repositoryType)
  {
    $this->repositoryType = $repositoryType;
  }
  /**
   * @return string
   */
  public function getRepositoryType()
  {
    return $this->repositoryType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepositoryEventConfig::class, 'Google_Service_CloudBuild_RepositoryEventConfig');
