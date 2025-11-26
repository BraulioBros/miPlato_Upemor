<?php
function asset($p){return 'public/'.ltrim($p,'/');}
function redirect($c,$a,$params=[]){$q=http_build_query(array_merge(['controller'=>$c,'action'=>$a],$params));header('Location: index.php?'.$q);exit;}
