import java.awt.*;
import java.awt.event.*;
import java.net.*;
import java.io.*;
import java.sql.*;
import java.util.*;

public class arsc
{
 static String DBUrl = "jdbc:mysql://194.231.30.146/arscdev?user=user&password=password";
 static String arsc_sid = "dhndewbz2798z23dzdewﬂdew";

 public static void main(String[] args)
 {
  MainFrameGui gui = new MainFrameGui();
  gui.setSize(480,300);
  gui.setVisible(true);
  
  try
  {
   try
   {
    Class.forName("org.gjt.mm.mysql.Driver").newInstance();
   }
   catch (Exception e)
   {
    System.err.println(e.toString());
   }
   
   Connection conn = null;
   Statement stmt = null;
   PreparedStatement pStmt = null;
  
   conn = DriverManager.getConnection(DBUrl);
   stmt = conn.createStatement();
   stmt.executeUpdate("INSERT INTO arsc_users (id, user, lastping, ip, room, language, version, level, sid, lastmessageping) VALUES ('', 'javapgod', '', '', 'lounge', 'german', 'sockets', '1', 'abcdefg', '') ");
  }
  catch (SQLException sqlEx) { System.err.println(sqlEx.toString()); }

  try
  {
   Thread.sleep(5000);
  }
  catch (InterruptedException e)
  {
   //nix
  }

  try
  {
   Socket sock = new Socket("194.231.30.146", 12345);
   BufferedReader in = new BufferedReader(new InputStreamReader(sock.getInputStream()));
   OutputStream out = sock.getOutputStream();
   String line = "arsc_sid=abcdefg HTTP";
   out.write(line.getBytes());
   out.write('\r');
   out.write('\n');
   String blubb = new String();
   while ((blubb = in.readLine()) != null)
   {
    gui.messages.append(blubb);
   }
   in.close();
   out.close();
   sock.close();
  }
  catch (IOException e)
  {
   System.err.println(e.toString());
   //System.exit;
  }
  
  while (true)
  {
   try
   {
    Thread.sleep(500);
   }
   catch (InterruptedException e)
   {
    //nix
   }
   //gui.messages.append("blah...\n");
   if (gui.cb.getState() == true)
   {
    gui.messages.setCaretPosition(99999999);
   }
  }
 }
}
