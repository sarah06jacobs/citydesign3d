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
    <input type="submit" value="アップロード" name="submit" /> &nbsp; <input type="button" value="キャンセル" onclick="closeWin()" />
    </form>
</body>
</html>