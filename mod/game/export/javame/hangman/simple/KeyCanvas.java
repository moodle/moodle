import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import javax.microedition.lcdui.Image;
import javax.microedition.lcdui.Canvas;
import javax.microedition.lcdui.Command;
import javax.microedition.lcdui.Font;
import javax.microedition.lcdui.Graphics;
import java.util.Hashtable;
import java.util.Random;

class KeyCanvas extends Canvas {
  //private Font mFont = Font.getFont(Font.FACE_PROPORTIONAL, Font.STYLE_PLAIN, Font.SIZE_MEDIUM);

  private String mMessage = "";

  private String m_letters;
  private String m_lastletter;
  private String m_answer;
  private String m_question;
  private String m_allletters;
  private String m_guess;
  private String m_wrong;
  private int m_wrongletters;
  private String m_encoding="";
  private int m_state=0;
  //0=start 1=play 2=next word 3=lose 4=win
  private String m_init_letters;
  private String m_init_allletters;
  private int m_count_games=0;
  private int m_count_wins=0;
  private String m_filewords;
  public Hashtable m_hashLocales;
  private int m_keysize = 0;
  private int [] m_key;

  public KeyCanvas() throws IOException {
    m_state = 0;
    
    m_key = new int[ m_keysize+1];
    m_key[ 0] = 0;
    
    SelectFileWords();
    //LoadEncoding();
    LoadLocales();
            
    addCommand(new Command( getlocale( "exit"), Command.EXIT, 0));
  }
  
  private String decrypt( String s){
      String    ret="";
      
      if( m_keysize <=0 ){
          return s;
      }
      
      int len=s.length();
      for(int i=0; i < len; i+=m_keysize){
        for(int j=0; j < m_keysize; j++){
            int pos=i + m_key[ j];
            if( pos < len){
                ret = ret + s.charAt( pos);
            }
        }
      }
      
      return ret;
  }

  private String getlocale( String key){
      return (String)m_hashLocales.get( key);
  }

  private void SelectFileWords() throws IOException{
    Class c = this.getClass();
    InputStream is = c.getResourceAsStream( "hangman/hangman.txt");
    InputStreamReader reader = new InputStreamReader( is, "UTF-8");

    String line = null;
    // Read a single line from the file. null represents the EOF.
    while ((line = readLine(reader)) != null) {
        int pos = line.indexOf( "=");
        if( pos >= 0){
            m_filewords = "hangman/" + line.substring( 0, pos);
        }
        break;
    }        
    reader.close();    
  }
  
       private String readLine(InputStreamReader reader) throws IOException {
        // Test whether the end of file has been reached. If so, return null.
        int readChar = reader.read();
        if (readChar <= -1) {
            return null;
        }
        StringBuffer string = new StringBuffer();
        // Read until end of file or new line
        while (readChar > -1 && readChar != '\n') {
                                    
            // Append the read character to the string. Some operating systems
            // such as Microsoft Windows prepend newline character ('\n') with
            // carriage return ('\r'). This is part of the newline character and
            // therefore an exception that should not be appended to the string.
            if (readChar != '\r') {
                string.append( (char )readChar);
            }
            
            // Read the next character
            readChar = reader.read();          
        }
        return string.toString();
    }
    
  private void LoadLocales() throws IOException{
    m_hashLocales = new Hashtable();
    
    Class c = this.getClass();
    InputStream is = c.getResourceAsStream( "hangman/language.txt");
    InputStreamReader reader = new InputStreamReader( is, "UTF-8");
    String line = null;
    String  key, data;

    // Read a single line from the file. null represents the EOF.
    while ((line = readLine(reader)) != null) {
        int pos = line.indexOf( "=");
        if( pos >= 0){
            key = line.substring( 0, pos);
            data = line.substring( pos+1);
            m_hashLocales.put(key, data);
        }
    }        
    reader.close();          
  }
  
  protected boolean SetCurrentWord( String line) throws IOException{
      
      int pos=line.indexOf( '=');
      if( pos == -1){
          return false;
      }
      m_answer = line.substring( 0, pos);
      m_question = line.substring( pos+1);

      return true;
  }
  
  protected int SelectWord( String fname) throws IOException{   
    Class c = this.getClass();
    InputStream is = c.getResourceAsStream( fname);
    InputStreamReader reader = new InputStreamReader( is, "UTF-8");
    String line = null;
    int    count=0;
    // Read a single line from the file. null represents the EOF.
    while ((line = readLine(reader)) != null) {
        // Append the read line to the main form with a linefeed ('\n')
        count = count + 1;
    }        
    reader.close();
    
    //select randomly one word
    Random r = new Random();
    int curline = r.nextInt();
    curline = curline % count;
    if( curline < 0)
        curline = curline + count;
    
    InputStreamReader reader2 = new InputStreamReader( 
            getClass().getResourceAsStream(fname), "UTF-8");
    int i = 0;
    
    // Read a single line from the file. null represents the EOF.
    while ((line = readLine(reader2)) != null) {
        if( i == curline){    
            line = decrypt( line);
            SetCurrentWord( line);
            return 1;
        }
        i = i + 1;
    }        
    reader.close();
    
    return 0;
  }


