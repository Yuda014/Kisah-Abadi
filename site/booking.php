<?php require 'inc/lib.php';$title='Booking';$pk=db()->query("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$v=['customer_name'=>'','whatsapp'=>'','event_date'=>$_GET['date']??'','package_id'=>(int)($_GET['package']??0),'event_type'=>'Wedding','start_time'=>'08:00','end_time'=>'12:00','location'=>'','notes'=>''];$err='';$done=null;
if($_SERVER['REQUEST_METHOD']==='POST'){foreach($v as $k=>$_)$v[$k]=trim($_POST[$k]??'');
$v=check_booking($v,false,$err);
if(!$err){$id=save_booking($v);if($id===false)$err=MSG_FULL;else{$done=$v;$done['id']=$id;foreach($pk as $p)if($p['id']==$v['package_id'])$done['pkg']=$p['name'];}}}
require 'inc/header.php';?>
<section class="sec"><h2>Booking</h2>
<?php if($done):$tgl=date('d-m-Y',strtotime($done['event_date']));$msg="Halo, saya ingin melakukan booking Wedding Content Creator.\n\nNama: {$done['customer_name']}\nNo. WhatsApp: {$done['whatsapp']}\nPaket: {$done['pkg']}\nTanggal: $tgl\nJenis Acara: {$done['event_type']}\nJam: {$done['start_time']}–{$done['end_time']}\nLokasi: {$done['location']}\nCatatan: {$done['notes']}\n\nMohon konfirmasi ketersediaannya.\n\nTerima kasih.";?>
<div class="card dt c"><h3>Booking diterima (Pending)</h3><p>Terima kasih, <?=e($done['customer_name'])?>. Selesaikan dengan konfirmasi via WhatsApp agar admin dapat memproses booking Anda.</p><a class="btn" target="_blank" rel="noopener" href="https://wa.me/<?=WA_NUMBER?>?text=<?=rawurlencode($msg)?>">KONFIRMASI VIA WHATSAPP</a></div>
<?php else:?>
<form method="post" class="card frm"><?php if($err):?><div class="alert"><?=e($err)?></div><?php endif;?>
<label>Nama<input name="customer_name" required value="<?=e($v['customer_name'])?>"></label>
<label>Nomor WhatsApp<input name="whatsapp" type="tel" required placeholder="08xxxxxxxxxx" value="<?=e($v['whatsapp'])?>"></label>
<label>Tanggal Acara<input name="event_date" type="date" required min="<?=date('Y-m-d',strtotime('+1 day'))?>" value="<?=e($v['event_date'])?>"></label>
<a class="lnk" href="availability.php">Cek Ketersediaan Tanggal →</a>
<label>Paket<select name="package_id" required><option value="">Pilih paket</option><?php foreach($pk as $p):?><option value="<?=$p['id']?>" <?=$v['package_id']==$p['id']?'selected':''?>><?=e($p['name'])?> — <?=rp($p['price'])?> (max <?=$p['max_hours']?> jam)</option><?php endforeach;?></select></label>
<label>Jenis Acara<select name="event_type"><?php foreach(['Wedding','Prewedding','Siraman','Akad','Resepsi','Lainnya'] as $t):?><option <?=$v['event_type']==$t?'selected':''?>><?=$t?></option><?php endforeach;?></select></label>
<div class="row"><label>Jam Mulai<input name="start_time" type="time" required value="<?=e($v['start_time'])?>"></label><label>Jam Selesai<input name="end_time" type="time" required value="<?=e($v['end_time'])?>"></label></div>
<label>Lokasi Acara<input name="location" required value="<?=e($v['location'])?>"></label>
<label>Catatan Tambahan<textarea name="notes" rows="3"><?=e($v['notes'])?></textarea></label>
<button class="btn" type="submit">KIRIM BOOKING</button></form><?php endif;?></section><?php require 'inc/footer.php';
