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

class CreateRepositoryRequest extends \Google\Model
{
  /**
   * Required. The connection to contain the repository. If the request is part
   * of a BatchCreateRepositoriesRequest, this field should be empty or match
   * the parent specified there.
   *
   * @var string
   */
  public $parent;
  protected $repositoryType = Repository::class;
  protected $repositoryDataType = '';
  /**
   * Required. The ID to use for the repository, which will become the final
   * component of the repository's resource name. This ID should be unique in
   * the connection. Allows alphanumeric characters and any of
   * -._~%!$&'()*+,;=@.
   *
   * @var string
   */
  public $repositoryId;

  /**
   * Required. The connection to contain the repository. If the request is part
   * of a BatchCreateRepositoriesRequest, this field should be empty or match
   * the parent specified there.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The repository to create.
   *
   * @param Repository $repository
   */
  public function setRepository(Repository $repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return Repository
   */
  public function getRepository()
  {
    return $this->repository;
  }
  /**
   * Required. The ID to use for the repository, which will become the final
   * component of the repository's resource name. This ID should be unique in
   * the connection. Allows alphanumeric characters and any of
   * -._~%!$&'()*+,;=@.
   *
   * @param string $repositoryId
   */
  public function setRepositoryId($repositoryId)
  {
    $this->repositoryId = $repositoryId;
  }
  /**
   * @return string
   */
  public function getRepositoryId()
  {
    return $this->repositoryId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateRepositoryRequest::class, 'Google_Service_CloudBuild_CreateRepositoryRequest');
