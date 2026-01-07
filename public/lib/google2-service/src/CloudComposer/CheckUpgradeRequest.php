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

namespace Google\Service\CloudComposer;

class CheckUpgradeRequest extends \Google\Model
{
  /**
   * Optional. The version of the software running in the environment. This
   * encapsulates both the version of Cloud Composer functionality and the
   * version of Apache Airflow. It must match the regular expression `composer-
   * ([0-9]+(\.[0-9]+\.[0-9]+(-preview\.[0-9]+)?)?|latest)-airflow-([0-9]+(\.[0-
   * 9]+(\.[0-9]+)?)?)`. When used as input, the server also checks if the
   * provided version is supported and denies the request for an unsupported
   * version. The Cloud Composer portion of the image version is a full
   * [semantic version](https://semver.org), or an alias in the form of major
   * version number or `latest`. When an alias is provided, the server replaces
   * it with the current Cloud Composer version that satisfies the alias. The
   * Apache Airflow portion of the image version is a full semantic version that
   * points to one of the supported Apache Airflow versions, or an alias in the
   * form of only major or major.minor versions specified. When an alias is
   * provided, the server replaces it with the latest Apache Airflow version
   * that satisfies the alias and is supported in the given Cloud Composer
   * version. In all cases, the resolved image version is stored in the same
   * field. See also [version list](/composer/docs/concepts/versioning/composer-
   * versions) and [versioning
   * overview](/composer/docs/concepts/versioning/composer-versioning-overview).
   *
   * @var string
   */
  public $imageVersion;

  /**
   * Optional. The version of the software running in the environment. This
   * encapsulates both the version of Cloud Composer functionality and the
   * version of Apache Airflow. It must match the regular expression `composer-
   * ([0-9]+(\.[0-9]+\.[0-9]+(-preview\.[0-9]+)?)?|latest)-airflow-([0-9]+(\.[0-
   * 9]+(\.[0-9]+)?)?)`. When used as input, the server also checks if the
   * provided version is supported and denies the request for an unsupported
   * version. The Cloud Composer portion of the image version is a full
   * [semantic version](https://semver.org), or an alias in the form of major
   * version number or `latest`. When an alias is provided, the server replaces
   * it with the current Cloud Composer version that satisfies the alias. The
   * Apache Airflow portion of the image version is a full semantic version that
   * points to one of the supported Apache Airflow versions, or an alias in the
   * form of only major or major.minor versions specified. When an alias is
   * provided, the server replaces it with the latest Apache Airflow version
   * that satisfies the alias and is supported in the given Cloud Composer
   * version. In all cases, the resolved image version is stored in the same
   * field. See also [version list](/composer/docs/concepts/versioning/composer-
   * versions) and [versioning
   * overview](/composer/docs/concepts/versioning/composer-versioning-overview).
   *
   * @param string $imageVersion
   */
  public function setImageVersion($imageVersion)
  {
    $this->imageVersion = $imageVersion;
  }
  /**
   * @return string
   */
  public function getImageVersion()
  {
    return $this->imageVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckUpgradeRequest::class, 'Google_Service_CloudComposer_CheckUpgradeRequest');
