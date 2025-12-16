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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1FirewallPolicy extends \Google\Collection
{
  protected $collection_key = 'actions';
  protected $actionsType = GoogleCloudRecaptchaenterpriseV1FirewallAction::class;
  protected $actionsDataType = 'array';
  /**
   * Optional. A CEL (Common Expression Language) conditional expression that
   * specifies if this policy applies to an incoming user request. If this
   * condition evaluates to true and the requested path matched the path
   * pattern, the associated actions should be executed by the caller. The
   * condition string is checked for CEL syntax correctness on creation. For
   * more information, see the [CEL spec](https://github.com/google/cel-spec)
   * and its [language definition](https://github.com/google/cel-
   * spec/blob/master/doc/langdef.md). A condition has a max length of 500
   * characters.
   *
   * @var string
   */
  public $condition;
  /**
   * Optional. A description of what this policy aims to achieve, for
   * convenience purposes. The description can at most include 256 UTF-8
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Identifier. The resource name for the FirewallPolicy in the format
   * `projects/{project}/firewallpolicies/{firewallpolicy}`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The path for which this policy applies, specified as a glob
   * pattern. For more information on glob, see the [manual
   * page](https://man7.org/linux/man-pages/man7/glob.7.html). A path has a max
   * length of 200 characters.
   *
   * @var string
   */
  public $path;

  /**
   * Optional. The actions that the caller should take regarding user access.
   * There should be at most one terminal action. A terminal action is any
   * action that forces a response, such as `AllowAction`, `BlockAction` or
   * `SubstituteAction`. Zero or more non-terminal actions such as `SetHeader`
   * might be specified. A single policy can contain up to 16 actions.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FirewallAction[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FirewallAction[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Optional. A CEL (Common Expression Language) conditional expression that
   * specifies if this policy applies to an incoming user request. If this
   * condition evaluates to true and the requested path matched the path
   * pattern, the associated actions should be executed by the caller. The
   * condition string is checked for CEL syntax correctness on creation. For
   * more information, see the [CEL spec](https://github.com/google/cel-spec)
   * and its [language definition](https://github.com/google/cel-
   * spec/blob/master/doc/langdef.md). A condition has a max length of 500
   * characters.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Optional. A description of what this policy aims to achieve, for
   * convenience purposes. The description can at most include 256 UTF-8
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Identifier. The resource name for the FirewallPolicy in the format
   * `projects/{project}/firewallpolicies/{firewallpolicy}`.
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
   * Optional. The path for which this policy applies, specified as a glob
   * pattern. For more information on glob, see the [manual
   * page](https://man7.org/linux/man-pages/man7/glob.7.html). A path has a max
   * length of 200 characters.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FirewallPolicy::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FirewallPolicy');
