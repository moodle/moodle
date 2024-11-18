<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <script src='delayedLoading.js' type="text/javascript"></script>
 <title>pChart 2.x - Delayed loading</title>
 <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
 <style>
  html       { height: 100%; }
  body       { background-color: #F0F0F0; font-family: tahoma; font-size: 14px; height: 100%;}
  td  	     { font-family: tahoma; font-size: 11px; }
  div.txt    { font-family: tahoma; font-size: 11px; width: 660px; padding: 15px; }
  a.smallLink:link    { text-decoration: none; color: #6A6A6A; }
  a.smallLink:visited { text-decoration: none; color: #6A6A6A; }
  a.smallLink:hover   { text-decoration: underline; color: #6A6A6A; }
  a.pChart { text-decoration: none; color: #6A6A6A; }
 </style>
</head>
<body onscroll="scrollEvent();" onload="loaderInit();">

 <table style='border: 2px solid #FFFFFF;'><tr><td>
  <div style='font-size: 11px; padding: 2px; color: #FFFFFF; background-color: #666666; border-bottom: 3px solid #484848;'>&nbsp;Navigation</div>
  <table style='padding: 1px; background-color: #E0E0E0; border: 1px solid #D0D0D0; border-top: 1px solid #FFFFFF;'><tr>
   <td width=16><img src='../resources/application_view_tile.png' width=16 height=16 alt=''/></td>
   <td width=95>&nbsp;<a class=smallLink href='../'>Examples</a></td>
   <td width=16><img src='../resources/application_view_list.png' width=16 height=16 alt=''/></td>
   <td width=95>&nbsp;<a class=smallLink href='../sandbox/'>Sandbox</a></td>
   <td width=16><img src='../resources/application_view_list.png' width=16 height=16 alt=''/></td>
   <td width=95>&nbsp;<b>Delayed loader</b></td>
   <td width=16><img src='../resources/application_view_list.png' width=16 height=16 alt=''/></td>
   <td width=100>&nbsp;<a class=smallLink href='../imageMap/'>Image Map</a></td>
  </tr></table>
 </td></tr></table>

 <br/>

 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=1' data-pchart-alt='Picture1' data-pchart-width='700' data-pchart-height='230'>Picture 1</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=2' data-pchart-alt='Picture2'>Picture 2</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=3' data-pchart-alt='Picture3'>Picture 3</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=4' data-pchart-alt='Picture4'>Picture 4</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=5' data-pchart-alt='Picture5'>Picture 5</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=6' data-pchart-alt='Picture6'>Picture 6</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
 <a class='pChart' href='draw.php?Seed=7' data-pchart-alt='Picture7'>Picture 7</a>
 <div class=txt>
  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna
  aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
  Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint
  occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 </div>
</body>
</html>