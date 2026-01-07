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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource extends \Google\Model
{
  /**
   * Regex matching branches to build. The syntax of the regular expressions
   * accepted is the syntax accepted by RE2 and described at
   * https://github.com/google/re2/wiki/Syntax
   *
   * @var string
   */
  public $branchName;
  /**
   * Explicit commit SHA to build.
   *
   * @var string
   */
  public $commitSha;
  /**
   * Optional. Directory, relative to the source root, in which to run the
   * build. This must be a relative path. If a step's `dir` is specified and is
   * an absolute path, this value is ignored for that step's execution.
   *
   * @var string
   */
  public $dir;
  /**
   * Optional. Only trigger a build if the revision regex does NOT match the
   * revision regex.
   *
   * @var bool
   */
  public $invertRegex;
  /**
   * Optional. ID of the project that owns the Cloud Source Repository. If
   * omitted, the project ID requesting the build is assumed.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. Name of the Cloud Source Repository.
   *
   * @var string
   */
  public $repoName;
  /**
   * Optional. Substitutions to use in a triggered build. Should only be used
   * with RunBuildTrigger
   *
   * @var string[]
   */
  public $substitutions;
  /**
   * Regex matching tags to build. The syntax of the regular expressions
   * accepted is the syntax accepted by RE2 and described at
   * https://github.com/google/re2/wiki/Syntax
   *
   * @var string
   */
  public $tagName;

  /**
   * Regex matching branches to build. The syntax of the regular expressions
   * accepted is the syntax accepted by RE2 and described at
   * https://github.com/google/re2/wiki/Syntax
   *
   * @param string $branchName
   */
  public function setBranchName($branchName)
  {
    $this->branchName = $branchName;
  }
  /**
   * @return string
   */
  public function getBranchName()
  {
    return $this->branchName;
  }
  /**
   * Explicit commit SHA to build.
   *
   * @param string $commitSha
   */
  public function setCommitSha($commitSha)
  {
    $this->commitSha = $commitSha;
  }
  /**
   * @return string
   */
  public function getCommitSha()
  {
    return $this->commitSha;
  }
  /**
   * Optional. Directory, relative to the source root, in which to run the
   * build. This must be a relative path. If a step's `dir` is specified and is
   * an absolute path, this value is ignored for that step's execution.
   *
   * @param string $dir
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
  }
  /**
   * @return string
   */
  public function getDir()
  {
    return $this->dir;
  }
  /**
   * Optional. Only trigger a build if the revision regex does NOT match the
   * revision regex.
   *
   * @param bool $invertRegex
   */
  public function setInvertRegex($invertRegex)
  {
    $this->invertRegex = $invertRegex;
  }
  /**
   * @return bool
   */
  public function getInvertRegex()
  {
    return $this->invertRegex;
  }
  /**
   * Optional. ID of the project that owns the Cloud Source Repository. If
   * omitted, the project ID requesting the build is assumed.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. Name of the Cloud Source Repository.
   *
   * @param string $repoName
   */
  public function setRepoName($repoName)
  {
    $this->repoName = $repoName;
  }
  /**
   * @return string
   */
  public function getRepoName()
  {
    return $this->repoName;
  }
  /**
   * Optional. Substitutions to use in a triggered build. Should only be used
   * with RunBuildTrigger
   *
   * @param string[] $substitutions
   */
  public function setSubstitutions($substitutions)
  {
    $this->substitutions = $substitutions;
  }
  /**
   * @return string[]
   */
  public function getSubstitutions()
  {
    return $this->substitutions;
  }
  /**
   * Regex matching tags to build. The syntax of the regular expressions
   * accepted is the syntax accepted by RE2 and described at
   * https://github.com/google/re2/wiki/Syntax
   *
   * @param string $tagName
   */
  public function setTagName($tagName)
  {
    $this->tagName = $tagName;
  }
  /**
   * @return string
   */
  public function getTagName()
  {
    return $this->tagName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1RepoSource');
