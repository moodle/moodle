#!/usr/local/php-cgi/current/bin/php -q
<?php
@set_time_limit (0);
set_magic_quotes_runtime(0);

include("../config.inc.php");
include("../functions.inc.php");
include("../filter.inc.php");


// Checking parameters

if ($argv[1] == "--help" OR $argv[1] == "-h" OR $argv[1] == "/?")
{
 echo "Starts the ARSC socket server on port {$arsc_parameters["socketserver_port"]}.";
 echo "\n\n";
 echo "Usage: arscd.php [-v|-l|--help]\n\n";
 echo "Example:\n";
 echo "  arscd.php -l=/var/log/arsc\n\n";
 echo "Options:\n";
 echo "  -v           Verbose mode, print every message every user receives to STDOUT\n";
 echo "  -l=DIR       Write message logfiles for every room into DIR (creates file for every room)\n";
 echo "  -h, --help   Show this help\n";
 echo "\n";
 echo "For bug reporting, please visit:";
 echo "<URL:http://manuel.kiessling.net/projects/software/arsc/bugs/>.\n";
 die();
}
$arsc_logdir = false;
if (ereg("-l=", $argv[1]))
{
 if (is_dir(str_replace("-l=", "", $argv[1])))
 {
  $arsc_logdir = str_replace("-l=", "", $argv[1]);
  echo "Logging into '".$arsc_logdir."'\n";
 }
 else
 {
  die ("Cannot log into '".str_replace("-l=", "", $argv[1])."'. Aborting.\n");
 }
}


// Creating socket

if(false === ($arsc_listen_socket = socket_create_listen((string)$arsc_parameters["socketserver_port"], $arsc_parameters["socketserver_maximumusers"])))
 die("Couldn't create listening socket on port {$arsc_parameters["socketserver_port"]}.\n");
if(false === socket_setopt($arsc_listen_socket, SOL_SOCKET, SO_REUSEADDR, 1))
 die("Couldn't set socket option\n");

socket_set_nonblock($arsc_listen_socket);

$arsc_connected_clients = 0;
$arsc_connections = array();
$arsc_connection_info = array();
$arsc_sid = array();

echo date("[Y-m-d H:i:s]")." {SOCK} Started ARSC server listening on port ".$arsc_parameters["socketserver_port"].".\n";

