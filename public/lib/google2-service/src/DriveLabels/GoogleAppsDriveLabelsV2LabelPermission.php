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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2LabelPermission extends \Google\Model
{
  /**
   * Unknown role.
   */
  public const ROLE_LABEL_ROLE_UNSPECIFIED = 'LABEL_ROLE_UNSPECIFIED';
  /**
   * A reader can read the label and associated metadata applied to Drive items.
   */
  public const ROLE_READER = 'READER';
  /**
   * An applier can write associated metadata on Drive items in which they also
   * have write access to. Implies `READER`.
   */
  public const ROLE_APPLIER = 'APPLIER';
  /**
   * An organizer can pin this label in shared drives they manage and add new
   * appliers to the label.
   */
  public const ROLE_ORGANIZER = 'ORGANIZER';
  /**
   * Editors can make any update including deleting the label which also deletes
   * the associated Drive item metadata. Implies `APPLIER`.
   */
  public const ROLE_EDITOR = 'EDITOR';
  /**
   * Audience to grant a role to. The magic value of `audiences/default` may be
   * used to apply the role to the default audience in the context of the
   * organization that owns the label.
   *
   * @var string
   */
  public $audience;
  /**
   * Specifies the email address for a user or group principal. Not populated
   * for audience principals. User and group permissions may only be inserted
   * using an email address. On update requests, if email address is specified,
   * no principal should be specified.
   *
   * @var string
   */
  public $email;
  /**
   * Group resource name.
   *
   * @var string
   */
  public $group;
  /**
   * Resource name of this permission.
   *
   * @var string
   */
  public $name;
  /**
   * Person resource name.
   *
   * @var string
   */
  public $person;
  /**
   * The role the principal should have.
   *
   * @var string
   */
  public $role;

  /**
   * Audience to grant a role to. The magic value of `audiences/default` may be
   * used to apply the role to the default audience in the context of the
   * organization that owns the label.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * Specifies the email address for a user or group principal. Not populated
   * for audience principals. User and group permissions may only be inserted
   * using an email address. On update requests, if email address is specified,
   * no principal should be specified.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Group resource name.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Resource name of this permission.
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
   * Person resource name.
   *
   * @param string $person
   */
  public function setPerson($person)
  {
    $this->person = $person;
  }
  /**
   * @return string
   */
  public function getPerson()
  {
    return $this->person;
  }
  /**
   * The role the principal should have.
   *
   * Accepted values: LABEL_ROLE_UNSPECIFIED, READER, APPLIER, ORGANIZER, EDITOR
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2LabelPermission::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2LabelPermission');
