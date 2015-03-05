 /*
     functions.js - Sandbox JS

     Version     : 1.1.0
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 18/01/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 Action = "Render";

 function toggleAuto()
  {
   if ( document.getElementById("g_autopos").checked ) { Automatic  = true; } else { Automatic  = false; }
  }

 function doLayout()
  {
   if ( !Automatic ) { return(0); }

   g_width   = document.getElementById("g_width").value;
   g_height  = document.getElementById("g_height").value;

   document.getElementById("g_title_x").value = g_width/2;

   checkEnabledAxis();
  }

 /* Apply curvy corners */
 function applyCorners()
  {
   settings = {tl: { radius: 10 },tr: { radius: 10 },bl: { radius: 10 },br: { radius: 10 },antiAlias: true,autoPad: true,validTags: ["div"]}
   var myBoxObject = new curvyCorners(settings, "roundedCorner");
   myBoxObject.applyCornersToAll();
  }

 /* Set or remove the focus */
 function setFocus(Object,Mode)
  {
   if (Mode == true)
    { Object.style.borderColor = "#808080"; }
   else
    { Object.style.borderColor = "#D0D0D0"; }
  }

 function highlightDIV(ID)
  {
   if ( ID == CurrentDiv ) { return(0); }
   document.getElementById("menu"+ID).style.backgroundColor = "#F4F4F4";
  }

 function clearDIV(ID)
  {
   if ( ID == CurrentDiv ) { return(0); }
   document.getElementById("menu"+ID).style.backgroundColor = "#EAEAEA";
  }

 function toggleDIV(ID)
  {
   /* reset the tab styles */
   for (i=1;i<=6;i++)
    { if ( i != ID ) { document.getElementById("menu"+i).style.backgroundColor = "#EAEAEA"; document.getElementById("menu"+i).style.borderColor = "#FEFEFE"; } }

   /* hide the curently displayed tab */
   if ( CurrentDiv != ID ) { document.getElementById("tab"+CurrentDiv).style.display = "none"; }

   document.getElementById("tab"+ID).style.display = "block";

   CurrentDiv = ID;
   document.getElementById("menu"+ID).style.backgroundColor = "#D0D0D0";
   document.getElementById("menu"+ID).style.borderColor = "#B0B0B0";
  }

 function render()
  {
   Action = "Render";
   saveToSession();
  }

 function code()
  {
   Action = "Code";
   saveToSession();
  }

 function saveToSession()
  {
   saveGeneral();
  }

 function saveGeneral()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Saving configuration (General)";

   g_width   = document.getElementById("g_width").value;
   g_height  = document.getElementById("g_height").value;
   g_border  = document.getElementById("g_border").checked;
   g_aa      = document.getElementById("g_aa").checked;
   g_shadow  = document.getElementById("g_shadow").checked;
   g_transparent = document.getElementById("g_transparent").checked;
   g_autopos = document.getElementById("g_autopos").checked;

   g_title_enabled      = document.getElementById("g_title_enabled").checked;
   g_title              = document.getElementById("g_title").value;
   g_title_align        = document.getElementById("g_title_align").options[document.getElementById("g_title_align").selectedIndex].value;
   g_title_x            = document.getElementById("g_title_x").value;
   g_title_y            = document.getElementById("g_title_y").value;
   g_title_color        = document.getElementById("g_title_color").value;
   g_title_font         = document.getElementById("g_title_font").options[document.getElementById("g_title_font").selectedIndex].value;
   g_title_font_size    = document.getElementById("g_title_font_size").value;
   g_title_box		= document.getElementById("g_title_box").checked;

   g_solid_enabled      = document.getElementById("g_solid_enabled").checked;
   g_solid_color        = document.getElementById("g_solid_color").value;
   g_solid_dashed       = document.getElementById("g_solid_dashed").checked;

   g_gradient_enabled   = document.getElementById("g_gradient_enabled").checked;
   g_gradient_start     = document.getElementById("g_gradient_start").value;
   g_gradient_end       = document.getElementById("g_gradient_end").value;
   g_gradient_direction = document.getElementById("g_gradient_direction").options[document.getElementById("g_gradient_direction").selectedIndex].value;
   g_gradient_alpha     = document.getElementById("g_gradient_alpha").value;

   var reg=new RegExp("(#)", "g");
   g_title_color    = g_title_color.replace(reg,"");
   g_solid_color    = g_solid_color.replace(reg,"");
   g_gradient_start = g_gradient_start.replace(reg,"");
   g_gradient_end   = g_gradient_end.replace(reg,"");

   URL = "script/session.php?g_width="+g_width+"&g_height="+g_height+"&g_border="+g_border+"&g_aa="+g_aa+"&g_shadow="+g_shadow+"&g_autopos="+g_autopos
    +"&g_title_enabled="+g_title_enabled+"&g_title="+g_title+"&g_title_align="+g_title_align+"&g_title_x="+g_title_x
    +"&g_title_y="+g_title_y+"&g_title_color="+g_title_color+"&g_title_font="+g_title_font+"&g_title_font_size="+g_title_font_size+"&g_title_box="+g_title_box
    +"&g_solid_enabled="+g_solid_enabled+"&g_solid_color="+g_solid_color+"&g_solid_dashed="+g_solid_dashed
    +"&g_gradient_enabled="+g_gradient_enabled+"&g_gradient_start="+g_gradient_start+"&g_gradient_end="+g_gradient_end
    +"&g_gradient_direction="+g_gradient_direction+"&g_gradient_alpha="+g_gradient_alpha+"&g_transparent="+g_transparent
    +"&Seed="+Math.random(100);

   push(URL,1);
  }

 function saveData()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Saving configuration (Data)";

   d_serie1_enabled	= document.getElementById("d_serie1_enabled").checked;
   d_serie2_enabled	= document.getElementById("d_serie2_enabled").checked;
   d_serie3_enabled	= document.getElementById("d_serie3_enabled").checked;
   d_absissa_enabled	= document.getElementById("d_absissa_enabled").checked;

   d_serie1_name	= document.getElementById("d_serie1_name").value;
   d_serie2_name	= document.getElementById("d_serie2_name").value;
   d_serie3_name	= document.getElementById("d_serie3_name").value;

   d_serie1_axis	= document.getElementById("d_serie1_axis").options[document.getElementById("d_serie1_axis").selectedIndex].value;
   d_serie2_axis	= document.getElementById("d_serie2_axis").options[document.getElementById("d_serie2_axis").selectedIndex].value;
   d_serie3_axis	= document.getElementById("d_serie3_axis").options[document.getElementById("d_serie3_axis").selectedIndex].value;

   data0 = ""; data1 = ""; data2 = ""; absissa = "";
   for(i=0;i<8;i++)
    {
     data0 = data0 + "!" + document.getElementById("d_serie1_data"+i).value;
     data1 = data1 + "!" + document.getElementById("d_serie2_data"+i).value;
     data2 = data2 + "!" + document.getElementById("d_serie3_data"+i).value;
     absissa = absissa + "!" + document.getElementById("d_absissa_data"+i).value;
    }

   d_normalize_enabled	= document.getElementById("d_normalize_enabled").checked;

   d_axis0_name		= document.getElementById("d_axis0_name").value;
   d_axis1_name		= document.getElementById("d_axis1_name").value;
   d_axis2_name		= document.getElementById("d_axis2_name").value;

   d_axis0_unit		= document.getElementById("d_axis0_unit").value;
   d_axis1_unit		= document.getElementById("d_axis1_unit").value;
   d_axis2_unit		= document.getElementById("d_axis2_unit").value;

   d_axis0_position	= document.getElementById("d_axis0_position").options[document.getElementById("d_axis0_position").selectedIndex].value;
   d_axis1_position	= document.getElementById("d_axis1_position").options[document.getElementById("d_axis1_position").selectedIndex].value;
   d_axis2_position	= document.getElementById("d_axis2_position").options[document.getElementById("d_axis2_position").selectedIndex].value;

   d_axis0_format	= document.getElementById("d_axis0_format").options[document.getElementById("d_axis0_format").selectedIndex].value;
   d_axis1_format	= document.getElementById("d_axis1_format").options[document.getElementById("d_axis1_format").selectedIndex].value;
   d_axis2_format	= document.getElementById("d_axis2_format").options[document.getElementById("d_axis2_format").selectedIndex].value;

   URL = "script/session.php?d_serie1_enabled="+d_serie1_enabled+"&d_serie2_enabled="+d_serie2_enabled+"&d_serie3_enabled="+d_serie3_enabled
        +"&d_absissa_enabled="+d_absissa_enabled+"&d_serie1_axis="+d_serie1_axis+"&d_serie2_axis="+d_serie2_axis+"&d_serie3_axis="+d_serie3_axis
        +"&data0="+data0+"&data1="+data1+"&data2="+data2+"&absissa="+absissa+"&d_axis0_name="+d_axis0_name+"&d_axis1_name="+d_axis1_name
        +"&d_axis2_name="+d_axis2_name+"&d_axis0_unit="+d_axis0_unit+"&d_axis1_unit="+d_axis1_unit+"&d_axis2_unit="+d_axis2_unit
        +"&d_axis0_position="+d_axis0_position+"&d_axis1_position="+d_axis1_position+"&d_axis2_position="+d_axis2_position
        +"&d_normalize_enabled="+d_normalize_enabled+"&d_serie1_name="+d_serie1_name+"&d_serie2_name="+d_serie2_name+"&d_serie3_name="+d_serie3_name
        +"&d_axis0_format="+d_axis0_format+"&d_axis1_format="+d_axis1_format+"&d_axis2_format="+d_axis2_format;

   push(URL,2);
  }

 function saveScale()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Saving configuration (Scale)";

   s_x			= document.getElementById("s_x").value;
   s_y			= document.getElementById("s_y").value;
   s_width		= document.getElementById("s_width").value;
   s_height		= document.getElementById("s_height").value;
   s_direction		= document.getElementById("s_direction").options[document.getElementById("s_direction").selectedIndex].value;
   s_arrows_enabled	= document.getElementById("s_arrows_enabled").checked;
   s_mode		= document.getElementById("s_mode").options[document.getElementById("s_mode").selectedIndex].value;
   s_cycle_enabled	= document.getElementById("s_cycle_enabled").checked;
   s_x_margin		= document.getElementById("s_x_margin").value;
   s_y_margin		= document.getElementById("s_y_margin").value;
   s_automargin_enabled	= document.getElementById("s_automargin_enabled").checked;
   s_font		= document.getElementById("s_font").options[document.getElementById("s_font").selectedIndex].value;
   s_font_size		= document.getElementById("s_font_size").value;
   s_font_color		= document.getElementById("s_font_color").value;

   s_x_labeling		= document.getElementById("s_x_labeling").options[document.getElementById("s_x_labeling").selectedIndex].value;
   s_x_skip		= document.getElementById("s_x_skip").value;
   s_x_label_rotation	= document.getElementById("s_x_label_rotation").value;

   s_grid_color		= document.getElementById("s_grid_color").value;
   s_grid_alpha		= document.getElementById("s_grid_alpha").value;
   s_grid_x_enabled	= document.getElementById("s_grid_x_enabled").checked;
   s_grid_y_enabled	= document.getElementById("s_grid_y_enabled").checked;

   s_ticks_color	= document.getElementById("s_ticks_color").value;
   s_ticks_alpha	= document.getElementById("s_ticks_alpha").value;
   s_subticks_color	= document.getElementById("s_subticks_color").value;
   s_subticks_alpha	= document.getElementById("s_subticks_alpha").value;
   s_subticks_enabled	= document.getElementById("s_subticks_enabled").checked;

   URL = "script/session.php?s_x="+s_x+"&s_y="+s_y+"&s_width="+s_width+"&s_height="+s_height+"&s_direction="+s_direction
        +"&s_arrows_enabled="+s_arrows_enabled+"&s_mode="+s_mode+"&s_cycle_enabled="+s_cycle_enabled+"&s_x_margin="+s_x_margin
        +"&s_y_margin="+s_y_margin+"&s_automargin_enabled="+s_automargin_enabled+"&s_x_labeling="+s_x_labeling+"&s_x_skip="+s_x_skip
        +"&s_x_label_rotation="+s_x_label_rotation+"&s_grid_color="+s_grid_color+"&s_grid_alpha="+s_grid_alpha+"&s_grid_x_enabled="+s_grid_x_enabled
        +"&s_grid_y_enabled="+s_grid_y_enabled+"&s_ticks_color="+s_ticks_color+"&s_ticks_alpha="+s_ticks_alpha+"&s_subticks_color="+s_subticks_color
        +"&s_subticks_alpha="+s_subticks_alpha+"&s_subticks_enabled="+s_subticks_enabled+"&s_font="+s_font+"&s_font_size="+s_font_size
        +"&s_font_color="+s_font_color+"&Seed="+Math.random(100);

   push(URL,3);
  }

 function saveChart()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Saving configuration (Chart)";

   c_family			= document.getElementById("c_family").options[document.getElementById("c_family").selectedIndex].value;
   c_display_values		= document.getElementById("c_display_values").checked;
   c_break_color		= document.getElementById("c_break_color").value;
   c_break			= document.getElementById("c_break").checked;

   c_plot_size			= document.getElementById("c_plot_size").value;
   c_border_size		= document.getElementById("c_border_size").value;
   c_border_enabled		= document.getElementById("c_border_enabled").checked;

   c_bar_classic		= document.getElementById("c_bar_classic").checked;
   c_bar_rounded		= document.getElementById("c_bar_rounded").checked;
   c_bar_gradient		= document.getElementById("c_bar_gradient").checked;
   c_around_zero1		= document.getElementById("c_around_zero1").checked;

   c_transparency		= document.getElementById("c_transparency").value;
   c_forced_transparency	= document.getElementById("c_forced_transparency").checked;
   c_around_zero2		= document.getElementById("c_around_zero2").checked;

   URL = "script/session.php?c_family="+c_family+"&c_display_values="+c_display_values+"&c_plot_size="+c_plot_size+"&c_border_size="+c_border_size+"&c_border_enabled="+c_border_enabled
        +"&c_bar_classic="+c_bar_classic+"&c_bar_rounded="+c_bar_rounded+"&c_bar_gradient="+c_bar_gradient+"&c_around_zero1="+c_around_zero1
        +"&c_transparency="+c_transparency+"&c_forced_transparency="+c_forced_transparency+"&c_around_zero2="+c_around_zero2
        +"&c_break="+c_break+"&c_break_color="+c_break_color;

   push(URL,4);
  }

 function saveLegend()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Saving configuration (Legend and Thresholds)";

   l_enabled		= document.getElementById("l_enabled").checked;

   l_font		= document.getElementById("l_font").options[document.getElementById("l_font").selectedIndex].value;
   l_font_size		= document.getElementById("l_font_size").value;
   l_font_color		= document.getElementById("l_font_color").value;

   l_margin		= document.getElementById("l_margin").value;
   l_alpha		= document.getElementById("l_alpha").value;
   l_format		= document.getElementById("l_format").options[document.getElementById("l_format").selectedIndex].value;

   l_orientation	= document.getElementById("l_orientation").options[document.getElementById("l_orientation").selectedIndex].value;
   l_box_size		= document.getElementById("l_box_size").value;

   l_position		= document.getElementById("l_position").options[document.getElementById("l_position").selectedIndex].value;
   l_x			= document.getElementById("l_x").value;
   l_y			= document.getElementById("l_y").value;

   l_family		= document.getElementById("l_family").options[document.getElementById("l_family").selectedIndex].value;

   t_enabled		= document.getElementById("t_enabled").checked;

   t_value		= document.getElementById("t_value").value;
   t_axis0		= document.getElementById("t_axis0").checked;
   t_axis1		= document.getElementById("t_axis1").checked;
   t_axis2		= document.getElementById("t_axis2").checked;

   t_color		= document.getElementById("t_color").value;
   t_alpha		= document.getElementById("t_alpha").value;
   t_ticks		= document.getElementById("t_ticks").checked;

   t_caption		= document.getElementById("t_caption").value;
   t_box		= document.getElementById("t_box").checked;
   t_caption_enabled	= document.getElementById("t_caption_enabled").checked;

   sl_enabled		= document.getElementById("sl_enabled").checked;
   sl_shaded		= document.getElementById("sl_shaded").checked;
   sl_caption_enabled	= document.getElementById("sl_caption_enabled").checked;
   sl_caption_line	= document.getElementById("sl_caption_line").checked;

   p_template		= document.getElementById("p_template").options[document.getElementById("p_template").selectedIndex].value;

   if ( t_axis0 ) { t_axis = 0; }
   if ( t_axis1 ) { t_axis = 1; }
   if ( t_axis2 ) { t_axis = 2; }

   URL = "script/session.php?l_enabled="+l_enabled+"&l_font="+l_font+"&l_font_size="+l_font_size+"&l_font_color="+l_font_color
        +"&l_margin="+l_margin+"&l_alpha="+l_alpha+"&l_format="+l_format+"&l_orientation="+l_orientation+"&l_box_size="+l_box_size
        +"&t_enabled="+t_enabled+"&t_value="+t_value+"&t_axis="+t_axis+"&t_color="+t_color+"&t_alpha="+t_alpha+"&t_ticks="+t_ticks
        +"&t_caption="+t_caption+"&t_box="+t_box+"&t_caption_enabled="+t_caption_enabled+"&l_position="+l_position+"&l_x="+l_x+"&l_y="+l_y
        +"&p_template="+p_template+"&l_family="+l_family+"&sl_enabled="+sl_enabled+"&sl_shaded="+sl_shaded+"&sl_caption_enabled="+sl_caption_enabled
        +"&sl_caption_line="+sl_caption_line;

   push(URL,5);
  }

 function randomize()
  {
   for(i=0;i<8;i++)
    {
     document.getElementById("d_serie1_data"+i).value = Math.ceil(Math.random()*100-50);
     document.getElementById("d_serie2_data"+i).value = Math.ceil(Math.random()*100-50);
     document.getElementById("d_serie3_data"+i).value = Math.ceil(Math.random()*100-50);
    }
  }

 function setColors()
  {
   applyColor("g_title_color","g_title_color_show");
   applyColor("g_solid_color","g_solid_color_show");
   applyColor("g_gradient_start","g_gradient_start_show");
   applyColor("g_gradient_end","g_gradient_end_show");
   applyColor("s_font_color","s_font_color_show");
   applyColor("s_grid_color","s_grid_color_show");
   applyColor("s_ticks_color","s_ticks_color_show");
   applyColor("s_subticks_color","s_subticks_color_show");
   applyColor("l_font_color","l_font_color_show");
   applyColor("t_color","t_color_show");
   applyColor("c_break_color","c_break_color_show");
  }

 function applyColor(SourceID,TargetID)
  {
   color = document.getElementById(SourceID).value;
   color = color.replace("#","");
   document.getElementById(TargetID).style.backgroundColor = "#"+color;
  }

 function checkChartSettings()
  {
   ChartFamily = document.getElementById("c_family").options[document.getElementById("c_family").selectedIndex].value;

   disableItem("c_plot_size"); disableItem("c_border_size"); disableCheck("c_border_enabled");
   disableRadio("c_bar_classic"); disableRadio("c_bar_rounded"); disableRadio("c_bar_gradient"); disableCheck("c_around_zero1");
   disableItem("c_transparency"); disableCheck("c_forced_transparency"); disableCheck("c_around_zero2");

   if ( ChartFamily == "plot" )
    { enableItem("c_plot_size"); enableItem("c_border_size"); enableCheck("c_border_enabled"); checkPlotBorder(); }

   if ( ChartFamily == "bar" || ChartFamily == "sbar" )
    { enableRadio("c_bar_classic"); enableRadio("c_bar_rounded"); enableRadio("c_bar_gradient"); enableCheck("c_around_zero1"); }

   if ( ChartFamily == "fspline" || ChartFamily == "area" || ChartFamily == "sarea" || ChartFamily == "fstep" )
    { enableItem("c_transparency"); enableCheck("c_forced_transparency"); enableCheck("c_around_zero2"); checkAreaChart(); }

   if ( Automatic )
    {
     if ( ChartFamily == "sbar" || ChartFamily == "sarea" )
      document.getElementById("s_mode").value = "SCALE_MODE_ADDALL";
     else
      document.getElementById("s_mode").value = "SCALE_MODE_FLOATING";
    }
  }

 function checkLegend()
  {
   l_position = document.getElementById("l_position").options[document.getElementById("l_position").selectedIndex].value;

   if ( l_position == "Manual" )
    { enableItem("l_x"); enableItem("l_y"); }
   else
    { disableItem("l_x"); disableItem("l_y"); }
  }

 function checkPlotBorder()
  {
   borderEnabled = document.getElementById("c_border_enabled").checked;
   if ( borderEnabled ) { enableItem("c_border_size"); } else { disableItem("c_border_size"); }
  }

 function checkAreaChart()
  {
   c_forced_transparency = document.getElementById("c_forced_transparency").checked;
   if ( c_forced_transparency ) { enableItem("c_transparency"); } else { disableItem("c_transparency"); }
  }

 function toggleSubTicks()
  {
   if ( !document.getElementById("s_subticks_enabled").checked )
    { disableItem("s_subticks_color"); disableItem("s_subticks_alpha"); }
   else
    { enableItem("s_subticks_color"); enableItem("s_subticks_alpha"); }
  }

 function toggleAutoMargins()
  {
   if ( document.getElementById("s_automargin_enabled").checked )
    { disableItem("s_x_margin"); disableItem("s_y_margin"); }
   else
    { enableItem("s_x_margin"); enableItem("s_y_margin"); }
  }

 function checkEnabledAxis()
  {
   Serie1Enabled = document.getElementById("d_serie1_enabled").checked;
   Serie2Enabled = document.getElementById("d_serie2_enabled").checked;
   Serie3Enabled = document.getElementById("d_serie3_enabled").checked;
   Serie1Binding = document.getElementById("d_serie1_axis").options[document.getElementById("d_serie1_axis").selectedIndex].value;
   Serie2Binding = document.getElementById("d_serie2_axis").options[document.getElementById("d_serie2_axis").selectedIndex].value;
   Serie3Binding = document.getElementById("d_serie3_axis").options[document.getElementById("d_serie3_axis").selectedIndex].value;

   Series = 0;
   if ( Serie1Enabled ) { Series++; }
   if ( Serie2Enabled ) { Series++; }
   if ( Serie3Enabled ) { Series++; }

   if ( (Serie1Binding != 0 || !Serie1Enabled) && (Serie2Binding != 0 || !Serie2Enabled) && (Serie3Binding != 0 || !Serie3Enabled) )
    { disableItem("d_axis0_name"); disableItem("d_axis0_unit"); disableItem("d_axis0_position"); disableItem("d_axis0_format"); }
   else
    { enableItem("d_axis0_name"); enableItem("d_axis0_unit"); enableItem("d_axis0_position"); enableItem("d_axis0_format"); }

   if ( (Serie1Binding != 1 || !Serie1Enabled) && (Serie2Binding != 1 || !Serie2Enabled) && (Serie3Binding != 1 || !Serie3Enabled) )
    { disableItem("d_axis1_name"); disableItem("d_axis1_unit"); disableItem("d_axis1_position"); disableItem("d_axis1_format"); }
   else
    { enableItem("d_axis1_name"); enableItem("d_axis1_unit"); enableItem("d_axis1_position"); enableItem("d_axis1_format"); }

   if ( (Serie1Binding != 2 || !Serie1Enabled) && (Serie2Binding != 2 || !Serie2Enabled) && (Serie3Binding != 2 || !Serie3Enabled) )
    { disableItem("d_axis2_name"); disableItem("d_axis2_unit"); disableItem("d_axis2_position"); disableItem("d_axis2_format"); }
   else
    { enableItem("d_axis2_name"); enableItem("d_axis2_unit"); enableItem("d_axis2_position"); enableItem("d_axis2_format"); }

   if ( Automatic )
    {
     sl_enabled  = document.getElementById("sl_enabled").checked;
     g_width     = document.getElementById("g_width").value;
     g_height    = document.getElementById("g_height").value;
     s_direction = document.getElementById("s_direction").options[document.getElementById("s_direction").selectedIndex].value;

     leftSeries = 0; rightSeries = 0;

     if ( !document.getElementById("d_axis0_position").disabled && document.getElementById("d_axis0_position").options[document.getElementById("d_axis0_position").selectedIndex].value == "left" ) { leftSeries++; }
     if ( !document.getElementById("d_axis0_position").disabled && document.getElementById("d_axis0_position").options[document.getElementById("d_axis0_position").selectedIndex].value == "right" ) { rightSeries++; }
     if ( !document.getElementById("d_axis1_position").disabled && document.getElementById("d_axis1_position").options[document.getElementById("d_axis1_position").selectedIndex].value == "left" ) { leftSeries++; }
     if ( !document.getElementById("d_axis1_position").disabled && document.getElementById("d_axis1_position").options[document.getElementById("d_axis1_position").selectedIndex].value == "right" ) { rightSeries++; }
     if ( !document.getElementById("d_axis2_position").disabled && document.getElementById("d_axis2_position").options[document.getElementById("d_axis2_position").selectedIndex].value == "left" ) { leftSeries++; }
     if ( !document.getElementById("d_axis2_position").disabled && document.getElementById("d_axis2_position").options[document.getElementById("d_axis2_position").selectedIndex].value == "right" ) { rightSeries++; }

     if ( s_direction == "SCALE_POS_LEFTRIGHT" )
      {
       if ( leftSeries == 0 ) { leftOffset = 20; } else { leftOffset = 10; }
       if ( rightSeries == 0 ) { rightOffset = 25; } else { rightOffset = 15; }

       leftMargin = leftOffset + 40 * leftSeries;
       width = g_width - leftMargin - 40 * rightSeries - rightOffset;

       if ( sl_enabled ) { BottomOffset = Series*15; } else { BottomOffset = 0; }

       document.getElementById("s_x").value = leftMargin;
       document.getElementById("s_y").value = 50;
       document.getElementById("s_width").value = width;
       document.getElementById("s_height").value = g_height - 50 - 40 - BottomOffset;
      }
     else
      {
       if ( leftSeries == 0 ) { topOffset = 40; } else { topOffset = 40; }
       if ( rightSeries == 0 ) { bottomOffset = 25; } else { bottomOffset = 15; }

       topMargin = topOffset + 30 * leftSeries;
       height = g_height - topMargin - 30 * rightSeries - bottomOffset;

       if ( sl_enabled ) { RightOffset = Series*15; } else { RightBottomOffset = 0; }

       document.getElementById("s_x").value = 70;
       document.getElementById("s_y").value = topMargin;
       document.getElementById("s_width").value = g_width - 70 - 40 - RightOffset;
       document.getElementById("s_height").value = height;
      }
    }
  }

 function disableItem(ID)
  {
   document.getElementById(ID).style.backgroundColor = "#E0E0E0";
   document.getElementById(ID).style.color = "#A0A0A0";
   document.getElementById(ID).disabled = true;
  }

 function disableCheck(ID)
  {
   document.getElementById(ID).style.color = "#A0A0A0";
   document.getElementById(ID).disabled = true;
  }

 function disableRadio(ID)
  {
   document.getElementById(ID).disabled = true;
  }

 function enableItem(ID)
  {
   document.getElementById(ID).style.backgroundColor = "#FFFFFF";
   document.getElementById(ID).style.color = "#707070";
   document.getElementById(ID).disabled = false;
  }

 function enableCheck(ID)
  {
   document.getElementById(ID).style.color = "#707070";
   document.getElementById(ID).disabled = false;
  }

 function enableRadio(ID)
  {
   document.getElementById(ID).disabled = false;
  }

 function setDefaultAbsissa()
  {
   document.getElementById("d_absissa_data0").value = "January";
   document.getElementById("d_absissa_data1").value = "February";
   document.getElementById("d_absissa_data2").value = "March";
   document.getElementById("d_absissa_data3").value = "April";
   document.getElementById("d_absissa_data4").value = "May";
   document.getElementById("d_absissa_data5").value = "June";
   document.getElementById("d_absissa_data6").value = "July";
   document.getElementById("d_absissa_data7").value = "August";
  }

 function push(URL,nextStep)
  {
   var xmlhttp=false;   
   /*@cc_on @*/  
   /*@if (@_jscript_version >= 5)  
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); } catch (E) { xmlhttp = false; } }  
   @end @*/  
  
   if (!xmlhttp && typeof XMLHttpRequest!='undefined')   
    { try { xmlhttp = new XMLHttpRequest(); } catch (e) { xmlhttp=false; } }   
  
   if (!xmlhttp && window.createRequest)   
    { try { xmlhttp = window.createRequest(); } catch (e) { xmlhttp=false; } }   
  
   xmlhttp.open("GET", URL,true);

   xmlhttp.onreadystatechange=function() {   
    if (xmlhttp.readyState==4)
     {
      if ( nextStep == 1 ) { saveData(); }
      if ( nextStep == 2 ) { saveScale(); }
      if ( nextStep == 3 ) { saveChart(); }
      if ( nextStep == 4 ) { saveLegend(); }
      if ( nextStep == 5 )
       {
        if ( Action == "Render" )
         doRender();
        else
         push("script/render.php?Mode=Source&Seed="+Math.random(100),6);
       }
      if ( nextStep == 6 )
       {
        document.getElementById("result_area").innerHTML = "<pre name='code'>"+xmlhttp.responseText+"</pre>";
       }
     }
    }   
   xmlhttp.send(null)   
  }

 function doRender()
  {
   document.getElementById("result_area").innerHTML = "<img src='graphix/wait.gif' width='24' height='24' alt=''><br/>Rendering";

   RandomKey = Math.random(100);
   URL       = "script/render.php?Seed=" + RandomKey;
 
   StartFade();
  }

 function StartFade()
  {
   Loader     = new Image();   
   Loader.src = URL;   
   setTimeout("CheckLoadingStatus()", 200);   
  }

 function CheckLoadingStatus()   
  {   
   if ( Loader.complete == true )   
    {
     changeOpac(0, "result_area");
     HTMLResult = "<center><img src='" + URL + "' alt=''/></center>";
     document.getElementById("result_area").innerHTML = HTMLResult;

     opacity("result_area",0,100,500);
    }
   else  
    setTimeout("CheckLoadingStatus()", 200);   
  }   

 function changeOpac(opacity, id)   
  {   
   var object = document.getElementById(id).style;   
   object.opacity = (opacity / 100);   
   object.MozOpacity = (opacity / 100);   
   object.KhtmlOpacity = (opacity / 100);   
   object.filter = "alpha(opacity=" + opacity + ")";   
  }   

 function opacity(id, opacStart, opacEnd, millisec)
  {
   var speed = Math.round(millisec / 100);
   var timer = 0;

   if(opacStart > opacEnd)
    {
     for(i = opacStart; i >= opacEnd; i--)
      {
       setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
       timer++;
      }
    }
   else if(opacStart < opacEnd)
    {
     for(i = opacStart; i <= opacEnd; i++)
      {
       setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
       timer++;
      }
    }
  }