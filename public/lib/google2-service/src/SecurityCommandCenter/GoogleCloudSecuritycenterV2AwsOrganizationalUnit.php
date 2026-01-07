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

class GoogleCloudSecuritycenterV2AwsOrganizationalUnit extends \Google\Model
{
  /**
   * The unique identifier (ID) associated with this OU. The regex pattern for
   * an organizational unit ID string requires "ou-" followed by from 4 to 32
   * lowercase letters or digits (the ID of the root that contains the OU). This
   * string is followed by a second "-" dash and from 8 to 32 additional
   * lowercase letters or digits. For example, "ou-ab12-cd34ef56".
   *
   * @var string
   */
  public $id;
  /**
   * The friendly name of the OU.
   *
   * @var string
   */
  public $name;

  /**
   * The unique identifier (ID) associated with this OU. The regex pattern for
   * an organizational unit ID string requires "ou-" followed by from 4 to 32
   * lowercase letters or digits (the ID of the root that contains the OU). This
   * string is followed by a second "-" dash and from 8 to 32 additional
   * lowercase letters or digits. For example, "ou-ab12-cd34ef56".
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The friendly name of the OU.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2AwsOrganizationalUnit::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2AwsOrganizationalUnit');
