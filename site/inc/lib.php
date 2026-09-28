<?php
require_once __DIR__.'/../config/database.php';
date_default_timezone_set('Asia/Jakarta');
const MSG_FULL='Maaf, jadwal tersebut sudah terbooking. Silakan pilih waktu atau tanggal lainnya.';
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function rp($n){return 'Rp'.number_format((int)$n,0,',','.');}
function tm($t){$p=explode(':',$t);return $p[0]*60+$p[1];}
function fmt($m){return sprintf('%02d.%02d',intdiv($m,60),$m%60);}
function usable_units(){return db()->query("SELECT id FROM units WHERE status IN('available','booked') ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);}
function day_bookings($d){$s=db()->prepare("SELECT b.*,p.name pkg FROM bookings b JOIN packages p ON p.id=b.package_id WHERE b.event_date=? AND b.status IN('pending','confirmed') ORDER BY b.start_time");$s->execute([$d]);return $s->fetchAll();}
function free_slots($bk,$units){$f=array_fill(0,32,false);foreach($units as $u)for($i=0;$i<32;$i++){$a=360+$i*30;$b=$a+30;$ok=true;foreach($bk as $x)if($x['unit_id']==$u&&tm($x['start_time'])<$b&&tm($x['end_time'])>$a){$ok=false;break;}if($ok)$f[$i]=true;}return $f;}
function free_ranges($f){$r=[];$s=null;for($i=0;$i<=32;$i++){$ok=$i<32&&$f[$i];if($ok&&$s===null)$s=$i;if(!$ok&&$s!==null){$r[]=fmt(360+$s*30).'–'.fmt(360+$i*30);$s=null;}}return $r;}
function day_status($bk,$units){$f=free_slots($bk,$units);$run=0;$max=0;foreach($f as $x){$run=$x?$run+1:0;$max=max($max,$run);}if(!$units||$max<4)return 'full';return $bk?'limited':'available';}
// Panggil di dalam transaksi: mengunci tabel units agar tidak terjadi double booking
function find_unit($date,$st,$en,$ex=0){$pdo=db();$units=$pdo->query("SELECT id FROM units WHERE status IN('available','booked') ORDER BY id FOR UPDATE")->fetchAll(PDO::FETCH_COLUMN);$s=$pdo->prepare("SELECT DISTINCT unit_id FROM bookings WHERE event_date=? AND status IN('pending','confirmed') AND start_time<? AND end_time>? AND id<>?");$s->execute([$date,$en,$st,$ex]);$free=array_values(array_diff($units,$s->fetchAll(PDO::FETCH_COLUMN)));return $free?$free[0]:null;}
function check_booking($v,$admin,&$err){
$v['whatsapp']=preg_replace('/\D/','',$v['whatsapp']);if(strpos($v['whatsapp'],'0')===0)$v['whatsapp']='62'.substr($v['whatsapp'],1);
$d=DateTime::createFromFormat('Y-m-d',$v['event_date']);$T='/^\d\d:\d\d(:\d\d)?$/';
if(strlen(trim($v['customer_name']))<2)$err='Nama wajib diisi.';
elseif(strlen($v['whatsapp'])<10||strlen($v['whatsapp'])>15)$err='Nomor WhatsApp tidak valid.';
elseif(!$d||$d->format('Y-m-d')!==$v['event_date'])$err='Tanggal tidak valid.';
elseif(!$admin&&$v['event_date']<date('Y-m-d',strtotime('+1 day')))$err='Booking maksimal H-1 (mulai besok).';
elseif(!preg_match($T,$v['start_time'])||!preg_match($T,$v['end_time']))$err='Jam tidak valid.';
elseif(tm($v['start_time'])<360||tm($v['end_time'])>1320||tm($v['end_time'])<=tm($v['start_time']))$err='Jam harus antara 06.00–22.00 dan jam selesai setelah jam mulai.';
else{$s=db()->prepare("SELECT * FROM packages WHERE id=?");$s->execute([(int)$v['package_id']]);$p=$s->fetch();
if(!$p)$err='Pilih paket.';elseif(tm($v['end_time'])-tm($v['start_time'])>$p['max_hours']*60)$err='Durasi melebihi maksimal paket '.$p['name'].' ('.$p['max_hours'].' jam).';}
$v['start_time']=substr($v['start_time'],0,5);$v['end_time']=substr($v['end_time'],0,5);return $v;}
function save_booking($v,$id=0){$pdo=db();$pdo->beginTransaction();try{
$st=$v['status']??'pending';$u=$v['unit_id']??null;
if(in_array($st,['pending','confirmed'])){$u=find_unit($v['event_date'],$v['start_time'],$v['end_time'],$id);if(!$u){$pdo->rollBack();return false;}}
$a=[$v['customer_name'],$v['whatsapp'],$v['event_date'],(int)$v['package_id'],$u,$v['event_type'],$v['start_time'],$v['end_time'],$v['location'],$v['notes'],$st];
if($id){$pdo->prepare("UPDATE bookings SET customer_name=?,whatsapp=?,event_date=?,package_id=?,unit_id=?,event_type=?,start_time=?,end_time=?,location=?,notes=?,status=? WHERE id=?")->execute([...$a,$id]);}
else{$pdo->prepare("INSERT INTO bookings(customer_name,whatsapp,event_date,package_id,unit_id,event_type,start_time,end_time,location,notes,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)")->execute($a);$id=$pdo->lastInsertId();}
$pdo->commit();return $id;}catch(Throwable $x){$pdo->rollBack();throw $x;}}
function packages_html(){$o='<div class="grid3">';foreach(db()->query("SELECT * FROM packages WHERE is_active=1 ORDER BY sort_order,id") as $p){
$o.='<article class="card pk"><h3>'.e($p['name']).'</h3><div class="pr">'.rp($p['price']).'</div><div class="mut">Maksimal '.(int)$p['max_hours'].' Hours</div><p class="tag">“'.e($p['tagline']).'”</p><ul>';
foreach(preg_split('/\r?\n/',trim($p['features'])) as $f)if($f!=='')$o.='<li>'.e($f).'</li>';
$o.='</ul><a class="btn" href="booking.php?package='.$p['id'].'">PILIH PAKET</a></article>';}return $o.'</div>';}
function ic($d){return '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round">'.$d.'</svg>';}
function included_html(){$i=[[ic('<rect x="7" y="2.5" width="10" height="19" rx="2.5"/><path d="M10.5 5h3"/><circle cx="12" cy="18.5" r=".6"/>'),'iPhone 13','Dokumentasi menggunakan iPhone 13.'],[ic('<rect x="9.5" y="2.5" width="5" height="10" rx="2.5"/><path d="M6 11a6 6 0 0 0 12 0M12 17v4M9 21h6"/>'),'Wireless Mic','Wireless microphone untuk kebutuhan cinematic content.'],[ic('<rect x="3" y="9" width="18" height="12" rx="1.5"/><path d="M3 9l17-4.5M8 7.6l2.4 2M13 6.3l2.4 2M18 5l2.3 2"/>'),'Cinematic Content','Konten wedding bergaya cinematic dan estetik.'],[ic('<path d="M4 16v-4l2-5h12l2 5v4M4 16h16M4 16v3h3v-3M17 16v3h3v-3"/><circle cx="8" cy="13" r=".6"/><circle cx="16" cy="13" r=".6"/>'),'Free Transport','Kota Cimahi & Tengah Kota Bandung.'],[ic('<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>'),'Booking','Maksimal booking H-1.']];
$o='<div class="grid5">';foreach($i as $x)$o.='<div class="card inc"><div class="ic">'.$x[0].'</div><h4>'.$x[1].'</h4><p>'.$x[2].'</p></div>';return $o.'</div>';}
function gallery_html($rows){if(!$rows)return '<p class="mut c">Belum ada portfolio.</p>';$o='<div class="gal">';foreach($rows as $r)$o.='<figure><img loading="lazy" src="uploads/'.e($r['image']).'" alt="'.e($r['title']).'"><figcaption>'.e($r['title']).'<small>'.e($r['category']).'</small></figcaption></figure>';return $o.'</div>';}
function testi_html($rows){if(!$rows)return '<p class="mut c">Belum ada testimoni.</p>';$o='';foreach($rows as $r)$o.='<figure class="tc"><img loading="lazy" src="uploads/'.e($r['image']).'" alt="Testimoni '.e($r['customer_name']).'"><figcaption><b>'.e($r['customer_name']).'</b>'.e($r['message']).'</figcaption></figure>';return $o;}
