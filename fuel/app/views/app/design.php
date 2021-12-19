<HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<TITLE>City</TITLE>
<SCRIPT language="javascript">
    var wallct = 0;
    function addRow(tableID) {
        
        wallct = wallct+1;

        var table = document.getElementById(tableID);

        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount-1); // before add button

        var cell1 = row.insertCell(0);
        cell1.innerHTML = "壁" + wallct;

        var cell2 = row.insertCell(1);
        var element1 = document.createElement("input");
        element1.type = "file";
        element1.name = "walltex" + wallct;
        cell2.appendChild(element1);

        var cell3 = row.insertCell(2);
        var element2 = document.createElement("input");
        element2.type = "text";
        element2.name = "wallw" + wallct;
        element2.value = "0";
        element2.size = "5";
        cell3.appendChild(element2);

        var element3 = document.createElement("input");
        element3.type = "text";
        element3.name = "wallh" + wallct;
        element3.value = "0";
        element3.size = "5";
        cell3.appendChild(element3);

        ////var cell4 = row.insertCell(3);
        ////var element4 = document.createElement("input");
        ////element4.type = "button";
        ////element4.value = "-";
        ////element4.setAttribute("onclick","deleteRow('dataTable' , "+wallct+");");
        //element4.onclick = deleteRow('dataTable',wallct);
        ////cell4.appendChild(element4);
    }

    
    function init() {
        var opener = window.opener;
        if(opener) {
            // set design id if saved.
        }
    }
</SCRIPT>
</HEAD>
<body onload="init();">
    <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="design_id" value="<?= $design_id; ?>" />
    <input type="hidden" name="action" value="save" />
    名称：<input type="text" name="dname" value=""/>
    <TABLE id="dataTable" width="350px" border="1">
        <TR>
            <TD> 壁D </TD>
            <TD> <input name="walltex0" type="file" /> </TD>
            <td> <input type="text" name="wallw0" value="" size="5" />
                <input type="text" name="wallh0" value="" size="5" />
            </td>
        </TR>
        <TR>
            <TD> 屋根 </TD>
            <TD> <input name="rooftex" type="file" /> </TD>
            <td> <input type="text" name="roofw" value="" size="5" />
                <input type="text" name="roofh" value="" size="5" />
                <input type="text" name="roofr" value="" size="5" />
            </td>
        </TR>
        <tr>
            <td colspan="3">
                <input type="button" value="壁を追加" onclick="addRow('dataTable')" />
            </td>
        </tr>
    </TABLE>
    
    <input type="submit" value="作成" />
        
        
    </form>
    
</body>
</html>