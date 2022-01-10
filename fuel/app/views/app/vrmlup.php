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
    <TABLE id="dataTable" width="350px" border="1">
        <TR>
            <TD> WRLファイル： </TD>
            <TD> <input name="vrmlfile" type="file" />  
            </TD>
            
        </TR>
    </TABLE>
    <input type="submit" value="アップロード" name="submit" /> &nbsp; <input type="button" value="キャンセル" onclick="closeWin()" />
    </form>
</body>
</html>