while(1) // Handling connections in a neverending loop
{
 $arsc_socket_set = array_merge($arsc_listen_socket, $arsc_connections);
 if(socket_select($arsc_socket_set, $a = NULL, $b = NULL, 0, 0))
 {
  foreach($arsc_connections as $arsc_connection)
  {
   if(!($arsc_connection == $arsc_listen_socket))
   {
    foreach($arsc_connection_info as $arsc_num => $arsc_info)
    {
     if($arsc_connection == $arsc_info['handle'])
     {
      if ($arsc_sid[$arsc_num] == "")
      {
       $arsc_read_socket = array($arsc_connection);
       $arsc_socket_changed = socket_select($arsc_read_socket, $write = NULL, $except = NULL, 0, 0);
       if ($arsc_socket_changed > 0)
       {
        $received_data = socket_read($arsc_connection, 100);
        ereg("arsc_sid=(.*) HTTP", $received_data, $a);
        $arsc_sid[$arsc_num] = $a[1];
        if ($arsc_sid[$arsc_num] <> "")
        {
         $arsc_my = arsc_getdatafromsid($arsc_sid[$arsc_num]);
         echo date("[Y-m-d H:i:s]")." {ARSC} #$arsc_num | Connection is an ARSC client (SID $arsc_sid[$arsc_num], nickname {$arsc_my["user"]}, room {$arsc_my["room"]})\n";
         arsc_socket_write($arsc_connection, $arsc_parameters["htmlhead_js"]);
         $arsc_sendtime = date("H:i:s");
         $arsc_timeid = arsc_microtime();
         @include("../shared/language/".$arsc_my["language"].".inc.php");
         $arsc_message = "/msg ".$arsc_my["user"]." ".$arsc_lang["welcome"];
         $arsc_message = arsc_filter_posting("System", $arsc_sendtime, $arsc_message, $arsc_my["room"], 0);
         arsc_socket_write($arsc_connection, $arsc_message);
        }
        else
        {
         $arsc_sid[$arsc_num] = "-1";
         echo date("[Y-m-d H:i:s]")." {SOCK} #$arsc_num | Connection is invalid\n";
         $arsc_text = "You don't seem to be a valid ARSC client. Connection closed.";
         arsc_socket_write($arsc_connection, $arsc_text);
         unset($arsc_connections[$arsc_num]);
         unset($arsc_connection_info[$arsc_num]);
         flush();
        }
       }
      }
      else
      {
       $arsc_newmessages = arsc_getmessages($arsc_sid[$arsc_num]);
       if ($arsc_newmessages <> "")
       {
        if (!arsc_socket_write($arsc_connection, $arsc_newmessages))
        {
         $arsc_user = $arsc_my["user"];
         $arsc_room = $arsc_my["room"];
         $arsc_nice_room = arsc_nice_room($arsc_room);
         $arsc_timeid = arsc_microtime();
         $arsc_sendtime = date("H:i:s");
         mysql_query("DELETE from arsc_users WHERE sid = '{$arsc_sid[$arsc_num]}'");
         mysql_query("INSERT into arsc_room_$arsc_room (message, user, sendtime, timeid) VALUES ('arsc_user_quit~~$arsc_user~~$arsc_nice_room', 'System', '$arsc_sendtime', '$arsc_timeid')");
         echo date("[Y-m-d H:i:s]")." {SOCK} #$arsc_num | Client #$arsc_num {$arsc_connection_info[$arsc_num]['address']}:{$arsc_connection_info[$arsc_num]['port']} disconnected\n";
         echo date("[Y-m-d H:i:s]")." {ARSC} #$arsc_num | Cannot reach user (SID $arsc_sid[$arsc_num], nickname {$arsc_my["user"]}, room {$arsc_my["room"]})\n";
         unset($arsc_connections[$arsc_num]);
         unset($arsc_connection_info[$arsc_num]);
         flush();
        }
       }
       else
       {
        $arsc_user = $arsc_my["user"];
        $arsc_room = $arsc_my["room"];
        $arsc_nice_room = arsc_nice_room($arsc_room);
        $arsc_timeid = arsc_microtime();
        $arsc_sendtime = date("H:i:s");
        mysql_query("DELETE from arsc_users WHERE sid = '{$arsc_sid[$arsc_num]}'");
        mysql_query("INSERT into arsc_room_$arsc_room (message, user, sendtime, timeid) VALUES ('arsc_user_quit~~$arsc_user~~$arsc_nice_room', 'System', '$arsc_sendtime', '$arsc_timeid')");
        echo date("[Y-m-d H:i:s]")." {ARSC} #$arsc_num | User no longer known to ARSC (SID was $arsc_sid[$arsc_num])\n";
        echo date("[Y-m-d H:i:s]")." {SOCK} #$arsc_num | Client {$arsc_connection_info[$arsc_num]['address']}:{$arsc_connection_info[$arsc_num]['port']} disconnected\n";
        unset($arsc_connections[$arsc_num]);
        unset($arsc_connection_info[$arsc_num]);
        flush();
       }
      }
     }
    }
   }
  } 
  // A new client connected
  if($arsc_connection_info[$arsc_connected_clients]['handle'] = @socket_accept($arsc_listen_socket))
  {
   $arsc_connections[] = $arsc_connection_info[$arsc_connected_clients]['handle'];
   socket_getpeername($arsc_connection_info[$arsc_connected_clients]['handle'], &$arsc_connection_info[$arsc_connected_clients]['address'], &$arsc_connection_info[$arsc_connected_clients]['port']);
   echo date("[Y-m-d H:i:s]")." {SOCK} #$arsc_connected_clients | Connection from {$arsc_connection_info[$arsc_connected_clients]['address']} on port {$arsc_connection_info[$arsc_connected_clients]['port']}\n";
   flush();
   $arsc_connected_clients++;
  }
 }
 usleep($arsc_parameters["socketserver_refresh"]); 
}


// Message handling

