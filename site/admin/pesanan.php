<?php require 'inc/auth.php';require 'inc/layout.php';$pdo=db();$err='';$a=$_GET['a']??'list';$id=(int)($_GET['id']??0);$ST=['pending','confirmed','completed','cancelled'];
$pk=$pdo->query("SELECT id,name FROM packages ORDER BY sort_order")->fetchAll(PDO::FETCH_KEY_PAIR);$F=['customer_name','whatsapp','event_date','package_id','event_type','start_time','end_time','location','notes','status'];$row=null;
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 if(isset($_POST['del'])){$pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([(int)$_POST['del']]);header('Location: ?ok=1');exit;}
 elseif(isset($_POST['setst'])){$s=$pdo->prepare("SELECT * FROM bookings WHERE id=?");$s->execute([(int)$_POST['setst']]);$r=$s->fetch();
  if($r&&in_array($_POST['status'],$ST)){$r['status']=$_POST['status'];if(save_booking($r,$r['id'])===false)$err=MSG_FULL;else{header('Location: ?ok=1');exit;}}}
 else{$id=(int)($_POST['id']??0);$v=[];foreach($F as $k)$v[$k]=trim($_POST[$k]??'');if(!in_array($v['status'],$ST))$v['status']='pending';
  $v=check_booking($v,true,$err);if(!$err){$r=save_booking($v,$id);if($r===false)$err=MSG_FULL;else{header('Location: ?ok=1');exit;}}$row=$v;$a=$id?'edit':'add';}}
adm_head('Kelola Pesanan');if(isset($_GET['ok']))echo '<div class="ok">Berhasil disimpan.</div>';if($err)echo '<div class="alert">'.e($err).'</div>';
if($a==='add'||$a==='edit'){if($a==='edit'&&!$row){$s=$pdo->prepare("SELECT * FROM bookings WHERE id=?");$s->execute([$id]);$row=$s->fetch()?:[];}
 $g=fn($k,$d='')=>e($row[$k]??$d);?>
<form method="post" class="card frm" style="margin:0"><?=csrf_field()?><input type="hidden" name="id" value="<?=$a==='edit'?$id:0?>">
<label>Nama<input name="customer_name" required value="<?=$g('customer_name')?>"></label><label>WhatsApp<input name="whatsapp" required value="<?=$g('whatsapp')?>"></label>
<label>Tanggal<input type="date" name="event_date" required value="<?=$g('event_date')?>"></label>
<label>Paket<select name="package_id"><?php foreach($pk as $i=>$n):?><option value="<?=$i?>" <?=($row['package_id']??0)==$i?'selected':''?>><?=e($n)?></option><?php endforeach;?></select></label>
<label>Jenis Acara<input name="event_type" value="<?=$g('event_type','Wedding')?>"></label>
<div class="row"><label>Jam Mulai<input type="time" name="start_time" required value="<?=substr($g('start_time','08:00'),0,5)?>"></label><label>Jam Selesai<input type="time" name="end_time" required value="<?=substr($g('end_time','12:00'),0,5)?>"></label></div>
<label>Lokasi<input name="location" value="<?=$g('location')?>"></label><label>Catatan<textarea name="notes" rows="3"><?=$g('notes')?></textarea></label>
<label>Status<select name="status"><?php foreach($ST as $s):?><option <?=($row['status']??'pending')==$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></label>
<div><button class="btn">SIMPAN</button> <a class="btn ol" href="?">BATAL</a></div></form>
<?php }elseif($a==='view'){$s=$pdo->prepare("SELECT b.*,p.name pkg,u.name unit FROM bookings b JOIN packages p ON p.id=b.package_id LEFT JOIN units u ON u.id=b.unit_id WHERE b.id=?");$s->execute([$id]);$r=$s->fetch();if(!$r)echo 'Tidak ditemukan.';else{?>
<div class="card"><p><b><?=e($r['customer_name'])?></b> · <a href="https://wa.me/<?=e($r['whatsapp'])?>" target="_blank" rel="noopener"><?=e($r['whatsapp'])?></a></p><p>Paket: <?=e($r['pkg'])?><br>Tanggal: <?=e($r['event_date'])?> · <?=substr($r['start_time'],0,5)?>–<?=substr($r['end_time'],0,5)?><br>Jenis acara: <?=e($r['event_type'])?><br>Lokasi: <?=e($r['location'])?><br>Unit: <?=e($r['unit']?:'-')?><br>Catatan: <?=nl2br(e($r['notes']))?><br>Status: <span class="bd <?=$r['status']?>"><?=$r['status']?></span></p><a class="btn sm" href="?a=edit&id=<?=$id?>">EDIT</a> <a class="btn ol sm" href="?">KEMBALI</a></div><?php }}
else{$st=$_GET['st']??'';$q="SELECT b.*,p.name pkg FROM bookings b JOIN packages p ON p.id=b.package_id ".(in_array($st,$ST)?"WHERE b.status='$st' ":'')."ORDER BY b.event_date DESC,b.start_time";?>
<p><a class="btn sm" href="?a=add">+ TAMBAH PESANAN</a> <?php foreach(array_merge([''],$ST) as $s):?><a class="chip <?=$st==$s?'on':''?>" href="?st=<?=$s?>"><?=$s?:'semua'?></a><?php endforeach;?></p>
<div class="tw"><table class="tbl"><tr><th>ID</th><th>Nama</th><th>Paket</th><th>Tanggal</th><th>Jam</th><th>Lokasi</th><th>Status</th><th>Action</th></tr>
<?php foreach($pdo->query($q) as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['customer_name'])?></td><td><?=e($r['pkg'])?></td><td><?=e($r['event_date'])?></td><td><?=substr($r['start_time'],0,5)?>–<?=substr($r['end_time'],0,5)?></td><td><?=e($r['location'])?></td>
<td><form method="post"><?=csrf_field()?><input type="hidden" name="setst" value="<?=$r['id']?>"><select name="status" onchange="this.form.submit()"><?php foreach($ST as $s):?><option <?=$r['status']==$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></form></td>
<td><a href="?a=view&id=<?=$r['id']?>">Detail</a> | <a href="?a=edit&id=<?=$r['id']?>">Edit</a> | <form method="post" onsubmit="return confirm('Hapus pesanan ini?')"><?=csrf_field()?><input type="hidden" name="del" value="<?=$r['id']?>"><button class="lb">Delete</button></form></td></tr><?php endforeach;?></table></div>
<?php }adm_foot();
