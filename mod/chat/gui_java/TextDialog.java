import java.awt.*;
import java.awt.event.*;

public class TextDialog
extends Dialog
implements ActionListener
{
 public TextDialog(Frame owner, String title, String textAreaText, String buttonText, boolean modal, int sizeX, int sizeY)
 {
  super(owner, title, modal);
  setBackground(Color.lightGray);
  setLocation(getToolkit().getScreenSize().width - sizeY, 0);
  
  //Layout setzen und Komponenten hinzufügen
  GridBagLayout gbl = new GridBagLayout();
  GridBagConstraints gbc;
  setLayout(gbl);
  
  //TextArea hinzufügen
  TextArea text = new TextArea(textAreaText, 1, 1, TextArea.SCROLLBARS_VERTICAL_ONLY);
  gbc = MainFrameGui.makegbc(0, 0, 1, 1);
  gbc.weightx = 100;
  gbc.weighty = 100;
  gbc.fill = GridBagConstraints.BOTH;
  gbl.setConstraints(text, gbc);
  add(text);
  
  //Button
  Button button = new Button(buttonText);
  gbc = MainFrameGui.makegbc(0, 1, 1, 1);
  gbc.fill = GridBagConstraints.NONE;
  gbc.anchor = GridBagConstraints.SOUTH;
  gbl.setConstraints(button, gbc);
  button.addActionListener(this);
  add(button);

  pack();
  setSize(sizeX, sizeY);
 }
 
 public void actionPerformed(ActionEvent event)
 {
  int i = getSize().height;
  int j = i / 20;
  int oldWidth = getSize().width;
  int oldHeight = getSize().height;
  
  System.out.println(i + " - " + j);
  
  while(i > -1)
  {
   //System.out.println(i);
   setSize(getSize().width, i);
   try
   {
    Thread.sleep(5);
   }
   catch (InterruptedException e)
   {
    //nix
   }
   i = i - j;
  }
  setVisible(false);
  setSize(oldWidth, oldHeight);
  dispose();
 }
}
