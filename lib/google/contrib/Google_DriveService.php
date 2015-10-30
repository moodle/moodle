<?php
/*
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


  /**
   * The "files" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $files = $driveService->files;
   *  </code>
   */
  class Google_FilesServiceResource extends Google_ServiceResource {


    /**
     * Insert a new file. (files.insert)
     *
     * @param Google_DriveFile $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool convert Whether to convert this file to the corresponding Google Docs format.
     * @opt_param string targetLanguage Target language to translate the file to. If no sourceLanguage is provided, the API will attempt to detect the language.
     * @opt_param string sourceLanguage The language of the original file to be translated.
     * @opt_param string ocrLanguage If ocr is true, hints at the language to use. Valid values are ISO 639-1 codes.
     * @opt_param bool pinned Whether to pin the head revision of the uploaded file.
     * @opt_param bool ocr Whether to attempt OCR on .jpg, .png, or .gif uploads.
     * @opt_param string timedTextTrackName The timed text track name.
     * @opt_param string timedTextLanguage The language of the timed text.
     * @return Google_DriveFile
     */
    public function insert(Google_DriveFile $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Restores a file from the trash. (files.untrash)
     *
     * @param string $fileId The ID of the file to untrash.
     * @param array $optParams Optional parameters.
     * @return Google_DriveFile
     */
    public function untrash($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('untrash', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Moves a file to the trash. (files.trash)
     *
     * @param string $fileId The ID of the file to trash.
     * @param array $optParams Optional parameters.
     * @return Google_DriveFile
     */
    public function trash($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('trash', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a file's metadata by ID. (files.get)
     *
     * @param string $fileId The ID for the file in question.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool updateViewedDate Whether to update the view date after successfully retrieving the file.
     * @opt_param string projection This parameter is deprecated and has no function.
     * @return Google_DriveFile
     */
    public function get($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists the user's files. (files.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string q Query string for searching files.
     * @opt_param string pageToken Page token for files.
     * @opt_param string projection This parameter is deprecated and has no function.
     * @opt_param int maxResults Maximum number of files to return.
     * @return Google_FileList
     */
    public function listFiles($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_FileList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates file metadata and/or content (files.update)
     *
     * @param string $fileId The ID of the file to update.
     * @param Google_DriveFile $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool convert Whether to convert this file to the corresponding Google Docs format.
     * @opt_param string targetLanguage Target language to translate the file to. If no sourceLanguage is provided, the API will attempt to detect the language.
     * @opt_param bool setModifiedDate Whether to set the modified date with the supplied modified date.
     * @opt_param bool updateViewedDate Whether to update the view date after successfully updating the file.
     * @opt_param string sourceLanguage The language of the original file to be translated.
     * @opt_param string ocrLanguage If ocr is true, hints at the language to use. Valid values are ISO 639-1 codes.
     * @opt_param bool pinned Whether to pin the new revision.
     * @opt_param bool newRevision Whether a blob upload should create a new revision. If false, the blob data in the current head revision will be replaced.
     * @opt_param bool ocr Whether to attempt OCR on .jpg, .png, or .gif uploads.
     * @opt_param string timedTextLanguage The language of the timed text.
     * @opt_param string timedTextTrackName The timed text track name.
     * @return Google_DriveFile
     */
    public function update($fileId, Google_DriveFile $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates file metadata and/or content. This method supports patch semantics. (files.patch)
     *
     * @param string $fileId The ID of the file to update.
     * @param Google_DriveFile $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool convert Whether to convert this file to the corresponding Google Docs format.
     * @opt_param string targetLanguage Target language to translate the file to. If no sourceLanguage is provided, the API will attempt to detect the language.
     * @opt_param bool setModifiedDate Whether to set the modified date with the supplied modified date.
     * @opt_param bool updateViewedDate Whether to update the view date after successfully updating the file.
     * @opt_param string sourceLanguage The language of the original file to be translated.
     * @opt_param string ocrLanguage If ocr is true, hints at the language to use. Valid values are ISO 639-1 codes.
     * @opt_param bool pinned Whether to pin the new revision.
     * @opt_param bool newRevision Whether a blob upload should create a new revision. If false, the blob data in the current head revision will be replaced.
     * @opt_param bool ocr Whether to attempt OCR on .jpg, .png, or .gif uploads.
     * @opt_param string timedTextLanguage The language of the timed text.
     * @opt_param string timedTextTrackName The timed text track name.
     * @return Google_DriveFile
     */
    public function patch($fileId, Google_DriveFile $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Set the file's updated time to the current server time. (files.touch)
     *
     * @param string $fileId The ID of the file to update.
     * @param array $optParams Optional parameters.
     * @return Google_DriveFile
     */
    public function touch($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('touch', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates a copy of the specified file. (files.copy)
     *
     * @param string $fileId The ID of the file to copy.
     * @param Google_DriveFile $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool convert Whether to convert this file to the corresponding Google Docs format.
     * @opt_param string targetLanguage Target language to translate the file to. If no sourceLanguage is provided, the API will attempt to detect the language.
     * @opt_param string sourceLanguage The language of the original file to be translated.
     * @opt_param string ocrLanguage If ocr is true, hints at the language to use. Valid values are ISO 639-1 codes.
     * @opt_param bool pinned Whether to pin the head revision of the new copy.
     * @opt_param bool ocr Whether to attempt OCR on .jpg, .png, or .gif uploads.
     * @opt_param string timedTextLanguage The language of the timed text.
     * @opt_param string timedTextTrackName The timed text track name.
     * @return Google_DriveFile
     */
    public function copy($fileId, Google_DriveFile $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('copy', array($params));
      if ($this->useObjects()) {
        return new Google_DriveFile($data);
      } else {
        return $data;
      }
    }
    /**
     * Permanently deletes a file by ID. Skips the trash. (files.delete)
     *
     * @param string $fileId The ID of the file to delete.
     * @param array $optParams Optional parameters.
     */
    public function delete($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "about" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $about = $driveService->about;
   *  </code>
   */
  class Google_AboutServiceResource extends Google_ServiceResource {


    /**
     * Gets the information about the current user along with Drive API settings (about.get)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeSubscribed Whether to include subscribed items when calculating the number of remaining change IDs
     * @opt_param string maxChangeIdCount Maximum number of remaining change IDs to count
     * @opt_param string startChangeId Change ID to start counting from when calculating number of remaining change IDs
     * @return Google_About
     */
    public function get($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_About($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "apps" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $apps = $driveService->apps;
   *  </code>
   */
  class Google_AppsServiceResource extends Google_ServiceResource {


    /**
     * Lists a user's apps. (apps.list)
     *
     * @param array $optParams Optional parameters.
     * @return Google_AppList
     */
    public function listApps($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_AppList($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a specific app. (apps.get)
     *
     * @param string $appId The ID of the app.
     * @param array $optParams Optional parameters.
     * @return Google_App
     */
    public function get($appId, $optParams = array()) {
      $params = array('appId' => $appId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_App($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "parents" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $parents = $driveService->parents;
   *  </code>
   */
  class Google_ParentsServiceResource extends Google_ServiceResource {


    /**
     * Adds a parent folder for a file. (parents.insert)
     *
     * @param string $fileId The ID of the file.
     * @param Google_ParentReference $postBody
     * @param array $optParams Optional parameters.
     * @return Google_ParentReference
     */
    public function insert($fileId, Google_ParentReference $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_ParentReference($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a specific parent reference. (parents.get)
     *
     * @param string $fileId The ID of the file.
     * @param string $parentId The ID of the parent.
     * @param array $optParams Optional parameters.
     * @return Google_ParentReference
     */
    public function get($fileId, $parentId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'parentId' => $parentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_ParentReference($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists a file's parents. (parents.list)
     *
     * @param string $fileId The ID of the file.
     * @param array $optParams Optional parameters.
     * @return Google_ParentList
     */
    public function listParents($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ParentList($data);
      } else {
        return $data;
      }
    }
    /**
     * Removes a parent from a file. (parents.delete)
     *
     * @param string $fileId The ID of the file.
     * @param string $parentId The ID of the parent.
     * @param array $optParams Optional parameters.
     */
    public function delete($fileId, $parentId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'parentId' => $parentId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "revisions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $revisions = $driveService->revisions;
   *  </code>
   */
  class Google_RevisionsServiceResource extends Google_ServiceResource {


    /**
     * Updates a revision. This method supports patch semantics. (revisions.patch)
     *
     * @param string $fileId The ID for the file.
     * @param string $revisionId The ID for the revision.
     * @param Google_Revision $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Revision
     */
    public function patch($fileId, $revisionId, Google_Revision $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'revisionId' => $revisionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Revision($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a specific revision. (revisions.get)
     *
     * @param string $fileId The ID of the file.
     * @param string $revisionId The ID of the revision.
     * @param array $optParams Optional parameters.
     * @return Google_Revision
     */
    public function get($fileId, $revisionId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'revisionId' => $revisionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Revision($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists a file's revisions. (revisions.list)
     *
     * @param string $fileId The ID of the file.
     * @param array $optParams Optional parameters.
     * @return Google_RevisionList
     */
    public function listRevisions($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_RevisionList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a revision. (revisions.update)
     *
     * @param string $fileId The ID for the file.
     * @param string $revisionId The ID for the revision.
     * @param Google_Revision $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Revision
     */
    public function update($fileId, $revisionId, Google_Revision $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'revisionId' => $revisionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Revision($data);
      } else {
        return $data;
      }
    }
    /**
     * Removes a revision. (revisions.delete)
     *
     * @param string $fileId The ID of the file.
     * @param string $revisionId The ID of the revision.
     * @param array $optParams Optional parameters.
     */
    public function delete($fileId, $revisionId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'revisionId' => $revisionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "changes" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $changes = $driveService->changes;
   *  </code>
   */
  class Google_ChangesServiceResource extends Google_ServiceResource {


    /**
     * Lists the changes for a user. (changes.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool includeSubscribed Whether to include subscribed items.
     * @opt_param string startChangeId Change ID to start listing changes from.
     * @opt_param bool includeDeleted Whether to include deleted items.
     * @opt_param int maxResults Maximum number of changes to return.
     * @opt_param string pageToken Page token for changes.
     * @return Google_ChangeList
     */
    public function listChanges($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ChangeList($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a specific change. (changes.get)
     *
     * @param string $changeId The ID of the change.
     * @param array $optParams Optional parameters.
     * @return Google_Change
     */
    public function get($changeId, $optParams = array()) {
      $params = array('changeId' => $changeId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Change($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "children" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $children = $driveService->children;
   *  </code>
   */
  class Google_ChildrenServiceResource extends Google_ServiceResource {


    /**
     * Inserts a file into a folder. (children.insert)
     *
     * @param string $folderId The ID of the folder.
     * @param Google_ChildReference $postBody
     * @param array $optParams Optional parameters.
     * @return Google_ChildReference
     */
    public function insert($folderId, Google_ChildReference $postBody, $optParams = array()) {
      $params = array('folderId' => $folderId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_ChildReference($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a specific child reference. (children.get)
     *
     * @param string $folderId The ID of the folder.
     * @param string $childId The ID of the child.
     * @param array $optParams Optional parameters.
     * @return Google_ChildReference
     */
    public function get($folderId, $childId, $optParams = array()) {
      $params = array('folderId' => $folderId, 'childId' => $childId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_ChildReference($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists a folder's children. (children.list)
     *
     * @param string $folderId The ID of the folder.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string q Query string for searching children.
     * @opt_param string pageToken Page token for children.
     * @opt_param int maxResults Maximum number of children to return.
     * @return Google_ChildList
     */
    public function listChildren($folderId, $optParams = array()) {
      $params = array('folderId' => $folderId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ChildList($data);
      } else {
        return $data;
      }
    }
    /**
     * Removes a child from a folder. (children.delete)
     *
     * @param string $folderId The ID of the folder.
     * @param string $childId The ID of the child.
     * @param array $optParams Optional parameters.
     */
    public function delete($folderId, $childId, $optParams = array()) {
      $params = array('folderId' => $folderId, 'childId' => $childId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "permissions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $driveService = new Google_DriveService(...);
   *   $permissions = $driveService->permissions;
   *  </code>
   */
  class Google_PermissionsServiceResource extends Google_ServiceResource {


    /**
     * Inserts a permission for a file. (permissions.insert)
     *
     * @param string $fileId The ID for the file.
     * @param Google_Permission $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool sendNotificationEmails Whether to send notification emails.
     * @return Google_Permission
     */
    public function insert($fileId, Google_Permission $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Permission($data);
      } else {
        return $data;
      }
    }
    /**
     * Gets a permission by ID. (permissions.get)
     *
     * @param string $fileId The ID for the file.
     * @param string $permissionId The ID for the permission.
     * @param array $optParams Optional parameters.
     * @return Google_Permission
     */
    public function get($fileId, $permissionId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'permissionId' => $permissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Permission($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists a file's permissions. (permissions.list)
     *
     * @param string $fileId The ID for the file.
     * @param array $optParams Optional parameters.
     * @return Google_PermissionList
     */
    public function listPermissions($fileId, $optParams = array()) {
      $params = array('fileId' => $fileId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_PermissionList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a permission. (permissions.update)
     *
     * @param string $fileId The ID for the file.
     * @param string $permissionId The ID for the permission.
     * @param Google_Permission $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Permission
     */
    public function update($fileId, $permissionId, Google_Permission $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'permissionId' => $permissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Permission($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates a permission. This method supports patch semantics. (permissions.patch)
     *
     * @param string $fileId The ID for the file.
     * @param string $permissionId The ID for the permission.
     * @param Google_Permission $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Permission
     */
    public function patch($fileId, $permissionId, Google_Permission $postBody, $optParams = array()) {
      $params = array('fileId' => $fileId, 'permissionId' => $permissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Permission($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes a permission from a file. (permissions.delete)
     *
     * @param string $fileId The ID for the file.
     * @param string $permissionId The ID for the permission.
     * @param array $optParams Optional parameters.
     */
    public function delete($fileId, $permissionId, $optParams = array()) {
      $params = array('fileId' => $fileId, 'permissionId' => $permissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

/**
 * Service definition for Google_Drive (v2).
 *
 * <p>
 * The API to interact with Drive.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/drive/" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_DriveService extends Google_Service {
  public $files;
  public $about;
  public $apps;
  public $parents;
  public $revisions;
  public $changes;
  public $children;
  public $permissions;
  /**
   * Constructs the internal representation of the Drive service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'drive/v2/';
    $this->version = 'v2';
    $this->serviceName = 'drive';

    $client->addService($this->serviceName, $this->version);
    $this->files = new Google_FilesServiceResource($this, $this->serviceName, 'files', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"convert": {"default": "false", "type": "boolean", "location": "query"}, "targetLanguage": {"type": "string", "location": "query"}, "sourceLanguage": {"type": "string", "location": "query"}, "ocrLanguage": {"type": "string", "location": "query"}, "pinned": {"default": "false", "type": "boolean", "location": "query"}, "ocr": {"default": "false", "type": "boolean", "location": "query"}, "timedTextTrackName": {"type": "string", "location": "query"}, "timedTextLanguage": {"type": "string", "location": "query"}}, "supportsMediaUpload": true, "request": {"$ref": "File"}, "mediaUpload": {"maxSize": "10GB", "protocols": {"simple": {"path": "/upload/drive/v2/files", "multipart": true}, "resumable": {"path": "/resumable/upload/drive/v2/files", "multipart": true}}, "accept": ["*/*"]}, "response": {"$ref": "File"}, "httpMethod": "POST", "path": "files", "id": "drive.files.insert"}, "untrash": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.files.untrash", "httpMethod": "POST", "path": "files/{fileId}/untrash", "response": {"$ref": "File"}}, "trash": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.files.trash", "httpMethod": "POST", "path": "files/{fileId}/trash", "response": {"$ref": "File"}}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"updateViewedDate": {"default": "false", "type": "boolean", "location": "query"}, "projection": {"enum": ["BASIC", "FULL"], "type": "string", "location": "query"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.files.get", "httpMethod": "GET", "path": "files/{fileId}", "response": {"$ref": "File"}}, "list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"q": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "projection": {"enum": ["BASIC", "FULL"], "type": "string", "location": "query"}, "maxResults": {"default": "100", "minimum": "0", "type": "integer", "location": "query", "format": "int32"}}, "response": {"$ref": "FileList"}, "httpMethod": "GET", "path": "files", "id": "drive.files.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"convert": {"default": "false", "type": "boolean", "location": "query"}, "ocr": {"default": "false", "type": "boolean", "location": "query"}, "setModifiedDate": {"default": "false", "type": "boolean", "location": "query"}, "updateViewedDate": {"default": "true", "type": "boolean", "location": "query"}, "sourceLanguage": {"type": "string", "location": "query"}, "ocrLanguage": {"type": "string", "location": "query"}, "pinned": {"default": "false", "type": "boolean", "location": "query"}, "newRevision": {"default": "true", "type": "boolean", "location": "query"}, "targetLanguage": {"type": "string", "location": "query"}, "timedTextLanguage": {"type": "string", "location": "query"}, "timedTextTrackName": {"type": "string", "location": "query"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "supportsMediaUpload": true, "request": {"$ref": "File"}, "mediaUpload": {"maxSize": "10GB", "protocols": {"simple": {"path": "/upload/drive/v2/files/{fileId}", "multipart": true}, "resumable": {"path": "/resumable/upload/drive/v2/files/{fileId}", "multipart": true}}, "accept": ["*/*"]}, "response": {"$ref": "File"}, "httpMethod": "PUT", "path": "files/{fileId}", "id": "drive.files.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"convert": {"default": "false", "type": "boolean", "location": "query"}, "ocr": {"default": "false", "type": "boolean", "location": "query"}, "setModifiedDate": {"default": "false", "type": "boolean", "location": "query"}, "updateViewedDate": {"default": "true", "type": "boolean", "location": "query"}, "sourceLanguage": {"type": "string", "location": "query"}, "ocrLanguage": {"type": "string", "location": "query"}, "pinned": {"default": "false", "type": "boolean", "location": "query"}, "newRevision": {"default": "true", "type": "boolean", "location": "query"}, "targetLanguage": {"type": "string", "location": "query"}, "timedTextLanguage": {"type": "string", "location": "query"}, "timedTextTrackName": {"type": "string", "location": "query"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "File"}, "response": {"$ref": "File"}, "httpMethod": "PATCH", "path": "files/{fileId}", "id": "drive.files.patch"}, "touch": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.files.touch", "httpMethod": "POST", "path": "files/{fileId}/touch", "response": {"$ref": "File"}}, "copy": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"convert": {"default": "false", "type": "boolean", "location": "query"}, "ocr": {"default": "false", "type": "boolean", "location": "query"}, "sourceLanguage": {"type": "string", "location": "query"}, "ocrLanguage": {"type": "string", "location": "query"}, "pinned": {"default": "false", "type": "boolean", "location": "query"}, "targetLanguage": {"type": "string", "location": "query"}, "timedTextLanguage": {"type": "string", "location": "query"}, "timedTextTrackName": {"type": "string", "location": "query"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "File"}, "response": {"$ref": "File"}, "httpMethod": "POST", "path": "files/{fileId}/copy", "id": "drive.files.copy"}, "delete": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "path": "files/{fileId}", "id": "drive.files.delete", "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->about = new Google_AboutServiceResource($this, $this->serviceName, 'about', json_decode('{"methods": {"get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"includeSubscribed": {"default": "true", "type": "boolean", "location": "query"}, "maxChangeIdCount": {"default": "1", "type": "string", "location": "query", "format": "int64"}, "startChangeId": {"type": "string", "location": "query", "format": "int64"}}, "response": {"$ref": "About"}, "httpMethod": "GET", "path": "about", "id": "drive.about.get"}}}', true));
    $this->apps = new Google_AppsServiceResource($this, $this->serviceName, 'apps', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/drive.apps.readonly"], "path": "apps", "response": {"$ref": "AppList"}, "id": "drive.apps.list", "httpMethod": "GET"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive.apps.readonly"], "parameters": {"appId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.apps.get", "httpMethod": "GET", "path": "apps/{appId}", "response": {"$ref": "App"}}}}', true));
    $this->parents = new Google_ParentsServiceResource($this, $this->serviceName, 'parents', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ParentReference"}, "response": {"$ref": "ParentReference"}, "httpMethod": "POST", "path": "files/{fileId}/parents", "id": "drive.parents.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"parentId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.parents.get", "httpMethod": "GET", "path": "files/{fileId}/parents/{parentId}", "response": {"$ref": "ParentReference"}}, "list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.parents.list", "httpMethod": "GET", "path": "files/{fileId}/parents", "response": {"$ref": "ParentList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "path": "files/{fileId}/parents/{parentId}", "id": "drive.parents.delete", "parameters": {"parentId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->revisions = new Google_RevisionsServiceResource($this, $this->serviceName, 'revisions', json_decode('{"methods": {"patch": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"revisionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Revision"}, "response": {"$ref": "Revision"}, "httpMethod": "PATCH", "path": "files/{fileId}/revisions/{revisionId}", "id": "drive.revisions.patch"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"revisionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.revisions.get", "httpMethod": "GET", "path": "files/{fileId}/revisions/{revisionId}", "response": {"$ref": "Revision"}}, "list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.revisions.list", "httpMethod": "GET", "path": "files/{fileId}/revisions", "response": {"$ref": "RevisionList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"revisionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Revision"}, "response": {"$ref": "Revision"}, "httpMethod": "PUT", "path": "files/{fileId}/revisions/{revisionId}", "id": "drive.revisions.update"}, "delete": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "path": "files/{fileId}/revisions/{revisionId}", "id": "drive.revisions.delete", "parameters": {"revisionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->changes = new Google_ChangesServiceResource($this, $this->serviceName, 'changes', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"includeSubscribed": {"default": "true", "type": "boolean", "location": "query"}, "startChangeId": {"type": "string", "location": "query", "format": "int64"}, "includeDeleted": {"default": "true", "type": "boolean", "location": "query"}, "maxResults": {"default": "100", "minimum": "0", "type": "integer", "location": "query", "format": "int32"}, "pageToken": {"type": "string", "location": "query"}}, "response": {"$ref": "ChangeList"}, "httpMethod": "GET", "path": "changes", "id": "drive.changes.list"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"changeId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.changes.get", "httpMethod": "GET", "path": "changes/{changeId}", "response": {"$ref": "Change"}}}}', true));
    $this->children = new Google_ChildrenServiceResource($this, $this->serviceName, 'children', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"folderId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "ChildReference"}, "response": {"$ref": "ChildReference"}, "httpMethod": "POST", "path": "files/{folderId}/children", "id": "drive.children.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"folderId": {"required": true, "type": "string", "location": "path"}, "childId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.children.get", "httpMethod": "GET", "path": "files/{folderId}/children/{childId}", "response": {"$ref": "ChildReference"}}, "list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"q": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "folderId": {"required": true, "type": "string", "location": "path"}, "maxResults": {"default": "100", "minimum": "0", "type": "integer", "location": "query", "format": "int32"}}, "id": "drive.children.list", "httpMethod": "GET", "path": "files/{folderId}/children", "response": {"$ref": "ChildList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "path": "files/{folderId}/children/{childId}", "id": "drive.children.delete", "parameters": {"folderId": {"required": true, "type": "string", "location": "path"}, "childId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->permissions = new Google_PermissionsServiceResource($this, $this->serviceName, 'permissions', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"sendNotificationEmails": {"default": "true", "type": "boolean", "location": "query"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Permission"}, "response": {"$ref": "Permission"}, "httpMethod": "POST", "path": "files/{fileId}/permissions", "id": "drive.permissions.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"permissionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.permissions.get", "httpMethod": "GET", "path": "files/{fileId}/permissions/{permissionId}", "response": {"$ref": "Permission"}}, "list": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file", "https://www.googleapis.com/auth/drive.metadata.readonly", "https://www.googleapis.com/auth/drive.readonly"], "parameters": {"fileId": {"required": true, "type": "string", "location": "path"}}, "id": "drive.permissions.list", "httpMethod": "GET", "path": "files/{fileId}/permissions", "response": {"$ref": "PermissionList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"permissionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Permission"}, "response": {"$ref": "Permission"}, "httpMethod": "PUT", "path": "files/{fileId}/permissions/{permissionId}", "id": "drive.permissions.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "parameters": {"permissionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Permission"}, "response": {"$ref": "Permission"}, "httpMethod": "PATCH", "path": "files/{fileId}/permissions/{permissionId}", "id": "drive.permissions.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/drive.file"], "path": "files/{fileId}/permissions/{permissionId}", "id": "drive.permissions.delete", "parameters": {"permissionId": {"required": true, "type": "string", "location": "path"}, "fileId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));

  }
}

class Google_About extends Google_Model {
  public $kind;
  protected $__featuresType = 'Google_AboutFeatures';
  protected $__featuresDataType = 'array';
  public $features;
  public $quotaBytesUsed;
  public $permissionId;
  protected $__maxUploadSizesType = 'Google_AboutMaxUploadSizes';
  protected $__maxUploadSizesDataType = 'array';
  public $maxUploadSizes;
  public $name;
  public $remainingChangeIds;
  protected $__additionalRoleInfoType = 'Google_AboutAdditionalRoleInfo';
  protected $__additionalRoleInfoDataType = 'array';
  public $additionalRoleInfo;
  public $etag;
  protected $__importFormatsType = 'Google_AboutImportFormats';
  protected $__importFormatsDataType = 'array';
  public $importFormats;
  public $quotaBytesTotal;
  public $rootFolderId;
  public $largestChangeId;
  public $quotaBytesUsedInTrash;
  protected $__exportFormatsType = 'Google_AboutExportFormats';
  protected $__exportFormatsDataType = 'array';
  public $exportFormats;
  public $domainSharingPolicy;
  public $selfLink;
  public $isCurrentAppInstalled;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setFeatures(/* array(Google_AboutFeatures) */ $features) {
    $this->assertIsArray($features, 'Google_AboutFeatures', __METHOD__);
    $this->features = $features;
  }
  public function getFeatures() {
    return $this->features;
  }
  public function setQuotaBytesUsed($quotaBytesUsed) {
    $this->quotaBytesUsed = $quotaBytesUsed;
  }
  public function getQuotaBytesUsed() {
    return $this->quotaBytesUsed;
  }
  public function setPermissionId($permissionId) {
    $this->permissionId = $permissionId;
  }
  public function getPermissionId() {
    return $this->permissionId;
  }
  public function setMaxUploadSizes(/* array(Google_AboutMaxUploadSizes) */ $maxUploadSizes) {
    $this->assertIsArray($maxUploadSizes, 'Google_AboutMaxUploadSizes', __METHOD__);
    $this->maxUploadSizes = $maxUploadSizes;
  }
  public function getMaxUploadSizes() {
    return $this->maxUploadSizes;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setRemainingChangeIds($remainingChangeIds) {
    $this->remainingChangeIds = $remainingChangeIds;
  }
  public function getRemainingChangeIds() {
    return $this->remainingChangeIds;
  }
  public function setAdditionalRoleInfo(/* array(Google_AboutAdditionalRoleInfo) */ $additionalRoleInfo) {
    $this->assertIsArray($additionalRoleInfo, 'Google_AboutAdditionalRoleInfo', __METHOD__);
    $this->additionalRoleInfo = $additionalRoleInfo;
  }
  public function getAdditionalRoleInfo() {
    return $this->additionalRoleInfo;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setImportFormats(/* array(Google_AboutImportFormats) */ $importFormats) {
    $this->assertIsArray($importFormats, 'Google_AboutImportFormats', __METHOD__);
    $this->importFormats = $importFormats;
  }
  public function getImportFormats() {
    return $this->importFormats;
  }
  public function setQuotaBytesTotal($quotaBytesTotal) {
    $this->quotaBytesTotal = $quotaBytesTotal;
  }
  public function getQuotaBytesTotal() {
    return $this->quotaBytesTotal;
  }
  public function setRootFolderId($rootFolderId) {
    $this->rootFolderId = $rootFolderId;
  }
  public function getRootFolderId() {
    return $this->rootFolderId;
  }
  public function setLargestChangeId($largestChangeId) {
    $this->largestChangeId = $largestChangeId;
  }
  public function getLargestChangeId() {
    return $this->largestChangeId;
  }
  public function setQuotaBytesUsedInTrash($quotaBytesUsedInTrash) {
    $this->quotaBytesUsedInTrash = $quotaBytesUsedInTrash;
  }
  public function getQuotaBytesUsedInTrash() {
    return $this->quotaBytesUsedInTrash;
  }
  public function setExportFormats(/* array(Google_AboutExportFormats) */ $exportFormats) {
    $this->assertIsArray($exportFormats, 'Google_AboutExportFormats', __METHOD__);
    $this->exportFormats = $exportFormats;
  }
  public function getExportFormats() {
    return $this->exportFormats;
  }
  public function setDomainSharingPolicy($domainSharingPolicy) {
    $this->domainSharingPolicy = $domainSharingPolicy;
  }
  public function getDomainSharingPolicy() {
    return $this->domainSharingPolicy;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setIsCurrentAppInstalled($isCurrentAppInstalled) {
    $this->isCurrentAppInstalled = $isCurrentAppInstalled;
  }
  public function getIsCurrentAppInstalled() {
    return $this->isCurrentAppInstalled;
  }
}

class Google_AboutAdditionalRoleInfo extends Google_Model {
  protected $__roleSetsType = 'Google_AboutAdditionalRoleInfoRoleSets';
  protected $__roleSetsDataType = 'array';
  public $roleSets;
  public $type;
  public function setRoleSets(/* array(Google_AboutAdditionalRoleInfoRoleSets) */ $roleSets) {
    $this->assertIsArray($roleSets, 'Google_AboutAdditionalRoleInfoRoleSets', __METHOD__);
    $this->roleSets = $roleSets;
  }
  public function getRoleSets() {
    return $this->roleSets;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
}

class Google_AboutAdditionalRoleInfoRoleSets extends Google_Model {
  public $primaryRole;
  public $additionalRoles;
  public function setPrimaryRole($primaryRole) {
    $this->primaryRole = $primaryRole;
  }
  public function getPrimaryRole() {
    return $this->primaryRole;
  }
  public function setAdditionalRoles(/* array(Google_string) */ $additionalRoles) {
    $this->assertIsArray($additionalRoles, 'Google_string', __METHOD__);
    $this->additionalRoles = $additionalRoles;
  }
  public function getAdditionalRoles() {
    return $this->additionalRoles;
  }
}

class Google_AboutExportFormats extends Google_Model {
  public $source;
  public $targets;
  public function setSource($source) {
    $this->source = $source;
  }
  public function getSource() {
    return $this->source;
  }
  public function setTargets(/* array(Google_string) */ $targets) {
    $this->assertIsArray($targets, 'Google_string', __METHOD__);
    $this->targets = $targets;
  }
  public function getTargets() {
    return $this->targets;
  }
}

class Google_AboutFeatures extends Google_Model {
  public $featureName;
  public $featureRate;
  public function setFeatureName($featureName) {
    $this->featureName = $featureName;
  }
  public function getFeatureName() {
    return $this->featureName;
  }
  public function setFeatureRate($featureRate) {
    $this->featureRate = $featureRate;
  }
  public function getFeatureRate() {
    return $this->featureRate;
  }
}

class Google_AboutImportFormats extends Google_Model {
  public $source;
  public $targets;
  public function setSource($source) {
    $this->source = $source;
  }
  public function getSource() {
    return $this->source;
  }
  public function setTargets(/* array(Google_string) */ $targets) {
    $this->assertIsArray($targets, 'Google_string', __METHOD__);
    $this->targets = $targets;
  }
  public function getTargets() {
    return $this->targets;
  }
}

class Google_AboutMaxUploadSizes extends Google_Model {
  public $type;
  public $size;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_App extends Google_Model {
  public $kind;
  public $primaryFileExtensions;
  public $useByDefault;
  public $name;
  protected $__iconsType = 'Google_AppIcons';
  protected $__iconsDataType = 'array';
  public $icons;
  public $secondaryFileExtensions;
  public $installed;
  public $productUrl;
  public $secondaryMimeTypes;
  public $authorized;
  public $supportsCreate;
  public $supportsImport;
  public $primaryMimeTypes;
  public $id;
  public $objectType;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPrimaryFileExtensions(/* array(Google_string) */ $primaryFileExtensions) {
    $this->assertIsArray($primaryFileExtensions, 'Google_string', __METHOD__);
    $this->primaryFileExtensions = $primaryFileExtensions;
  }
  public function getPrimaryFileExtensions() {
    return $this->primaryFileExtensions;
  }
  public function setUseByDefault($useByDefault) {
    $this->useByDefault = $useByDefault;
  }
  public function getUseByDefault() {
    return $this->useByDefault;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setIcons(/* array(Google_AppIcons) */ $icons) {
    $this->assertIsArray($icons, 'Google_AppIcons', __METHOD__);
    $this->icons = $icons;
  }
  public function getIcons() {
    return $this->icons;
  }
  public function setSecondaryFileExtensions(/* array(Google_string) */ $secondaryFileExtensions) {
    $this->assertIsArray($secondaryFileExtensions, 'Google_string', __METHOD__);
    $this->secondaryFileExtensions = $secondaryFileExtensions;
  }
  public function getSecondaryFileExtensions() {
    return $this->secondaryFileExtensions;
  }
  public function setInstalled($installed) {
    $this->installed = $installed;
  }
  public function getInstalled() {
    return $this->installed;
  }
  public function setProductUrl($productUrl) {
    $this->productUrl = $productUrl;
  }
  public function getProductUrl() {
    return $this->productUrl;
  }
  public function setSecondaryMimeTypes(/* array(Google_string) */ $secondaryMimeTypes) {
    $this->assertIsArray($secondaryMimeTypes, 'Google_string', __METHOD__);
    $this->secondaryMimeTypes = $secondaryMimeTypes;
  }
  public function getSecondaryMimeTypes() {
    return $this->secondaryMimeTypes;
  }
  public function setAuthorized($authorized) {
    $this->authorized = $authorized;
  }
  public function getAuthorized() {
    return $this->authorized;
  }
  public function setSupportsCreate($supportsCreate) {
    $this->supportsCreate = $supportsCreate;
  }
  public function getSupportsCreate() {
    return $this->supportsCreate;
  }
  public function setSupportsImport($supportsImport) {
    $this->supportsImport = $supportsImport;
  }
  public function getSupportsImport() {
    return $this->supportsImport;
  }
  public function setPrimaryMimeTypes(/* array(Google_string) */ $primaryMimeTypes) {
    $this->assertIsArray($primaryMimeTypes, 'Google_string', __METHOD__);
    $this->primaryMimeTypes = $primaryMimeTypes;
  }
  public function getPrimaryMimeTypes() {
    return $this->primaryMimeTypes;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setObjectType($objectType) {
    $this->objectType = $objectType;
  }
  public function getObjectType() {
    return $this->objectType;
  }
}

class Google_AppIcons extends Google_Model {
  public $category;
  public $iconUrl;
  public $size;
  public function setCategory($category) {
    $this->category = $category;
  }
  public function getCategory() {
    return $this->category;
  }
  public function setIconUrl($iconUrl) {
    $this->iconUrl = $iconUrl;
  }
  public function getIconUrl() {
    return $this->iconUrl;
  }
  public function setSize($size) {
    $this->size = $size;
  }
  public function getSize() {
    return $this->size;
  }
}

class Google_AppList extends Google_Model {
  protected $__itemsType = 'Google_App';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public $selfLink;
  public function setItems(/* array(Google_App) */ $items) {
    $this->assertIsArray($items, 'Google_App', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_Change extends Google_Model {
  public $kind;
  public $deleted;
  protected $__fileType = 'Google_DriveFile';
  protected $__fileDataType = '';
  public $file;
  public $id;
  public $selfLink;
  public $fileId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDeleted($deleted) {
    $this->deleted = $deleted;
  }
  public function getDeleted() {
    return $this->deleted;
  }
  public function setFile(Google_DriveFile $file) {
    $this->file = $file;
  }
  public function getFile() {
    return $this->file;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setFileId($fileId) {
    $this->fileId = $fileId;
  }
  public function getFileId() {
    return $this->fileId;
  }
}

class Google_ChangeList extends Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'Google_Change';
  protected $__itemsDataType = 'array';
  public $items;
  public $nextLink;
  public $etag;
  public $largestChangeId;
  public $selfLink;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(Google_Change) */ $items) {
    $this->assertIsArray($items, 'Google_Change', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setLargestChangeId($largestChangeId) {
    $this->largestChangeId = $largestChangeId;
  }
  public function getLargestChangeId() {
    return $this->largestChangeId;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_ChildList extends Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'Google_ChildReference';
  protected $__itemsDataType = 'array';
  public $items;
  public $nextLink;
  public $etag;
  public $selfLink;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(Google_ChildReference) */ $items) {
    $this->assertIsArray($items, 'Google_ChildReference', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_ChildReference extends Google_Model {
  public $kind;
  public $childLink;
  public $id;
  public $selfLink;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setChildLink($childLink) {
    $this->childLink = $childLink;
  }
  public function getChildLink() {
    return $this->childLink;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_DriveFile extends Google_Model {
  public $mimeType;
  public $thumbnailLink;
  protected $__labelsType = 'Google_DriveFileLabels';
  protected $__labelsDataType = '';
  public $labels;
  protected $__indexableTextType = 'Google_DriveFileIndexableText';
  protected $__indexableTextDataType = '';
  public $indexableText;
  public $explicitlyTrashed;
  public $etag;
  public $lastModifyingUserName;
  public $writersCanShare;
  public $id;
  public $title;
  public $ownerNames;
  public $sharedWithMeDate;
  public $lastViewedByMeDate;
  protected $__parentsType = 'Google_ParentReference';
  protected $__parentsDataType = 'array';
  public $parents;
  public $exportLinks;
  public $originalFilename;
  public $description;
  public $webContentLink;
  public $editable;
  public $kind;
  public $quotaBytesUsed;
  public $fileSize;
  public $createdDate;
  public $md5Checksum;
  protected $__imageMediaMetadataType = 'Google_DriveFileImageMediaMetadata';
  protected $__imageMediaMetadataDataType = '';
  public $imageMediaMetadata;
  public $embedLink;
  public $alternateLink;
  public $modifiedByMeDate;
  public $downloadUrl;
  protected $__userPermissionType = 'Google_Permission';
  protected $__userPermissionDataType = '';
  public $userPermission;
  public $fileExtension;
  public $selfLink;
  public $modifiedDate;
  public function setMimeType($mimeType) {
    $this->mimeType = $mimeType;
  }
  public function getMimeType() {
    return $this->mimeType;
  }
  public function setThumbnailLink($thumbnailLink) {
    $this->thumbnailLink = $thumbnailLink;
  }
  public function getThumbnailLink() {
    return $this->thumbnailLink;
  }
  public function setLabels(Google_DriveFileLabels $labels) {
    $this->labels = $labels;
  }
  public function getLabels() {
    return $this->labels;
  }
  public function setIndexableText(Google_DriveFileIndexableText $indexableText) {
    $this->indexableText = $indexableText;
  }
  public function getIndexableText() {
    return $this->indexableText;
  }
  public function setExplicitlyTrashed($explicitlyTrashed) {
    $this->explicitlyTrashed = $explicitlyTrashed;
  }
  public function getExplicitlyTrashed() {
    return $this->explicitlyTrashed;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setLastModifyingUserName($lastModifyingUserName) {
    $this->lastModifyingUserName = $lastModifyingUserName;
  }
  public function getLastModifyingUserName() {
    return $this->lastModifyingUserName;
  }
  public function setWritersCanShare($writersCanShare) {
    $this->writersCanShare = $writersCanShare;
  }
  public function getWritersCanShare() {
    return $this->writersCanShare;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setOwnerNames(/* array(Google_string) */ $ownerNames) {
    $this->assertIsArray($ownerNames, 'Google_string', __METHOD__);
    $this->ownerNames = $ownerNames;
  }
  public function getOwnerNames() {
    return $this->ownerNames;
  }
  public function setSharedWithMeDate($sharedWithMeDate) {
    $this->sharedWithMeDate = $sharedWithMeDate;
  }
  public function getSharedWithMeDate() {
    return $this->sharedWithMeDate;
  }
  public function setLastViewedByMeDate($lastViewedByMeDate) {
    $this->lastViewedByMeDate = $lastViewedByMeDate;
  }
  public function getLastViewedByMeDate() {
    return $this->lastViewedByMeDate;
  }
  public function setParents(/* array(Google_ParentReference) */ $parents) {
    $this->assertIsArray($parents, 'Google_ParentReference', __METHOD__);
    $this->parents = $parents;
  }
  public function getParents() {
    return $this->parents;
  }
  public function setExportLinks($exportLinks) {
    $this->exportLinks = $exportLinks;
  }
  public function getExportLinks() {
    return $this->exportLinks;
  }
  public function setOriginalFilename($originalFilename) {
    $this->originalFilename = $originalFilename;
  }
  public function getOriginalFilename() {
    return $this->originalFilename;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setWebContentLink($webContentLink) {
    $this->webContentLink = $webContentLink;
  }
  public function getWebContentLink() {
    return $this->webContentLink;
  }
  public function setEditable($editable) {
    $this->editable = $editable;
  }
  public function getEditable() {
    return $this->editable;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setQuotaBytesUsed($quotaBytesUsed) {
    $this->quotaBytesUsed = $quotaBytesUsed;
  }
  public function getQuotaBytesUsed() {
    return $this->quotaBytesUsed;
  }
  public function setFileSize($fileSize) {
    $this->fileSize = $fileSize;
  }
  public function getFileSize() {
    return $this->fileSize;
  }
  public function setCreatedDate($createdDate) {
    $this->createdDate = $createdDate;
  }
  public function getCreatedDate() {
    return $this->createdDate;
  }
  public function setMd5Checksum($md5Checksum) {
    $this->md5Checksum = $md5Checksum;
  }
  public function getMd5Checksum() {
    return $this->md5Checksum;
  }
  public function setImageMediaMetadata(Google_DriveFileImageMediaMetadata $imageMediaMetadata) {
    $this->imageMediaMetadata = $imageMediaMetadata;
  }
  public function getImageMediaMetadata() {
    return $this->imageMediaMetadata;
  }
  public function setEmbedLink($embedLink) {
    $this->embedLink = $embedLink;
  }
  public function getEmbedLink() {
    return $this->embedLink;
  }
  public function setAlternateLink($alternateLink) {
    $this->alternateLink = $alternateLink;
  }
  public function getAlternateLink() {
    return $this->alternateLink;
  }
  public function setModifiedByMeDate($modifiedByMeDate) {
    $this->modifiedByMeDate = $modifiedByMeDate;
  }
  public function getModifiedByMeDate() {
    return $this->modifiedByMeDate;
  }
  public function setDownloadUrl($downloadUrl) {
    $this->downloadUrl = $downloadUrl;
  }
  public function getDownloadUrl() {
    return $this->downloadUrl;
  }
  public function setUserPermission(Google_Permission $userPermission) {
    $this->userPermission = $userPermission;
  }
  public function getUserPermission() {
    return $this->userPermission;
  }
  public function setFileExtension($fileExtension) {
    $this->fileExtension = $fileExtension;
  }
  public function getFileExtension() {
    return $this->fileExtension;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setModifiedDate($modifiedDate) {
    $this->modifiedDate = $modifiedDate;
  }
  public function getModifiedDate() {
    return $this->modifiedDate;
  }
}

class Google_DriveFileImageMediaMetadata extends Google_Model {
  public $width;
  public $rotation;
  protected $__locationType = 'Google_DriveFileImageMediaMetadataLocation';
  protected $__locationDataType = '';
  public $location;
  public $height;
  public function setWidth($width) {
    $this->width = $width;
  }
  public function getWidth() {
    return $this->width;
  }
  public function setRotation($rotation) {
    $this->rotation = $rotation;
  }
  public function getRotation() {
    return $this->rotation;
  }
  public function setLocation(Google_DriveFileImageMediaMetadataLocation $location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setHeight($height) {
    $this->height = $height;
  }
  public function getHeight() {
    return $this->height;
  }
}

class Google_DriveFileImageMediaMetadataLocation extends Google_Model {
  public $latitude;
  public $altitude;
  public $longitude;
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  public function getLatitude() {
    return $this->latitude;
  }
  public function setAltitude($altitude) {
    $this->altitude = $altitude;
  }
  public function getAltitude() {
    return $this->altitude;
  }
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  public function getLongitude() {
    return $this->longitude;
  }
}

class Google_DriveFileIndexableText extends Google_Model {
  public $text;
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
}

class Google_DriveFileLabels extends Google_Model {
  public $restricted;
  public $hidden;
  public $viewed;
  public $starred;
  public $trashed;
  public function setRestricted($restricted) {
    $this->restricted = $restricted;
  }
  public function getRestricted() {
    return $this->restricted;
  }
  public function setHidden($hidden) {
    $this->hidden = $hidden;
  }
  public function getHidden() {
    return $this->hidden;
  }
  public function setViewed($viewed) {
    $this->viewed = $viewed;
  }
  public function getViewed() {
    return $this->viewed;
  }
  public function setStarred($starred) {
    $this->starred = $starred;
  }
  public function getStarred() {
    return $this->starred;
  }
  public function setTrashed($trashed) {
    $this->trashed = $trashed;
  }
  public function getTrashed() {
    return $this->trashed;
  }
}

class Google_FileList extends Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__itemsType = 'Google_DriveFile';
  protected $__itemsDataType = 'array';
  public $items;
  public $nextLink;
  public $etag;
  public $selfLink;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setItems(/* array(Google_DriveFile) */ $items) {
    $this->assertIsArray($items, 'Google_DriveFile', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setNextLink($nextLink) {
    $this->nextLink = $nextLink;
  }
  public function getNextLink() {
    return $this->nextLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_ParentList extends Google_Model {
  protected $__itemsType = 'Google_ParentReference';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public $selfLink;
  public function setItems(/* array(Google_ParentReference) */ $items) {
    $this->assertIsArray($items, 'Google_ParentReference', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_ParentReference extends Google_Model {
  public $selfLink;
  public $kind;
  public $id;
  public $isRoot;
  public $parentLink;
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setIsRoot($isRoot) {
    $this->isRoot = $isRoot;
  }
  public function getIsRoot() {
    return $this->isRoot;
  }
  public function setParentLink($parentLink) {
    $this->parentLink = $parentLink;
  }
  public function getParentLink() {
    return $this->parentLink;
  }
}

class Google_Permission extends Google_Model {
  public $withLink;
  public $kind;
  public $name;
  public $value;
  public $id;
  public $authKey;
  public $etag;
  public $role;
  public $photoLink;
  public $type;
  public $additionalRoles;
  public $selfLink;
  public function setWithLink($withLink) {
    $this->withLink = $withLink;
  }
  public function getWithLink() {
    return $this->withLink;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAuthKey($authKey) {
    $this->authKey = $authKey;
  }
  public function getAuthKey() {
    return $this->authKey;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setRole($role) {
    $this->role = $role;
  }
  public function getRole() {
    return $this->role;
  }
  public function setPhotoLink($photoLink) {
    $this->photoLink = $photoLink;
  }
  public function getPhotoLink() {
    return $this->photoLink;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setAdditionalRoles(/* array(Google_string) */ $additionalRoles) {
    $this->assertIsArray($additionalRoles, 'Google_string', __METHOD__);
    $this->additionalRoles = $additionalRoles;
  }
  public function getAdditionalRoles() {
    return $this->additionalRoles;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_PermissionList extends Google_Model {
  protected $__itemsType = 'Google_Permission';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public $selfLink;
  public function setItems(/* array(Google_Permission) */ $items) {
    $this->assertIsArray($items, 'Google_Permission', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}

class Google_Revision extends Google_Model {
  public $mimeType;
  public $pinned;
  public $kind;
  public $publishedLink;
  public $publishedOutsideDomain;
  public $publishAuto;
  public $published;
  public $downloadUrl;
  public $selfLink;
  public $etag;
  public $fileSize;
  public $exportLinks;
  public $lastModifyingUserName;
  public $originalFilename;
  public $id;
  public $md5Checksum;
  public $modifiedDate;
  public function setMimeType($mimeType) {
    $this->mimeType = $mimeType;
  }
  public function getMimeType() {
    return $this->mimeType;
  }
  public function setPinned($pinned) {
    $this->pinned = $pinned;
  }
  public function getPinned() {
    return $this->pinned;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setPublishedLink($publishedLink) {
    $this->publishedLink = $publishedLink;
  }
  public function getPublishedLink() {
    return $this->publishedLink;
  }
  public function setPublishedOutsideDomain($publishedOutsideDomain) {
    $this->publishedOutsideDomain = $publishedOutsideDomain;
  }
  public function getPublishedOutsideDomain() {
    return $this->publishedOutsideDomain;
  }
  public function setPublishAuto($publishAuto) {
    $this->publishAuto = $publishAuto;
  }
  public function getPublishAuto() {
    return $this->publishAuto;
  }
  public function setPublished($published) {
    $this->published = $published;
  }
  public function getPublished() {
    return $this->published;
  }
  public function setDownloadUrl($downloadUrl) {
    $this->downloadUrl = $downloadUrl;
  }
  public function getDownloadUrl() {
    return $this->downloadUrl;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setFileSize($fileSize) {
    $this->fileSize = $fileSize;
  }
  public function getFileSize() {
    return $this->fileSize;
  }
  public function setExportLinks($exportLinks) {
    $this->exportLinks = $exportLinks;
  }
  public function getExportLinks() {
    return $this->exportLinks;
  }
  public function setLastModifyingUserName($lastModifyingUserName) {
    $this->lastModifyingUserName = $lastModifyingUserName;
  }
  public function getLastModifyingUserName() {
    return $this->lastModifyingUserName;
  }
  public function setOriginalFilename($originalFilename) {
    $this->originalFilename = $originalFilename;
  }
  public function getOriginalFilename() {
    return $this->originalFilename;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setMd5Checksum($md5Checksum) {
    $this->md5Checksum = $md5Checksum;
  }
  public function getMd5Checksum() {
    return $this->md5Checksum;
  }
  public function setModifiedDate($modifiedDate) {
    $this->modifiedDate = $modifiedDate;
  }
  public function getModifiedDate() {
    return $this->modifiedDate;
  }
}

class Google_RevisionList extends Google_Model {
  protected $__itemsType = 'Google_Revision';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public $selfLink;
  public function setItems(/* array(Google_Revision) */ $items) {
    $this->assertIsArray($items, 'Google_Revision', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setSelfLink($selfLink) {
    $this->selfLink = $selfLink;
  }
  public function getSelfLink() {
    return $this->selfLink;
  }
}