  public void paint(Graphics g) {
    switch( m_state){
    case 0:
        try {
            paint_state_start(g);
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        break;
    case 1:   
        try {
            paint_state_play( g);
        } catch (StringIndexOutOfBoundsException ex) {
            ex.printStackTrace();
        }        
        break;
    case 2:
        try {
            paint_state_nextword(g);
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        break;
    case 3:   
        paint_state_lose( g);
        break;
    case 4:
        try {
            paint_state_win(g);
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        break;
    }
  }
  
private void paint_state_start(Graphics g) throws IOException{
    
    m_init_letters = getlocale( "keyboardletters");
    m_init_allletters = m_init_letters;
    
    String  sRemove = "1234567890:#";
    for(int i=0; i < sRemove.length(); i++){
        for(;;){
            int pos = m_init_allletters.indexOf( sRemove.charAt(i));
            if( pos < 0)
                break;
            m_init_allletters = m_init_allletters.substring( 0, pos) + m_init_allletters.substring( pos+1);
        }
    }
    
    m_state = 2;
    paint_state_nextword( g);
}

private void paint_state_nextword(Graphics g) throws IOException{
    m_letters = m_init_letters;
    m_allletters = m_init_allletters;
    
    SelectWord( m_filewords);

    m_lastletter = "";
    int len = m_answer.length();
    m_guess = "";
    m_wrong = "";
    for(int i=0; i < len; i++){
        m_guess = m_guess + "-";
    }
    m_wrongletters = 0;
    
    m_state = 1;    //play
    paint_state_play( g);    
}

private void paint_state_win(Graphics g) throws IOException{
    m_count_games++;
    m_count_wins++;
    
    m_state = 2;
    paint_state_nextword( g);
}

private void paint_state_lose(Graphics g){
    m_count_games++;
    
  //clear the screen
  g.setColor(255,255,255);
  g.fillRect(0, 0, getWidth(), getHeight());
  //set color to black
  g.setColor(0,0,0);
  //get the font height
  
  int y=10;
  
  int iHeight=g.getFont().getHeight();
  
  String s = m_answer;
  
  if( m_wrong.length() > 0){
    s = s + " (" + m_wrong + ")";
  }
  
  s = s + " [" + String.valueOf( m_count_wins) + "/" + String.valueOf( m_count_games) + "]";
  
  y = drawtextmultiline( g, s, 0, y);
        
  y  = drawtextmultiline( g, m_question, 0, y+iHeight);
  
  m_state = 2;
}
    
private void paint_state_play(Graphics g){
      
  //clear the screen
  g.setColor(255,255,255);
  g.fillRect(0, 0, getWidth(), getHeight());
  //set color to black
  g.setColor(0,0,0);
  //get the font height
  
  int y=0;
  Font font = g.getFont();
  
  int iHeight=g.getFont().getHeight();
  
  String s = m_guess;
  if( m_wrong.compareTo( "") != 0){
    s = s + " (" + m_wrong + ")";
  }
  
  s = s + " (" + String.valueOf( m_count_wins) + "/" + String.valueOf( m_count_games) + ")";
  
  y = drawtextmultiline( g, s, 0, y);
  
  int x = getWidth() - 3 * font.charWidth( '-');
  y = drawtextmultiline( g, mMessage, x, y) + iHeight;
      
  y  = drawtextmultiline( g, m_question, 0, y) + iHeight;
    
    Image im = null;
        try {
            String filename = "/hangman/hangman_" + String.valueOf(m_wrongletters) + ".jpg";
            im = Image.createImage( filename);
        } catch (IOException ex) {
            ex.printStackTrace();
        }

    int xMul = (100 * getWidth()) / im.getWidth();
    int yMul = (100 * (getHeight() - y)) / im.getHeight();
    
    if( yMul < xMul){
        xMul = yMul;
    }
    int cx = (xMul * im.getWidth()) / 100;
    int cy = (yMul * im.getHeight()) / 100;
    Image resize = resizeImage( im, cx, cy);
    
    g.drawImage(resize, 0, y, Graphics.LEFT | Graphics.TOP);
  }

  protected int drawtextmultiline(Graphics g, String text, int x, int y){
    Font font = g.getFont();
    int fontHeight = font.getHeight();
    //change string to char data
    char[] data = new char[text.length()];
    text.getChars(0, text.length(), data, 0);
    int width = getWidth();
    int lineWidth = 0;
    int charWidth = 0;
    int xStart = x;
    char ch;
    for(int ccnt=0; ccnt < data.length; ccnt++)
    {
       ch = data[ccnt];
       //measure the char to draw
       charWidth = font.charWidth(ch);
       lineWidth = lineWidth + charWidth;
       //see if a new line is needed
       if (lineWidth > width)
       {
            y = y + fontHeight;
            lineWidth = 0;
            x = xStart;
       }
       //draw the char
       g.drawChar(ch, x, y,
         Graphics.TOP|Graphics.LEFT);
       x = x + charWidth;
    }
    
    return y;
  }
  
  protected void keyPressed(int keyCode) {
    char  number;
    
    if( m_state == 2){
        repaint();
        return;
    }
    
    if( (keyCode >= 49) && (keyCode <= 57)){
        String  numbers = "123456789";
        number = numbers.charAt(keyCode - 49);
        
        int pos = m_letters.indexOf( number + ":");
        String letters = "";
        
        String letters2 = m_letters.substring( pos+2);        
        
        if( pos >= 0){
            pos = letters2.indexOf( '#');
            if( pos >= 0){
                letters = letters2.substring( 0, pos);
                //Compute the letters that correspond to the key pressed
                
                if( m_lastletter.compareTo( "") != 0){
                    pos = letters.indexOf( m_lastletter);
                                        
                    if( pos >= 0){
                        pos = pos + 1;
                        if( pos >= letters.length()){
                            pos = 0;
                        }
                    }else{
                        //different key
                        pos = 0;
                    }
                }else{
                    pos = 0;
                }
                if( (pos < letters.length()) && (pos >= 0)){
                    m_lastletter = letters.substring( pos, pos+1);
                    mMessage = m_lastletter;
                    repaint();
                }
                return;
            }
        }
        
        repaint();
        return;
    }
          
    int gameAction = getGameAction(keyCode);
    switch (gameAction) {
    case FIRE:
      OnFire();
      break;

   default:
      mMessage = String.valueOf( keyCode);
      break;
    }
 }
  
  protected void OnFire() {    
    int pos = m_guess.indexOf( m_lastletter);
    if( pos >= 0){
        //Already used
        return;
    }
    
    char ch = m_lastletter.charAt( 0);
    pos = m_answer.indexOf( ch);
    if( pos >= 0){
        //correct letter
        //Maybe there are many letters
        for(pos=0; pos < m_guess.length();pos++){
            if( m_answer.charAt( pos) == ch){
                m_guess = m_guess.substring( 0, pos) + m_lastletter + m_guess.substring( pos+1);
            }
        }
             
        pos = m_allletters.indexOf( m_lastletter);
        if( pos >= 0){
            m_allletters = m_allletters.substring( 0, pos) + "." + m_allletters.substring( pos+1);
        }
        
        remove_lastletter_from_keyboard();
        
        if( m_guess.indexOf( '-') < 0){
            m_state = 4;        //state=win;
        }
        
        repaint();
        return;
    }
    
    pos = m_allletters.indexOf( m_lastletter);
    if( pos < 0){
        return;
    }
   
    //wrong letter
    m_wrongletters = m_wrongletters + 1;    

    pos = m_allletters.indexOf( m_lastletter);
    if( pos >= 0){
        m_allletters = m_allletters.substring( 0, pos) + "." + m_allletters.substring( pos+1);
        m_wrong = m_wrong + m_lastletter;
    }
    
    remove_lastletter_from_keyboard();
    
    if( m_wrongletters >= 6){
        m_state = 3;  //state=lose
    }
    repaint();
  }
  
  private void remove_lastletter_from_keyboard(){
    int pos = m_letters.indexOf( m_lastletter);
    
    if( pos >= 0){
        m_letters = m_letters.substring( 0, pos) + m_letters.substring( pos+1);
    }
  }
  
  private Image resizeImage(Image src, int cx, int cy) {
      int srcWidth = src.getWidth();
      int srcHeight = src.getHeight();
      Image tmp = Image.createImage(cx, srcHeight);
      Graphics g = tmp.getGraphics();
      int ratio = (srcWidth << 16) / cx;
      int pos = ratio/2;

      //Horizontal Resize        

      for (int x = 0; x < cx; x++) {
          g.setClip(x, 0, 1, srcHeight);
          g.drawImage(src, x - (pos >> 16), 0, Graphics.LEFT | Graphics.TOP);
          pos += ratio;
      }

      Image resizedImage = Image.createImage(cx, cy);
      g = resizedImage.getGraphics();
      ratio = (srcHeight << 16) / cy;
      pos = ratio/2;        

      //Vertical resize

      for (int y = 0; y < cy; y++) {
          g.setClip(0, y, cx, 1);
          g.drawImage(tmp, 0, y - (pos >> 16), Graphics.LEFT | Graphics.TOP);
          pos += ratio;
      }
      return resizedImage;

  }//resize image    
}