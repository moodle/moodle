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

namespace Google\Service\SecurityCommandCenter;

class SecurityPolicy extends \Google\Model
{
  /**
   * The name of the Google Cloud Armor security policy, for example, "my-
   * security-policy".
   *
   * @var string
   */
  public $name;
  /**
   * Whether or not the associated rule or policy is in preview mode.
   *
   * @var bool
   */
  public $preview;
  /**
   * The type of Google Cloud Armor security policy for example, 'backend
   * security policy', 'edge security policy', 'network edge security policy',
   * or 'always-on DDoS protection'.
   *
   * @var string
   */
  public $type;

  /**
   * The name of the Google Cloud Armor security policy, for example, "my-
   * security-policy".
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
   * Whether or not the associated rule or policy is in preview mode.
   *
   * @param bool $preview
   */
  public function setPreview($preview)
  {
    $this->preview = $preview;
  }
  /**
   * @return bool
   */
  public function getPreview()
  {
    return $this->preview;
  }
  /**
   * The type of Google Cloud Armor security policy for example, 'backend
   * security policy', 'edge security policy', 'network edge security policy',
   * or 'always-on DDoS protection'.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityPolicy::class, 'Google_Service_SecurityCommandCenter_SecurityPolicy');
