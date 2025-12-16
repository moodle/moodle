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

class Source extends \Google\Model
{
  /**
   * The canonical name of the finding source. It's either
   * "organizations/{organization_id}/sources/{source_id}",
   * "folders/{folder_id}/sources/{source_id}", or
   * "projects/{project_number}/sources/{source_id}", depending on the closest
   * CRM ancestor of the resource associated with the finding.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * The description of the source (max of 1024 characters). Example: "Web
   * Security Scanner is a web security scanner for common vulnerabilities in
   * App Engine applications. It can automatically scan and detect four common
   * vulnerabilities, including cross-site-scripting (XSS), Flash injection,
   * mixed content (HTTP in HTTPS), and outdated or insecure libraries."
   *
   * @var string
   */
  public $description;
  /**
   * The source's display name. A source's display name must be unique amongst
   * its siblings, for example, two sources with the same parent can't share the
   * same display name. The display name must have a length between 1 and 64
   * characters (inclusive).
   *
   * @var string
   */
  public $displayName;
  /**
   * The relative resource name of this source. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/sources/{source_id}"
   *
   * @var string
   */
  public $name;

  /**
   * The canonical name of the finding source. It's either
   * "organizations/{organization_id}/sources/{source_id}",
   * "folders/{folder_id}/sources/{source_id}", or
   * "projects/{project_number}/sources/{source_id}", depending on the closest
   * CRM ancestor of the resource associated with the finding.
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * The description of the source (max of 1024 characters). Example: "Web
   * Security Scanner is a web security scanner for common vulnerabilities in
   * App Engine applications. It can automatically scan and detect four common
   * vulnerabilities, including cross-site-scripting (XSS), Flash injection,
   * mixed content (HTTP in HTTPS), and outdated or insecure libraries."
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The source's display name. A source's display name must be unique amongst
   * its siblings, for example, two sources with the same parent can't share the
   * same display name. The display name must have a length between 1 and 64
   * characters (inclusive).
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
   * The relative resource name of this source. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/sources/{source_id}"
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
class_alias(Source::class, 'Google_Service_SecurityCommandCenter_Source');
