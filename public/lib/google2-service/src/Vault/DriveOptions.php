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

namespace Google\Service\Vault;

class DriveOptions extends \Google\Model
{
  /**
   * Encryption status unspecified. Results include both client-side encrypted
   * and non-encrypted content.
   */
  public const CLIENT_SIDE_ENCRYPTED_OPTION_CLIENT_SIDE_ENCRYPTED_OPTION_UNSPECIFIED = 'CLIENT_SIDE_ENCRYPTED_OPTION_UNSPECIFIED';
  /**
   * Include both client-side encrypted and unencrypted content in results.
   */
  public const CLIENT_SIDE_ENCRYPTED_OPTION_CLIENT_SIDE_ENCRYPTED_OPTION_ANY = 'CLIENT_SIDE_ENCRYPTED_OPTION_ANY';
  /**
   * Include client-side encrypted content only.
   */
  public const CLIENT_SIDE_ENCRYPTED_OPTION_CLIENT_SIDE_ENCRYPTED_OPTION_ENCRYPTED = 'CLIENT_SIDE_ENCRYPTED_OPTION_ENCRYPTED';
  /**
   * Include unencrypted content only.
   */
  public const CLIENT_SIDE_ENCRYPTED_OPTION_CLIENT_SIDE_ENCRYPTED_OPTION_UNENCRYPTED = 'CLIENT_SIDE_ENCRYPTED_OPTION_UNENCRYPTED';
  /**
   * No shared drive option specified.
   */
  public const SHARED_DRIVES_OPTION_SHARED_DRIVES_OPTION_UNSPECIFIED = 'SHARED_DRIVES_OPTION_UNSPECIFIED';
  /**
   * If a resource is in a shared drive, it isn't included in the search.
   */
  public const SHARED_DRIVES_OPTION_NOT_INCLUDED = 'NOT_INCLUDED';
  /**
   * Shared drive resources are only included in instances where the account is
   * a collaborator on a resource but they are not a member of the shared drive.
   * This maps to the *"Included only if documents shared directly (not due to
   * shared drive membership)"* option in the Vault UI. (Previously
   * "include_shared_drives" off)
   */
  public const SHARED_DRIVES_OPTION_INCLUDED_IF_ACCOUNT_IS_NOT_A_MEMBER = 'INCLUDED_IF_ACCOUNT_IS_NOT_A_MEMBER';
  /**
   * Resources in shared drives are included in the search. (Previously
   * "include_shared_drives" on)
   */
  public const SHARED_DRIVES_OPTION_INCLUDED = 'INCLUDED';
  /**
   * Set whether the results include only content encrypted with [Google
   * Workspace Client-side encryption](https://support.google.com/a?p=cse_ov)
   * content, only unencrypted content, or both. Defaults to both. Currently
   * supported for Drive.
   *
   * @var string
   */
  public $clientSideEncryptedOption;
  /**
   * Set to **true** to include shared drives.
   *
   * @deprecated
   * @var bool
   */
  public $includeSharedDrives;
  /**
   * Set to true to include Team Drive.
   *
   * @deprecated
   * @var bool
   */
  public $includeTeamDrives;
  /**
   * Optional. Options to include or exclude documents in shared drives. We
   * recommend using this field over include_shared_drives. This field overrides
   * include_shared_drives and include_team_drives when set.
   *
   * @var string
   */
  public $sharedDrivesOption;
  /**
   * Search the current version of the Drive file, but export the contents of
   * the last version saved before 12:00 AM UTC on the specified date. Enter the
   * date in UTC.
   *
   * @var string
   */
  public $versionDate;

  /**
   * Set whether the results include only content encrypted with [Google
   * Workspace Client-side encryption](https://support.google.com/a?p=cse_ov)
   * content, only unencrypted content, or both. Defaults to both. Currently
   * supported for Drive.
   *
   * Accepted values: CLIENT_SIDE_ENCRYPTED_OPTION_UNSPECIFIED,
   * CLIENT_SIDE_ENCRYPTED_OPTION_ANY, CLIENT_SIDE_ENCRYPTED_OPTION_ENCRYPTED,
   * CLIENT_SIDE_ENCRYPTED_OPTION_UNENCRYPTED
   *
   * @param self::CLIENT_SIDE_ENCRYPTED_OPTION_* $clientSideEncryptedOption
   */
  public function setClientSideEncryptedOption($clientSideEncryptedOption)
  {
    $this->clientSideEncryptedOption = $clientSideEncryptedOption;
  }
  /**
   * @return self::CLIENT_SIDE_ENCRYPTED_OPTION_*
   */
  public function getClientSideEncryptedOption()
  {
    return $this->clientSideEncryptedOption;
  }
  /**
   * Set to **true** to include shared drives.
   *
   * @deprecated
   * @param bool $includeSharedDrives
   */
  public function setIncludeSharedDrives($includeSharedDrives)
  {
    $this->includeSharedDrives = $includeSharedDrives;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIncludeSharedDrives()
  {
    return $this->includeSharedDrives;
  }
  /**
   * Set to true to include Team Drive.
   *
   * @deprecated
   * @param bool $includeTeamDrives
   */
  public function setIncludeTeamDrives($includeTeamDrives)
  {
    $this->includeTeamDrives = $includeTeamDrives;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIncludeTeamDrives()
  {
    return $this->includeTeamDrives;
  }
  /**
   * Optional. Options to include or exclude documents in shared drives. We
   * recommend using this field over include_shared_drives. This field overrides
   * include_shared_drives and include_team_drives when set.
   *
   * Accepted values: SHARED_DRIVES_OPTION_UNSPECIFIED, NOT_INCLUDED,
   * INCLUDED_IF_ACCOUNT_IS_NOT_A_MEMBER, INCLUDED
   *
   * @param self::SHARED_DRIVES_OPTION_* $sharedDrivesOption
   */
  public function setSharedDrivesOption($sharedDrivesOption)
  {
    $this->sharedDrivesOption = $sharedDrivesOption;
  }
  /**
   * @return self::SHARED_DRIVES_OPTION_*
   */
  public function getSharedDrivesOption()
  {
    return $this->sharedDrivesOption;
  }
  /**
   * Search the current version of the Drive file, but export the contents of
   * the last version saved before 12:00 AM UTC on the specified date. Enter the
   * date in UTC.
   *
   * @param string $versionDate
   */
  public function setVersionDate($versionDate)
  {
    $this->versionDate = $versionDate;
  }
  /**
   * @return string
   */
  public function getVersionDate()
  {
    return $this->versionDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveOptions::class, 'Google_Service_Vault_DriveOptions');
