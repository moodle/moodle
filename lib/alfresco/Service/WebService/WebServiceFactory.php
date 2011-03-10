<?php

/*
 * Copyright (C) 2005 Alfresco, Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

 * As a special exception to the terms and conditions of version 2.0 of 
 * the GPL, you may redistribute this Program in connection with Free/Libre 
 * and Open Source Software ("FLOSS") applications as described in Alfresco's 
 * FLOSS exception.  You should have recieved a copy of the text describing 
 * the FLOSS exception, and it is also available here: 
 * http://www.alfresco.com/legal/licensing"
 */

// changed by moodle
require_once ($CFG->libdir.'/alfresco/Service/WebService/AlfrescoWebService.php');

class WebServiceFactory
{
   public static function getAuthenticationService($path)
   {
        $path .= '/AuthenticationService?wsdl';
        return new AlfrescoWebService($path, array('location'=>$path));
   }

   public static function getRepositoryService($path, $ticket)
   {
        $path .= '/RepositoryService?wsdl';
        return new AlfrescoWebService($path, array('location'=>$path), $ticket);
   }
   
   public static function getContentService($path, $ticket)
   {
        $path .= '/ContentService?wsdl';
        return new AlfrescoWebService($path, array('location'=>$path), $ticket);
   }
   
   public static function getAdministrationService($path, $ticket)
   {
        $path .= '/AdministrationService?wsdl';
        return new AlfrescoWebService($path, array('location'=>$path), $ticket);
   }   
   
   public static function getAuthoringService($path, $ticket)
   {
        $path .= '/AuthoringService?wsdl';
        return new AlfrescoWebService($path, array('location'=>$path), $ticket);
   }
}

?>
