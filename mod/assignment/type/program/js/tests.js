<!--
    /* tests.js (v1.0 - 2007/06/18)
     * ********************************************************************* *
     * by Arkaitz Garro, June 2007                                           *
     * Copyright (c) 2007 Arkaitz Garro. All Rights Reserved.                *
     *                                                                       *
     * This code is free software; you can redistribute it and/or modify     *
     * it under the terms of the GNU General Public License as published by  *
     * the Free Software Foundation; either version 2 of the License, or     *
     * (at your option) any later version.                                   *
     *                                                                       *
     * This program is distributed in the hope that it will be useful,       *
     * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
     * GNU General Public License for more details:                          *
     *                                                                       *
     *          http://www.gnu.org/copyleft/gpl.html                         *
     * ********************************************************************* *
     * This JavaScript add / delete tests dynamicaly                         *
     *                                                                       *
     * @author Arkaitz Garro                                                 *
     * @package epaile                                                       *
     * ********************************************************************* *
     */

    // Add a new test
    function addTest() {
        divTest = document.createElement("tbody");               // New Test container
        divTest.setAttribute("id","test"+id);
        divTest.appendChild(createTitle());                      // New title row
        divTest.appendChild(createBoxes());                      // New boxes row
        document.getElementById("tests").appendChild(divTest);
        id++;
    }

    // Delete an existing test
    function delTest(testId) {
        var divTest = document.getElementById("test"+testId);
        document.getElementById("tests").removeChild(divTest);
        id--;
    }

    // Create test title (Test+id)
    function createTitle() {
        
        trTitle = document.createElement("tr");         // New title row
        tdTitle = document.createElement("td");         // New title cell       
        aDel = document.createElement("a");             // New link 'Delete test'
        imgDel = document.createElement("img");         // New image 'Delete test'
        txtTest = document.createElement("strong")      // New Test text

        
        trTitle.setAttribute("valign","top");                                   // Set attributes to title row
        trTitle.setAttribute("style","border-bottom: 1px solid #BBBBBB;");      // Set attributes to title row
        tdTitle.setAttribute("colspan","2");                                    // Set attributes to title cell
        aDel.setAttribute("href","#");                                          // Set attributes to link
        aDel.setAttribute("onclick","Javascript:delTest("+id+");");                 // Set attributes to link
        imgDel.setAttribute("src",pixpath+"/t/switch_minus.gif");    // Set attributes to image

        // Title structure
        txtTest.appendChild(document.createTextNode(" Test"));
        aDel.appendChild(imgDel);
        tdTitle.appendChild(aDel);
        tdTitle.appendChild(txtTest);
        trTitle.appendChild(tdTitle);

        return trTitle;
    }

    // Create input / output boxes
    function createBoxes() {
        
        tr = document.createElement("tr");          // New row
        tdIn = document.createElement("td");        // New 'input' cell 
        tdOut = document.createElement("td");       // New 'output' cell
        tbIn = document.createElement("input");     // New 'input' textBox
        tbOut = document.createElement("input");    // New 'output' textBox
        txtIn = document.createElement("strong")    // New input text
        txtOut = document.createElement("strong")   // New output text
        
        tdIn.setAttribute("align","right");         // Set attributes to cell
        
        tbIn.setAttribute("type","text");           // Set attributes to textBox 'input'
        tbIn.setAttribute("name","input[]");        // Set attributes to textBox 'input'
        tbIn.setAttribute("size","30");             // Set attributes to textBox 'input'
        
        tbOut.setAttribute("type","text");          // Set attributes to textBox 'output'
        tbOut.setAttribute("name","output[]");      // Set attributes to textBox 'output'
        tbOut.setAttribute("size","30");            // Set attributes to textBox 'output'
        tbIn.focus();
        
        
        // Row structure
        txtIn.appendChild(document.createTextNode("Input: "));
        txtOut.appendChild(document.createTextNode("Output: "));
        tdIn.appendChild(txtIn);
        tdIn.appendChild(tbIn);
        tdOut.appendChild(txtOut);
        tdOut.appendChild(tbOut);
        tr.appendChild(tdIn);
        tr.appendChild(tdOut);

        return tr;
    }
//-->