function arsc_getmessages($arsc_sid)
{
 GLOBAL $arsc_my,
        $arsc_parameters,
        $arsc_lang,
        $argv,
        $arsc_logdir,
        $arsc_lastlogmessage,
        $arsc_num;
 
 $arsc_sid = str_replace("/", "", $arsc_sid);
 
 if (($arsc_my = arsc_getdatafromsid($arsc_sid)) <> FALSE)
 {
  $arsc_room = $arsc_my["room"];
  if ($arsc_my["level"] < 0)
  {
   include("../shared/language/".$arsc_my["language"].".inc.php");
   switch($arsc_my["level"])
   {
    case "-1": mysql_query("DELETE from arsc_users WHERE sid = '$arsc_sid'");
               return arsc_filter_posting("System", date("H:i:s"), "<font size=\"4\"><b>".$arsc_lang["youwerekicked"]."</b></font>", $arsc_room, 0);
               
   }
  }
  else
  {
   $arsc_posting = " \n ";
   include("../shared/language/".$arsc_my["language"].".inc.php");
   $arsc_result = mysql_query("SELECT lastmessageping from arsc_users WHERE sid = '$arsc_sid'");
   $arsc_b = mysql_fetch_array($arsc_result);
   if ($arsc_b["lastmessageping"] == "0")
   {
    $arsc_lastmessageping = arsc_microtime();
    mysql_query("UPDATE arsc_users SET lastmessageping = '$arsc_lastmessageping' WHERE sid = '$arsc_sid'");
   }
   else
   {
    $arsc_lastmessageping = $arsc_b["lastmessageping"];
    $arsc_result = mysql_query("SELECT message, user, flag_ripped, sendtime, timeid from arsc_room_$arsc_room WHERE timeid > '$arsc_lastmessageping' ORDER BY timeid ASC, id ASC");
    while ($arsc_a = mysql_fetch_array($arsc_result))
    {
     $arsc_posting .= arsc_filter_posting($arsc_a["user"], $arsc_a["sendtime"], $arsc_a["message"], $arsc_room, $arsc_a["flag_ripped"]);
     $arsc_lastmessageping = $arsc_a["timeid"];
     if ($argv[1] == "-v")
     {
      echo date("[Y-m-d H:i:s]")." {MESG} #$arsc_num | Room: $arsc_room | User: {$arsc_a["user"]} | Sendtime: {$arsc_a["sendtime"]} | Message: {$arsc_a["message"]}\n";
     }
     elseif($arsc_logdir)
     {
      $arsc_logmessage = "[".date("Y-m-d")."] [{$arsc_a["sendtime"]}] {$arsc_a["user"]}: {$arsc_a["message"]}\n";
      if ($arsc_lastlogmessage != $arsc_logmessage)
      {
       $arsc_lastlogmessage = $arsc_logmessage;
       $arsc_logresource = "fp_".$arsc_room;
       if(is_resource($$arsc_logresource))
       {
        fputs($$arsc_logresource, $arsc_logmessage);
       }
       else
       {
        if ($$arsc_logresource = fopen($arsc_logdir."/".$arsc_room.".log", "a"))
        {
         fputs($$arsc_logresource, $arsc_logmessage);
        }
        else
        {
         echo "Error: cannot open logfile '".$arsc_logdir."/".$arsc_room.".log', disable logging.\n";
         $arsc_logdir = false;
        }
       }
      }
     }
    }
    $arsc_ping = time();
    mysql_query("UPDATE arsc_users SET lastping = '$arsc_ping', lastmessageping = '$arsc_lastmessageping' WHERE sid = '$arsc_sid'");
   }
   return $arsc_posting;
  }
 }
}


// Helpers
function arsc_socket_write(&$connection, $text)
{
 $return = false;
 $check_socket = array($connection);
 $socket_changed = socket_select($read = NULL, $check_socket, $except = NULL, 0, 0);
 if ($socket_changed > 0)
 {
  $return = true;
  @socket_write($connection, $text, strlen($text));
 }
 else
 {
  echo date("[Y-m-d H:i:s]")." {SOCK} Socket not ready for write, closing connection.\n";
 }
 return $return;
}

?>
