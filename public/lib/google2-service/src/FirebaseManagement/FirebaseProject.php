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

namespace Google\Service\FirebaseManagement;

class FirebaseProject extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Project is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The Project has been soft-deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * A set of user-defined annotations for the FirebaseProject. Learn more about
   * annotations in Google's [AIP-128
   * standard](https://google.aip.dev/128#annotations). These annotations are
   * intended solely for developers and client-side tools. Firebase services
   * will not mutate this annotations set.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * The user-assigned display name of the Project.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and it may be sent with update requests to ensure the client has an up-to-
   * date value before proceeding. Learn more about `etag` in Google's [AIP-154
   * standard](https://google.aip.dev/154#declarative-friendly-resources). This
   * etag is strongly validated.
   *
   * @var string
   */
  public $etag;
  /**
   * The resource name of the Project, in the format:
   * projects/PROJECT_IDENTIFIER PROJECT_IDENTIFIER: the Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Immutable. A user-assigned unique identifier for the Project.
   * This identifier may appear in URLs or names for some Firebase resources
   * associated with the Project, but it should generally be treated as a
   * convenience alias to reference the Project.
   *
   * @var string
   */
  public $projectId;
  /**
   * Output only. Immutable. The globally unique, Google-assigned canonical
   * identifier for the Project. Use this identifier when configuring
   * integrations and/or making API calls to Firebase or third-party services.
   *
   * @var string
   */
  public $projectNumber;
  protected $resourcesType = DefaultResources::class;
  protected $resourcesDataType = '';
  /**
   * Output only. The lifecycle state of the Project.
   *
   * @var string
   */
  public $state;

  /**
   * A set of user-defined annotations for the FirebaseProject. Learn more about
   * annotations in Google's [AIP-128
   * standard](https://google.aip.dev/128#annotations). These annotations are
   * intended solely for developers and client-side tools. Firebase services
   * will not mutate this annotations set.
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
   * The user-assigned display name of the Project.
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
   * This checksum is computed by the server based on the value of other fields,
   * and it may be sent with update requests to ensure the client has an up-to-
   * date value before proceeding. Learn more about `etag` in Google's [AIP-154
   * standard](https://google.aip.dev/154#declarative-friendly-resources). This
   * etag is strongly validated.
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
   * The resource name of the Project, in the format:
   * projects/PROJECT_IDENTIFIER PROJECT_IDENTIFIER: the Project's
   * [`ProjectNumber`](../projects#FirebaseProject.FIELDS.project_number)
   * ***(recommended)*** or its
   * [`ProjectId`](../projects#FirebaseProject.FIELDS.project_id). Learn more
   * about using project identifiers in Google's [AIP 2510
   * standard](https://google.aip.dev/cloud/2510). Note that the value for
   * PROJECT_IDENTIFIER in any response body will be the `ProjectId`.
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
   * Output only. Immutable. A user-assigned unique identifier for the Project.
   * This identifier may appear in URLs or names for some Firebase resources
   * associated with the Project, but it should generally be treated as a
   * convenience alias to reference the Project.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Output only. Immutable. The globally unique, Google-assigned canonical
   * identifier for the Project. Use this identifier when configuring
   * integrations and/or making API calls to Firebase or third-party services.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * Output only. **DEPRECATED.** _Auto-provisioning of these resources is
   * changing, so this object no longer reliably provides information about the
   * Project. Instead, retrieve information about each resource directly from
   * its resource-specific API._ The default Firebase resources associated with
   * the Project.
   *
   * @deprecated
   * @param DefaultResources $resources
   */
  public function setResources(DefaultResources $resources)
  {
    $this->resources = $resources;
  }
  /**
   * @deprecated
   * @return DefaultResources
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Output only. The lifecycle state of the Project.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirebaseProject::class, 'Google_Service_FirebaseManagement_FirebaseProject');
