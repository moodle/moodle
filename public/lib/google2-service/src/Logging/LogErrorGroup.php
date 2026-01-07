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

namespace Google\Service\Logging;

class LogErrorGroup extends \Google\Model
{
  /**
   * The id is a unique identifier for a particular error group; it is the last
   * part of the error group resource name:
   * /project/[PROJECT_ID]/errors/[ERROR_GROUP_ID]. Example: COShysOX0r_51QE.
   * The id is derived from key parts of the error-log content and is treated as
   * Service Data. For information about how Service Data is handled, see Google
   * Cloud Privacy Notice (https://cloud.google.com/terms/cloud-privacy-notice).
   *
   * @var string
   */
  public $id;

  /**
   * The id is a unique identifier for a particular error group; it is the last
   * part of the error group resource name:
   * /project/[PROJECT_ID]/errors/[ERROR_GROUP_ID]. Example: COShysOX0r_51QE.
   * The id is derived from key parts of the error-log content and is treated as
   * Service Data. For information about how Service Data is handled, see Google
   * Cloud Privacy Notice (https://cloud.google.com/terms/cloud-privacy-notice).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogErrorGroup::class, 'Google_Service_Logging_LogErrorGroup');
