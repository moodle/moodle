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

namespace Google\Service\ApiKeysService;

class V2Key extends \Google\Model
{
  /**
   * Annotations is an unstructured key-value map stored with a policy that may
   * be set by external tools to store and retrieve arbitrary metadata. They are
   * not queryable and should be preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. A timestamp identifying the time this key was originally
   * created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. A timestamp when this key was deleted. If the resource is not
   * deleted, this must be empty.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Human-readable display name of this key that you can modify. The maximum
   * length is 63 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the Key resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding. See
   * https://google.aip.dev/154.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. An encrypted and signed value held by this key. This field can
   * be accessed only through the `GetKeyString` method.
   *
   * @var string
   */
  public $keyString;
  /**
   * Output only. The resource name of the key. The `name` has the form:
   * `projects//locations/global/keys/`. For example: `projects/123456867718/loc
   * ations/global/keys/b7ff1f9f-8275-410a-94dd-3855ee9b5dd2` NOTE: Key is a
   * global resource; hence the only supported value for location is `global`.
   *
   * @var string
   */
  public $name;
  protected $restrictionsType = V2Restrictions::class;
  protected $restrictionsDataType = '';
  /**
   * Optional. The email address of [the service
   * account](https://cloud.google.com/iam/docs/service-accounts) the key is
   * bound to.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * Output only. Unique id in UUID4 format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. A timestamp identifying the time this key was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Annotations is an unstructured key-value map stored with a policy that may
   * be set by external tools to store and retrieve arbitrary metadata. They are
   * not queryable and should be preserved when modifying objects.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. A timestamp identifying the time this key was originally
   * created.
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
   * Output only. A timestamp when this key was deleted. If the resource is not
   * deleted, this must be empty.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Human-readable display name of this key that you can modify. The maximum
   * length is 63 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the Key resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding. See
   * https://google.aip.dev/154.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. An encrypted and signed value held by this key. This field can
   * be accessed only through the `GetKeyString` method.
   *
   * @param string $keyString
   */
  public function setKeyString($keyString)
  {
    $this->keyString = $keyString;
  }
  /**
   * @return string
   */
  public function getKeyString()
  {
    return $this->keyString;
  }
  /**
   * Output only. The resource name of the key. The `name` has the form:
   * `projects//locations/global/keys/`. For example: `projects/123456867718/loc
   * ations/global/keys/b7ff1f9f-8275-410a-94dd-3855ee9b5dd2` NOTE: Key is a
   * global resource; hence the only supported value for location is `global`.
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
   * Key restrictions.
   *
   * @param V2Restrictions $restrictions
   */
  public function setRestrictions(V2Restrictions $restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return V2Restrictions
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
  /**
   * Optional. The email address of [the service
   * account](https://cloud.google.com/iam/docs/service-accounts) the key is
   * bound to.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * Output only. Unique id in UUID4 format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. A timestamp identifying the time this key was last updated.
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
class_alias(V2Key::class, 'Google_Service_ApiKeysService_V2Key');
