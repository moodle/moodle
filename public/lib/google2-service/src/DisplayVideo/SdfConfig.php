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

namespace Google\Service\DisplayVideo;

class SdfConfig extends \Google\Model
{
  /**
   * SDF version value is not specified or is unknown in this version.
   */
  public const VERSION_SDF_VERSION_UNSPECIFIED = 'SDF_VERSION_UNSPECIFIED';
  /**
   * SDF version 3.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_3_1 = 'SDF_VERSION_3_1';
  /**
   * SDF version 4
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4 = 'SDF_VERSION_4';
  /**
   * SDF version 4.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4_1 = 'SDF_VERSION_4_1';
  /**
   * SDF version 4.2
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_4_2 = 'SDF_VERSION_4_2';
  /**
   * SDF version 5.
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5 = 'SDF_VERSION_5';
  /**
   * SDF version 5.1
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_1 = 'SDF_VERSION_5_1';
  /**
   * SDF version 5.2
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_2 = 'SDF_VERSION_5_2';
  /**
   * SDF version 5.3
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_3 = 'SDF_VERSION_5_3';
  /**
   * SDF version 5.4
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_4 = 'SDF_VERSION_5_4';
  /**
   * SDF version 5.5
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_5_5 = 'SDF_VERSION_5_5';
  /**
   * SDF version 6
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_6 = 'SDF_VERSION_6';
  /**
   * SDF version 7. Read the [v7 migration guide](/display-video/api/structured-
   * data-file/v7-migration-guide) before migrating to this version.
   *
   * @deprecated
   */
  public const VERSION_SDF_VERSION_7 = 'SDF_VERSION_7';
  /**
   * SDF version 7.1. Read the [v7 migration guide](/display-
   * video/api/structured-data-file/v7-migration-guide) before migrating to this
   * version.
   */
  public const VERSION_SDF_VERSION_7_1 = 'SDF_VERSION_7_1';
  /**
   * SDF version 8. Read the [v8 migration guide](/display-video/api/structured-
   * data-file/v8-migration-guide) before migrating to this version.
   */
  public const VERSION_SDF_VERSION_8 = 'SDF_VERSION_8';
  /**
   * SDF version 8.1.
   */
  public const VERSION_SDF_VERSION_8_1 = 'SDF_VERSION_8_1';
  /**
   * SDF version 9. Read the [v9 migration guide](/display-video/api/structured-
   * data-file/v9-migration-guide) before migrating to this version.
   */
  public const VERSION_SDF_VERSION_9 = 'SDF_VERSION_9';
  /**
   * SDF version 9.1.
   */
  public const VERSION_SDF_VERSION_9_1 = 'SDF_VERSION_9_1';
  /**
   * SDF version 9.2.
   */
  public const VERSION_SDF_VERSION_9_2 = 'SDF_VERSION_9_2';
  /**
   * An administrator email address to which the SDF processing status reports
   * will be sent.
   *
   * @var string
   */
  public $adminEmail;
  /**
   * Required. The version of SDF being used.
   *
   * @var string
   */
  public $version;

  /**
   * An administrator email address to which the SDF processing status reports
   * will be sent.
   *
   * @param string $adminEmail
   */
  public function setAdminEmail($adminEmail)
  {
    $this->adminEmail = $adminEmail;
  }
  /**
   * @return string
   */
  public function getAdminEmail()
  {
    return $this->adminEmail;
  }
  /**
   * Required. The version of SDF being used.
   *
   * Accepted values: SDF_VERSION_UNSPECIFIED, SDF_VERSION_3_1, SDF_VERSION_4,
   * SDF_VERSION_4_1, SDF_VERSION_4_2, SDF_VERSION_5, SDF_VERSION_5_1,
   * SDF_VERSION_5_2, SDF_VERSION_5_3, SDF_VERSION_5_4, SDF_VERSION_5_5,
   * SDF_VERSION_6, SDF_VERSION_7, SDF_VERSION_7_1, SDF_VERSION_8,
   * SDF_VERSION_8_1, SDF_VERSION_9, SDF_VERSION_9_1, SDF_VERSION_9_2
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SdfConfig::class, 'Google_Service_DisplayVideo_SdfConfig');
