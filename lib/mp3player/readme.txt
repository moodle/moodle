Many aspects of the appearance and behaviour of the mp3 player can be customised,
these custom values are set by passing variables to the flash movie using "flashvars".
flashvars are supplied to the movie using

<param name="flashvars" value="some value">

alongside the other param tags, and with an extra attribute in the embed tag

flashvars="some value"

the "some value" part is a list of urlencoded variables

&variableName=value&anotherVariable=anotherValue&

 +--------------------------------------------------------------------------------------------------------------------+
 | Variable Name   | Possible Values                            | What it does                                        |
 |--------------------------------------------------------------------------------------------------------------------|
 | bgColour        | a colour in the format RRGGBB where        | sets the background colour of the player.           | 
 |                 | RR, GG and BB are hexadecimal values       |                                                     |
 |                 | (00 to FF) representing the Red, Green     |                                                     | 
 |                 | and Blue parts of the colour. eg           |                                                     |
 |                 | ffffff is white, 000000 is black,          |                                                     |
 |                 | ff9900 is orange, 000033 is a dark blue.   |                                                     |
 |                 | the default colour is black.               |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | btnColour       | the same as bgColour, the default colour   | sets the colour of the play and pause buttons.      |
 |                 | is white.                                  |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | btnBorderColour | the same as bgColour, the default colour   | the colour of the border around the buttons.        |
 |                 | is grey.                                   | make this the same as btnColour if you don't want   |
 |                 |                                            | a border.                                           |
 |--------------------------------------------------------------------------------------------------------------------|
 | iconColour      | the same as bgColour, the default colour   | the colour of the play/pause icons.                 |
 |                 | is black.                                  |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | iconOverColour  | the same as bgColour, the default colour   | the colour of the play/pause icons when the mouse   |
 |                 | is green.                                  | is over the button.                                 |
 |--------------------------------------------------------------------------------------------------------------------|
 | handleColour    | the same as bgColour, the default colour   | the colour of the handles on the playback slider,   |
 |                 | is white.                                  | volume and pan controls.                            |
 |--------------------------------------------------------------------------------------------------------------------|
 | trackColour     | the same as bgColour, the default colour   | the colour of the track for the playback slider,    |
 |                 | is grey.                                   | volume and pan controls.                            |
 |--------------------------------------------------------------------------------------------------------------------|
 | loaderColour    | the same as bgColour, the default colour   | the colour of the loading bar.                      |
 |                 | is white.                                  |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | font            | a name of a font, eg verdana. the          | the font used in the movie.                         |
 |                 | default value is Trebuchet MS.             |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | fontColour      | the same as bgColour, the default colour   | the colour of the text.                             |
 |                 | is white                                   |                                                     |
 |--------------------------------------------------------------------------------------------------------------------|
 | waitForPlay     | yes or no, the default is no.              | should the player wait until the play button is     |
 |                 |                                            | pressed before starting to download the file?       |
 |--------------------------------------------------------------------------------------------------------------------|
 | autoPlay        | yes or no, the default is yes.             | will the player wait until the play button is       |
 |                 |                                            | pressed before starting to play the file? if        |
 |--------------------------------------------------------------------------------------------------------------------|
 | buffer          | a value in seconds, the default is 20.     | how many seconds of the file should be buffered     |
 |                 |                                            | before playback starts?                             |
 |--------------------------------------------------------------------------------------------------------------------|
 | volText         | a string (could this come from the         | the text used in the player to label the volume     |
 |                 | language pack?) the default is "Vol".      | control.                                            |
 |--------------------------------------------------------------------------------------------------------------------|
 | panText         | the same as volText. the default is "Pan". | the text used in the player to label the pan        |
 |                 |                                            | control.                                            |
 +--------------------------------------------------------------------------------------------------------------------+


a brightly coloured example ;)

<param name="flashvars" value="&bgColour=ffff00&btnColour=00ff00&&btnBorderColour=ff9900iconColour=000000&iconOverColour=ff00ff&handleColour=ff00ff&trackColour=000000&loaderColour=ff9900&font=verdana&fontColour=ff0000&waitForPlay=no&dontAutoPlay=yes&buffer=30&">

the content of the value attribute would need to be repeated and included as the value of 
the flashvars attribute in the embed tag.


extra notes...

the song information comes from 2 sources, initally the player displays the filename (minus the file extension) 
as the title, once the file is fully loaded it checks the id3 data in the mp3 file for the songname, artist and year 
and displays this information instead of the filename.


Andy Walker (www.altoncollege.ac.uk)

