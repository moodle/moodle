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

namespace Google\Service\ManagedKafka;

class UpdateSchemaModeRequest extends \Google\Model
{
  /**
   * The default / unset value. The subject mode is NONE/unset by default, which
   * means use the global schema registry mode. This should not be used for
   * setting the mode.
   */
  public const MODE_NONE = 'NONE';
  /**
   * READONLY mode.
   */
  public const MODE_READONLY = 'READONLY';
  /**
   * READWRITE mode.
   */
  public const MODE_READWRITE = 'READWRITE';
  /**
   * IMPORT mode.
   */
  public const MODE_IMPORT = 'IMPORT';
  /**
   * Required. The mode type.
   *
   * @var string
   */
  public $mode;

  /**
   * Required. The mode type.
   *
   * Accepted values: NONE, READONLY, READWRITE, IMPORT
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSchemaModeRequest::class, 'Google_Service_ManagedKafka_UpdateSchemaModeRequest');
