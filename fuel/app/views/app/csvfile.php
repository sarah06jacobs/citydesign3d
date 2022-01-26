<?php
header('Content-Type: application/octet-stream; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
echo "\xEF\xBB\xBF";
?><?php if(count($list)) { ?><?php foreach($list[0] as $idx => $value) { ?><?= $idx; ?>,<?php } ?>	
<?php foreach($list as $row => $col) { ?><?php foreach($col as $idx => $value) if( substr($value,0,4) != 'http') { ?><?= $value; ?>,<?php } else {?>"<? echo $value; ?>",<?php } ?>

<?php } ?><?php } ?>