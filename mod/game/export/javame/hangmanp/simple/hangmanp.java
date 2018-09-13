import java.io.IOException;
import javax.microedition.lcdui.Command;
import javax.microedition.lcdui.CommandListener;
import javax.microedition.lcdui.Display;
import javax.microedition.lcdui.Displayable;
import javax.microedition.midlet.MIDlet;

public class hangmanp extends MIDlet {
  public void startApp() {
    Displayable d = null;
        try {
            d = new keycanvasp();
        } catch (IOException ex) {
            ex.printStackTrace();
        }

    d.setCommandListener(new CommandListener() {
      public void commandAction(Command c, Displayable s) {
        notifyDestroyed();
      }
    });

    Display.getDisplay(this).setCurrent(d);
  }

  public void pauseApp() {
  }

  public void destroyApp(boolean unconditional) {
  }
}


