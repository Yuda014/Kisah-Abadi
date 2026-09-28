<?php require 'inc/auth.php';require 'inc/layout.php';$pdo=db();
$c=$pdo->query("SELECT status,COUNT(*) n FROM bookings GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);$tot=array_sum($c);
$up=$pdo->query("SELECT b.*,p.name pkg FROM bookings b JOIN packages p ON p.id=b.package_id WHERE b.event_date>=CURDATE() AND b.status IN('pending','confirmed') ORDER BY b.event_date,b.start_time LIMIT 5")->fetchAll();
adm_head('Dashboard');?>
<div class="stats"><?php foreach(['Total Booking'=>$tot,'Pending'=>$c['pending']??0,'Confirmed'=>$c['confirmed']??0,'Completed'=>$c['completed']??0,'Cancelled'=>$c['cancelled']??0] as $k=>$n):?><div class="card"><b><?=$n?></b><?=$k?></div><?php endforeach;?></div>
<h3>Jadwal Terdekat</h3><div class="tw"><table class="tbl"><tr><th>Tanggal</th><th>Jam</th><th>Nama</th><th>Paket</th><th>Lokasi</th><th>Status</th></tr>
<?php foreach($up as $r):?><tr><td><?=e($r['event_date'])?></td><td><?=substr($r['start_time'],0,5)?>–<?=substr($r['end_time'],0,5)?></td><td><?=e($r['customer_name'])?></td><td><?=e($r['pkg'])?></td><td><?=e($r['location'])?></td><td><span class="bd <?=$r['status']?>"><?=$r['status']?></span></td></tr><?php endforeach;if(!$up)echo '<tr><td colspan="6">Belum ada jadwal.</td></tr>';?></table></div><?php adm_foot();
