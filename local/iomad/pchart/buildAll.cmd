ECHO OFF
CLS
ECHO Processing all examples
ECHO.
ECHO  [01/28] A simple line chart
 php -q %~dp0Example1.php
ECHO  [02/28] A cubic curve graph
 php -q %~dp0Example2.php
ECHO  [03/28] An overlayed bar graph
 php -q %~dp0Example3.php
ECHO  [04/28] Showing how to draw area
 php -q %~dp0Example4.php
ECHO  [05/28] A limits graph
 php -q %~dp0Example5.php
ECHO  [06/28] A simple filled line graph
 php -q %~dp0Example6.php
ECHO  [07/28] A filled cubic curve graph
 php -q %~dp0Example7.php
ECHO  [08/28] A radar graph
 php -q %~dp0Example8.php
ECHO  [09/28] Showing how to use labels
 php -q %~dp0Example9.php
ECHO  [10/28] A 3D exploded pie graph
 php -q %~dp0Example10.php
ECHO  [11/28] A true bar graph
 php -q %~dp0Example12.php
ECHO  [12/28] A 2D exploded pie graph
 php -q %~dp0Example13.php
ECHO  [13/28] A smooth flat pie graph
 php -q %~dp0Example14.php
ECHO  [14/28] Playing with line style and pictures inclusion
 php -q %~dp0Example15.php
ECHO  [15/28] Importing CSV data
 php -q %~dp0Example16.php
ECHO  [16/28] Playing with axis
 php -q %~dp0Example17.php
ECHO  [17/28] Missing values
 php -q %~dp0Example18.php
ECHO  [18/28] Error reporting
 php -q %~dp0Example19.php
ECHO  [19/28] Stacked bar graph
 php -q %~dp0Example20.php
ECHO  [20/28] Playing with background
 php -q %~dp0Example21.php
ECHO  [21/28] Customizing plot charts
 php -q %~dp0Example22.php
ECHO  [22/28] Playing with background - Bis
 php -q %~dp0Example23.php
ECHO  [23/28] X Versus Y chart
 php -q %~dp0Example24.php
ECHO  [24/28] Using shadows
 php -q %~dp0Example25.php
ECHO  [25/28] Two Y axis / shadow demonstration
 php -q %~dp0Example26.php
ECHO  [26/28] Naked and easy!
 php -q %~dp0Naked.php
ECHO  [27/28] Let's go fast, draw small!
 php -q %~dp0SmallGraph.php
ECHO  [28/28] A Small stacked chart
 php -q %~dp0SmallStacked.php
ECHO.
ECHO Rendering complete!
PAUSE
