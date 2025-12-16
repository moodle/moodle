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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for DriveLabels (v2).
 *
 * <p>
 * An API for managing Drive Labels</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/workspace/drive/labels" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class DriveLabels extends \Google\Service
{
  /** See, edit, create, and delete all Google Drive labels in your organization, and see your organization's label-related admin policies. */
  const DRIVE_ADMIN_LABELS =
      "https://www.googleapis.com/auth/drive.admin.labels";
  /** See all Google Drive labels and label-related admin policies in your organization. */
  const DRIVE_ADMIN_LABELS_READONLY =
      "https://www.googleapis.com/auth/drive.admin.labels.readonly";
  /** See, edit, create, and delete your Google Drive labels. */
  const DRIVE_LABELS =
      "https://www.googleapis.com/auth/drive.labels";
  /** See your Google Drive labels. */
  const DRIVE_LABELS_READONLY =
      "https://www.googleapis.com/auth/drive.labels.readonly";

  public $labels;
  public $labels_locks;
  public $labels_permissions;
  public $labels_revisions;
  public $labels_revisions_locks;
  public $labels_revisions_permissions;
  public $limits;
  public $users;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the DriveLabels service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://drivelabels.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://drivelabels.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v2';
    $this->serviceName = 'drivelabels';

    $this->labels = new DriveLabels\Resource\Labels(
        $this,
        $this->serviceName,
        'labels',
        [
          'methods' => [
            'create' => [
              'path' => 'v2/labels',
              'httpMethod' => 'POST',
              'parameters' => [
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'delete' => [
              'path' => 'v2/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'writeControl.requiredRevisionId' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'delta' => [
              'path' => 'v2/{+name}:delta',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'disable' => [
              'path' => 'v2/{+name}:disable',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'enable' => [
              'path' => 'v2/{+name}:enable',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v2/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v2/labels',
              'httpMethod' => 'GET',
              'parameters' => [
                'customer' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'languageCode' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'minimumRole' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'publishedOnly' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'publish' => [
              'path' => 'v2/{+name}:publish',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'updateLabelCopyMode' => [
              'path' => 'v2/{+name}:updateLabelCopyMode',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'updateLabelEnabledAppSettings' => [
              'path' => 'v2/{+name}:updateLabelEnabledAppSettings',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'updatePermissions' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
    $this->labels_locks = new DriveLabels\Resource\LabelsLocks(
        $this,
        $this->serviceName,
        'locks',
        [
          'methods' => [
            'list' => [
              'path' => 'v2/{+parent}/locks',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->labels_permissions = new DriveLabels\Resource\LabelsPermissions(
        $this,
        $this->serviceName,
        'permissions',
        [
          'methods' => [
            'batchDelete' => [
              'path' => 'v2/{+parent}/permissions:batchDelete',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'batchUpdate' => [
              'path' => 'v2/{+parent}/permissions:batchUpdate',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'create' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'delete' => [
              'path' => 'v2/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'list' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
    $this->labels_revisions = new DriveLabels\Resource\LabelsRevisions(
        $this,
        $this->serviceName,
        'revisions',
        [
          'methods' => [
            'updatePermissions' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
    $this->labels_revisions_locks = new DriveLabels\Resource\LabelsRevisionsLocks(
        $this,
        $this->serviceName,
        'locks',
        [
          'methods' => [
            'list' => [
              'path' => 'v2/{+parent}/locks',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->labels_revisions_permissions = new DriveLabels\Resource\LabelsRevisionsPermissions(
        $this,
        $this->serviceName,
        'permissions',
        [
          'methods' => [
            'batchDelete' => [
              'path' => 'v2/{+parent}/permissions:batchDelete',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'batchUpdate' => [
              'path' => 'v2/{+parent}/permissions:batchUpdate',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'create' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'POST',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'delete' => [
              'path' => 'v2/{+name}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],'list' => [
              'path' => 'v2/{+parent}/permissions',
              'httpMethod' => 'GET',
              'parameters' => [
                'parent' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'useAdminAccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
    $this->limits = new DriveLabels\Resource\Limits(
        $this,
        $this->serviceName,
        'limits',
        [
          'methods' => [
            'getLabel' => [
              'path' => 'v2/limits/label',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->users = new DriveLabels\Resource\Users(
        $this,
        $this->serviceName,
        'users',
        [
          'methods' => [
            'getCapabilities' => [
              'path' => 'v2/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'customer' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveLabels::class, 'Google_Service_DriveLabels');
