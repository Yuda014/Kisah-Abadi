<?php
function upload($k,&$err){if(empty($_FILES[$k]['name']))return null;$f=$_FILES[$k];if($f['error']!==0){$err='Upload gagal.';return null;}if($f['size']>3*1024*1024){$err='Ukuran gambar maksimal 3MB.';return null;}
$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'])]??null;if(!$ext){$err='Hanya JPG, PNG, atau WEBP.';return null;}
$n=bin2hex(random_bytes(8)).'.'.$ext;move_uploaded_file($f['tmp_name'],__DIR__.'/../../uploads/'.$n);return $n;}
// $F: kolom => [label, tipe(text|number|textarea|select|check|image), opsi]
function crud($t,$title,$F,$cols,$orderBy='id DESC'){$pdo=db();$err='';$a=$_GET['a']??'list';$id=(int)($_GET['id']??0);$row=null;
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 if(isset($_POST['del'])){try{$pdo->prepare("DELETE FROM $t WHERE id=?")->execute([(int)$_POST['del']]);header('Location: ?ok=1');exit;}catch(PDOException $x){$err='Data tidak bisa dihapus karena masih dipakai booking.';}}
 else{$id=(int)($_POST['id']??0);$v=[];foreach($F as $k=>$f){
  if($f[1]==='image'){$n=upload($k,$err);if($n)$v[$k]=$n;elseif(!$id&&!$err)$err='Gambar wajib diupload.';}
  elseif($f[1]==='check')$v[$k]=isset($_POST[$k])?1:0;
  else{$v[$k]=trim($_POST[$k]??'');if($v[$k]===''&&$f[1]!=='textarea')$err=$f[0].' wajib diisi.';if($f[1]==='select'&&!isset($f[2][$v[$k]]))$err=$f[0].' tidak valid.';}}
  if(!$err){if($id){$pdo->prepare("UPDATE $t SET ".implode(',',array_map(fn($k)=>"$k=?",array_keys($v)))." WHERE id=?")->execute([...array_values($v),$id]);}
  else{$pdo->prepare("INSERT INTO $t(".implode(',',array_keys($v)).") VALUES(".implode(',',array_fill(0,count($v),'?')).")")->execute(array_values($v));}
  header('Location: ?ok=1');exit;}
  $a=$id?'edit':'add';$row=$_POST;}}
adm_head($title);if(isset($_GET['ok']))echo '<div class="ok">Berhasil disimpan.</div>';if($err)echo '<div class="alert">'.e($err).'</div>';
if($a==='add'||$a==='edit'){if($a==='edit'&&!$row){$s=$pdo->prepare("SELECT * FROM $t WHERE id=?");$s->execute([$id]);$row=$s->fetch()?:[];}
 echo '<form method="post" enctype="multipart/form-data" class="card frm" style="margin:16px 0 0">'.csrf_field().'<input type="hidden" name="id" value="'.($a==='edit'?$id:0).'">';
 foreach($F as $k=>$f){$val=$row[$k]??'';echo '<label>'.e($f[0]);
  if($f[1]==='textarea')echo '<textarea name="'.$k.'" rows="4">'.e($val).'</textarea>';
  elseif($f[1]==='select'){echo '<select name="'.$k.'">';foreach($f[2] as $ov=>$ol)echo '<option value="'.e($ov).'" '.($val==$ov?'selected':'').'>'.e($ol).'</option>';echo '</select>';}
  elseif($f[1]==='check')echo '<input type="checkbox" name="'.$k.'" value="1" style="width:auto" '.(($a==='add'&&!$row)||$val?'checked':'').'>';
  elseif($f[1]==='image')echo '<input type="file" name="'.$k.'" accept="image/jpeg,image/png,image/webp">'.($a==='edit'&&!empty($row[$k])?'<img src="../uploads/'.e($row[$k]).'" style="width:90px">':'');
  else echo '<input type="'.$f[1].'" name="'.$k.'" value="'.e($val).'">';
  echo '</label>';}
 echo '<div><button class="btn">SIMPAN</button> <a class="btn ol" href="?">BATAL</a></div></form>';}
else{echo '<p><a class="btn sm" href="?a=add">+ TAMBAH</a></p><div class="tw"><table class="tbl"><tr><th>ID</th>';foreach($cols as $k)echo '<th>'.e($F[$k][0]).'</th>';echo '<th>Action</th></tr>';
 foreach($pdo->query("SELECT * FROM $t ORDER BY $orderBy") as $r){echo '<tr><td>'.$r['id'].'</td>';
  foreach($cols as $k){$ty=$F[$k][1];echo '<td>'.($ty==='image'?'<img src="../uploads/'.e($r[$k]).'">':($ty==='check'?($r[$k]?'✓':'–'):e(mb_strimwidth((string)$r[$k],0,60,'…')))).'</td>';}
  echo '<td><a href="?a=edit&id='.$r['id'].'">Edit</a> | <form method="post" onsubmit="return confirm(\'Hapus data ini?\')">'.csrf_field().'<input type="hidden" name="del" value="'.$r['id'].'"><button class="lb">Delete</button></form></td></tr>';}
 echo '</table></div>';}
adm_foot();}
