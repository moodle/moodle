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

namespace Google\Service\Script;

class ScriptFile extends \Google\Model
{
  /**
   * Undetermined file type; never actually used.
   */
  public const TYPE_ENUM_TYPE_UNSPECIFIED = 'ENUM_TYPE_UNSPECIFIED';
  /**
   * An Apps Script server-side code file.
   */
  public const TYPE_SERVER_JS = 'SERVER_JS';
  /**
   * A file containing client-side HTML.
   */
  public const TYPE_HTML = 'HTML';
  /**
   * A file in JSON format. This type is only used for the script project's
   * manifest. The manifest file content must match the structure of a valid
   * [ScriptManifest](/apps-script/concepts/manifests)
   */
  public const TYPE_JSON = 'JSON';
  /**
   * Creation date timestamp.
   *
   * @var string
   */
  public $createTime;
  protected $functionSetType = GoogleAppsScriptTypeFunctionSet::class;
  protected $functionSetDataType = '';
  protected $lastModifyUserType = GoogleAppsScriptTypeUser::class;
  protected $lastModifyUserDataType = '';
  /**
   * The name of the file. The file extension is not part of the file name,
   * which can be identified from the type field.
   *
   * @var string
   */
  public $name;
  /**
   * The file content.
   *
   * @var string
   */
  public $source;
  /**
   * The type of the file.
   *
   * @var string
   */
  public $type;
  /**
   * Last modified date timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Creation date timestamp.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The defined set of functions in the script file, if any.
   *
   * @param GoogleAppsScriptTypeFunctionSet $functionSet
   */
  public function setFunctionSet(GoogleAppsScriptTypeFunctionSet $functionSet)
  {
    $this->functionSet = $functionSet;
  }
  /**
   * @return GoogleAppsScriptTypeFunctionSet
   */
  public function getFunctionSet()
  {
    return $this->functionSet;
  }
  /**
   * The user who modified the file most recently. The details visible in this
   * object are controlled by the profile visibility settings of the last
   * modifying user.
   *
   * @param GoogleAppsScriptTypeUser $lastModifyUser
   */
  public function setLastModifyUser(GoogleAppsScriptTypeUser $lastModifyUser)
  {
    $this->lastModifyUser = $lastModifyUser;
  }
  /**
   * @return GoogleAppsScriptTypeUser
   */
  public function getLastModifyUser()
  {
    return $this->lastModifyUser;
  }
  /**
   * The name of the file. The file extension is not part of the file name,
   * which can be identified from the type field.
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
   * The file content.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The type of the file.
   *
   * Accepted values: ENUM_TYPE_UNSPECIFIED, SERVER_JS, HTML, JSON
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Last modified date timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScriptFile::class, 'Google_Service_Script_ScriptFile');
