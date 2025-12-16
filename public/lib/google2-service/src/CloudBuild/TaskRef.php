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

class TaskRef extends \Google\Collection
{
  /**
   * Default enum type; should not be used.
   */
  public const RESOLVER_RESOLVER_NAME_UNSPECIFIED = 'RESOLVER_NAME_UNSPECIFIED';
  /**
   * Bundles resolver. https://tekton.dev/docs/pipelines/bundle-resolver/
   */
  public const RESOLVER_BUNDLES = 'BUNDLES';
  /**
   * GCB repo resolver.
   */
  public const RESOLVER_GCB_REPO = 'GCB_REPO';
  /**
   * Simple Git resolver. https://tekton.dev/docs/pipelines/git-resolver/
   */
  public const RESOLVER_GIT = 'GIT';
  /**
   * Developer Connect resolver.
   */
  public const RESOLVER_DEVELOPER_CONNECT = 'DEVELOPER_CONNECT';
  /**
   * Default resolver.
   */
  public const RESOLVER_DEFAULT = 'DEFAULT';
  protected $collection_key = 'params';
  /**
   * Optional. Name of the task.
   *
   * @var string
   */
  public $name;
  protected $paramsType = Param::class;
  protected $paramsDataType = 'array';
  /**
   * Resolver is the name of the resolver that should perform resolution of the
   * referenced Tekton resource.
   *
   * @var string
   */
  public $resolver;

  /**
   * Optional. Name of the task.
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
   * Params contains the parameters used to identify the referenced Tekton
   * resource. Example entries might include "repo" or "path" but the set of
   * params ultimately depends on the chosen resolver.
   *
   * @param Param[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return Param[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Resolver is the name of the resolver that should perform resolution of the
   * referenced Tekton resource.
   *
   * Accepted values: RESOLVER_NAME_UNSPECIFIED, BUNDLES, GCB_REPO, GIT,
   * DEVELOPER_CONNECT, DEFAULT
   *
   * @param self::RESOLVER_* $resolver
   */
  public function setResolver($resolver)
  {
    $this->resolver = $resolver;
  }
  /**
   * @return self::RESOLVER_*
   */
  public function getResolver()
  {
    return $this->resolver;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskRef::class, 'Google_Service_CloudBuild_TaskRef');
