import java.awt.*;
import java.awt.event.*;

public class MainFrameGui
extends Frame
{
 public static TextArea messages;
 public static TextField eingabe;
 public static List userliste;
 public static Checkbox cb;
 public static Button hilfebutton;
 public static GridBagConstraints makegbc;
 public static TextDialog hilfedialog;
 public static MenuBar menu;
 
 public MainFrameGui()
 {
  super ("ARSC Really Simple Chat");
  setBackground(Color.lightGray);
  addWindowListener(new WindowClosingAdapter(true));
  
  //Menü
  MenuBar menu = new MenuBar();
   Menu menuConnection = new Menu("Connection");
    menuConnection.add("Connect to server");
    menuConnection.add("Disconnect from server");
    menuConnection.add("Quit");
   Menu menuConfiguration = new Menu("Configuration");
   Menu menuHelp = new Menu("Help");
    MenuItem mi = new MenuItem("Show commands");
    mi.addActionListener(new hilfebuttonListener());
    menuHelp.add(mi);
    menuHelp.add("About...");
   menu.add(menuConnection);
   menu.add(menuConfiguration);
   menu.add(menuHelp);
  
  setMenuBar(menu);
  
  //Layout setzen und Komponenten hinzufügen
  GridBagLayout gbl = new GridBagLayout();
  GridBagConstraints gbc;
  setLayout(gbl);
  
  //TextArea hinzufügen
  messages = new TextArea("", 20, 20, TextArea.SCROLLBARS_VERTICAL_ONLY);
  gbc = makegbc(0, 0, 2, 2);
  gbc.weightx = 100;
  gbc.weighty = 100;
  gbc.fill = GridBagConstraints.BOTH;
  gbl.setConstraints(messages, gbc);
  add(messages);
  
  //Userliste
  userliste = new List();
  userliste.add("pgod");
  userliste.add("HanSolo");
  userliste.add("dArUdE");
  gbc = makegbc(2, 0, 2, 2);
  gbc.fill = GridBagConstraints.BOTH;
  gbl.setConstraints(userliste, gbc);
  userliste.addActionListener(new userlisteListener());
  add(userliste);
  
  //Eingabefeld
  eingabe = new TextField();
  gbc = makegbc(0, 2, 1, 1);
  gbc.weightx = 100;
  gbc.fill = GridBagConstraints.HORIZONTAL;
  gbc.anchor = GridBagConstraints.SOUTH;
  gbl.setConstraints(eingabe, gbc);
  eingabe.addActionListener(new eingabeListener());
  add(eingabe);

  //Checkbox
  cb = new Checkbox("Scrolling");
  cb.setState(true);
  gbc = makegbc(1, 2, 1, 1);
  gbc.fill = GridBagConstraints.NONE;
  gbc.anchor = GridBagConstraints.SOUTH;
  gbl.setConstraints(cb, gbc);
  add(cb);
  
  //Hilfebutton
  hilfebutton = new Button("Help");
  gbc = makegbc(2, 2, 1, 1);
  gbc.fill = GridBagConstraints.NONE;
  gbc.anchor = GridBagConstraints.WEST;
  gbl.setConstraints(hilfebutton, gbc);
  hilfebutton.addActionListener(new hilfebuttonListener());
  add(hilfebutton);
  //Gib ihm
  pack();
  hilfedialog = new TextDialog(this, "Hilfe", "Hilfe\nLala\nLulu...", "Schliessen", false, 200, 400);
 }
 
 //Die Listener
 public class eingabeListener
 implements ActionListener
 {
  public void actionPerformed(ActionEvent event)
  {
   System.out.println("Event erhalten");
   TextField source = (TextField)event.getSource();
   messages.append("\n" + source.getText());
   source.selectAll();
  }
 }

 public class userlisteListener
 implements ActionListener
 {
  public void actionPerformed(ActionEvent event)
  {
   System.out.println("userliste Event erhalten");
   List source = (List)event.getSource();
   eingabe.setText("/msg " + source.getSelectedItem() + " ");
   eingabe.setCaretPosition(99);
   eingabe.requestFocus();
  }
 }

 public class hilfebuttonListener
 implements ActionListener
 {
  public void actionPerformed(ActionEvent event)
  {
   System.out.println("hilfebutton Event erhalten");
   if(getToolkit().getScreenSize().width - (getBounds().x + getBounds().width) >= hilfedialog.getSize().width)
   {
    hilfedialog.setLocation(getBounds().x + getBounds().width, getBounds().y);
   }
   else
   {
    hilfedialog.setLocation(getToolkit().getScreenSize().width - hilfedialog.getSize().width, getBounds().y);
   }
   hilfedialog.setVisible(true);
  }
 }

 public class WindowClosingAdapter
 extends WindowAdapter
 {
  private boolean exitSystem;
  
  public WindowClosingAdapter(boolean exitSystem)
  {
   this.exitSystem = exitSystem;
  }
  
  public WindowClosingAdapter()
  {
   this(true);
  }
  
  public void windowClosing(WindowEvent event)
  {
   event.getWindow().setVisible(false);
   event.getWindow().dispose();
   if (exitSystem)
   {
    System.exit(0);
   }
  }
 }
 
 public static GridBagConstraints makegbc(int x, int y, int width, int height)
 {
  GridBagConstraints gbc = new GridBagConstraints();
  gbc.gridx = x;
  gbc.gridy = y;
  gbc.gridwidth = width;
  gbc.gridheight = height;
  gbc.insets = new Insets(1, 1, 1, 1);
  return gbc;
 }
}
