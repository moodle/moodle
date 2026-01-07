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

namespace Google\Service\BigtableAdmin;

class AuthorizedView extends \Google\Model
{
  /**
   * Set to true to make the AuthorizedView protected against deletion. The
   * parent Table and containing Instance cannot be deleted if an AuthorizedView
   * has this bit set.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * The etag for this AuthorizedView. If this is provided on update, it must
   * match the server's etag. The server returns ABORTED error on a mismatched
   * etag.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The name of this AuthorizedView. Values are of the form `projec
   * ts/{project}/instances/{instance}/tables/{table}/authorizedViews/{authorize
   * d_view}`
   *
   * @var string
   */
  public $name;
  protected $subsetViewType = GoogleBigtableAdminV2AuthorizedViewSubsetView::class;
  protected $subsetViewDataType = '';

  /**
   * Set to true to make the AuthorizedView protected against deletion. The
   * parent Table and containing Instance cannot be deleted if an AuthorizedView
   * has this bit set.
   *
   * @param bool $deletionProtection
   */
  public function setDeletionProtection($deletionProtection)
  {
    $this->deletionProtection = $deletionProtection;
  }
  /**
   * @return bool
   */
  public function getDeletionProtection()
  {
    return $this->deletionProtection;
  }
  /**
   * The etag for this AuthorizedView. If this is provided on update, it must
   * match the server's etag. The server returns ABORTED error on a mismatched
   * etag.
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
   * Identifier. The name of this AuthorizedView. Values are of the form `projec
   * ts/{project}/instances/{instance}/tables/{table}/authorizedViews/{authorize
   * d_view}`
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
   * An AuthorizedView permitting access to an explicit subset of a Table.
   *
   * @param GoogleBigtableAdminV2AuthorizedViewSubsetView $subsetView
   */
  public function setSubsetView(GoogleBigtableAdminV2AuthorizedViewSubsetView $subsetView)
  {
    $this->subsetView = $subsetView;
  }
  /**
   * @return GoogleBigtableAdminV2AuthorizedViewSubsetView
   */
  public function getSubsetView()
  {
    return $this->subsetView;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizedView::class, 'Google_Service_BigtableAdmin_AuthorizedView');
