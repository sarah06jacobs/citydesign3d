<HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<TITLE>City</TITLE>
<SCRIPT language="javascript">
    var wallct = <?= count($walls); ?>;
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

        var elementx = document.createElement("input");
        elementx.type = "hidden";
        elementx.name = "wallidx" + wallct;
        cell2.appendChild(elementx);

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
            <? if ( $result === "complete" ) { ?>
            opener.setDesign( <?= $design_id; ?> );
            window.close();
            <? } ?>
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
            <TD> <input name="walltex0" type="file" /> 
                <? if ($default_wall["img"] !== "") { ?>
                    <img src="<?= $default_wall["img"]; ?>" width="30" height="30" />
                <? } ?>
                <input type="hidden" name="wallidx0" value="<?= $default_wall["idx"]; ?>" />
            </TD>
            <td> <input type="text" name="wallw0" value="<?= $default_wall["w"]; ?>" size="5" />
                <input type="text" name="wallh0" value="<?= $default_wall["h"]; ?>" size="5" />
            </td>
        </TR>
        <TR>
            <TD> 屋根 </TD>
            <TD> <input name="rooftex" type="file" /> 
                <? if ($roof["img"] !== "") { ?>
                    <img src="<?= $roof["img"]; ?>" width="30" height="30" />
                <? } ?>
                <input type="hidden" name="roofidx" value="<?= $roof["idx"]; ?>" />
            </TD>
            <td> <input type="text" name="roofw" value="<?= $roof["w"]; ?>" size="5" />
                <input type="text" name="roofh" value="<?= $roof["h"]; ?>" size="5" />
                <input type="text" name="roofr" value="<?= $roof["r"]; ?>" size="5" />
            </td>
        </TR>
        <? foreach ($walls as $wall) { ?>
        
        <TR>
            <TD> 壁<?= $wall["idx"]; ?> </TD>
            <TD> <input name="walltex" type="file" /> 
                <? if ($wall["img"] !== "") { ?>
                    <img src="<?= $wall["img"]; ?>" width="30" height="30" />
                <? } ?>
                <input type="hidden" name="wallidx<?= $wall["idx"]; ?>" value="<?= $wall["idx"]; ?>" />
            </TD>
            <td> <input type="text" name="wallw<?= $wall["idx"]; ?>" value="<?= $wall["w"]; ?>" size="5" />
                <input type="text" name="wallh<?= $wall["idx"]; ?>" value="<?= $wall["h"]; ?>" size="5" />
            </td>
        </TR>


        <? } ?>
        <tr>
            <td colspan="3">
                <input type="button" value="+ 追加" onclick="addRow('dataTable')" />
            </td>
        </tr>
    </TABLE>
    
    <input type="submit" value="作成" />
        
        
    </form>
    
</body>
</html>