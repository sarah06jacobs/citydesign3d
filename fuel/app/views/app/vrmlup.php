<HTML>
<HEAD>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<TITLE>City</TITLE>
<SCRIPT language="javascript">
 function init() {
    var opener = window.opener;
    if(opener) {
        // set design id if saved.
        <? if ( $result === "complete" ) { ?>
        opener.setVrml('<?= $vrml_id; ?>' , '<?= $vfname; ?>' , '<?= $layer; ?>' , '<?= $tname; ?>', '<?= $cdate; ?>', '<?= $points; ?>' ,  '<?= $tfm; ?>');
        window.close();
        <? } ?>
    }
}

var wallct = 1;
function addRow(tableID) {
    
    wallct = wallct+1;

    var table = document.getElementById(tableID);

    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount-1); // before add button

    var cell1 = row.insertCell(0);
    cell1.innerHTML = wallct + ".";

    var cell2 = row.insertCell(1);
    var element1 = document.createElement("input");
    element1.type = "file";
    element1.name = "vtex" + wallct;
    cell2.appendChild(element1);

    document.getElementById("wallct").value = wallct;
}

function closeWin() {
	window.close();
}
</SCRIPT>
</HEAD>
<body onload="init();">
    <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload" />
    <input type="hidden" name="vrmlid" value="<?= $vrml_id ?>" />
    <input type="hidden" name="layer" value="<?= $layer ?>" />
    
    <br><b>3Dファイル</b><br>
    <TABLE id="dataTable" width="350px" border="1">
        <TR>
            <TD> WRLファイル： </TD>
            <TD> <input name="vrmlfile" type="file" />  
            </TD>
            
        </TR>
        <TR>
            <TD> 点群XML ファイル： </TD>
            <TD> <input name="tengunfile" type="file" />  
            </TD>
            
        </TR>
        <TR>
            <TD> 点群CSV ファイル： </TD>
            <TD> <input name="csvfile" type="file" />  
            </TD>
        </TR>
        <TR>
            <TD> 点群 頂点削減： </TD>
            <TD> 
               点(n) %  <select name="skip">
                    <option value=0>0</option>
                    <option value=2>2</option>
                    <option value=4>4</option>
                    <option value=5>5</option>
                    <option value=8>8</option>
                    <option value=10>10</option>
                    <option value=20>20</option>
                    <option value=100>100</option>
                </select> 含む  
            </TD>
            
        </TR>
    </TABLE>

    <br><b>関連テキスチャー</b><br>
    <TABLE id="imageTable" width="350px" border="1">
        <TR>
            <TD> 1. </TD>
            <TD> <input name="vtex1" type="file" /> </TD>
        </TR>
        <tr>
            <td colspan="2">
                <input type="button" value="+ 追加" onclick="addRow('imageTable')" />
            </td>
        </tr>
    </TABLE>
    <input type="hidden" name="wallct" value="1" />

    <br><br>
    <input type="submit" value="アップロード" name="submit" /> &nbsp; <input type="button" value="キャンセル" onclick="closeWin()" />
    </form>
</body>
</html>