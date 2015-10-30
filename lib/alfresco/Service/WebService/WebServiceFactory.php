<?php

/*
 * Copyright (C) 2005-2010 Alfresco Software Limited.
 *
 * This file is part of Alfresco
 *
 * Alfresco is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Alfresco is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Alfresco. If not, see <http://www.gnu.org/licenses/>.
 */

require_once $CFG->libdir.'/alfresco/Service/WebService/AlfrescoWebService.php';

class WebServiceFactory
{
   public static function getAuthenticationService($path)
   {
        $path .= '/AuthenticationService?wsdl';
        return new AlfrescoWebService($path, array());
   }

   public static function getRepositoryService($path, $ticket)
   {
        $path .= '/RepositoryService?wsdl';
        return new AlfrescoWebService($path, array(), $ticket);
   }
   
   public static function getContentService($path, $ticket)
   {
        $path .= '/ContentService?wsdl';
        return new AlfrescoWebService($path, array(), $ticket);
   }
   
   public static function getAdministrationService($path, $ticket)
   {
        $path .= '/AdministrationService?wsdl';
        return new AlfrescoWebService($path, array(), $ticket);
   }   
   
   public static function getAuthoringService($path, $ticket)
   {
        $path .= '/AuthoringService?wsdl';
        return new AlfrescoWebService($path, array(), $ticket);
   }
}

?>