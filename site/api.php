<?php require 'inc/lib.php';header('Content-Type: application/json');$units=usable_units();
if(isset($_GET['date'])){$d=$_GET['date'];if(!preg_match('/^\d{4}-\d\d-\d\d$/',$d)){http_response_code(400);exit('{}');}
$bk=day_bookings($d);echo json_encode(['date'=>$d,'status'=>day_status($bk,$units),'bookings'=>array_map(fn($b)=>['time'=>str_replace(':','.',substr($b['start_time'],0,5)).'–'.str_replace(':','.',substr($b['end_time'],0,5)),'pkg'=>$b['pkg']],$bk),'free'=>free_ranges(free_slots($bk,$units))]);exit;}
$m=$_GET['month']??date('Y-m');if(!preg_match('/^\d{4}-\d\d$/',$m)){http_response_code(400);exit('{}');}
$s=db()->prepare("SELECT event_date,start_time,end_time,unit_id FROM bookings WHERE event_date BETWEEN ? AND ? AND status IN('pending','confirmed')");$s->execute([$m.'-01',$m.'-31']);
$g=[];foreach($s as $r)$g[$r['event_date']][]=$r;$o=[];$n=(int)date('t',strtotime($m.'-01'));
for($i=1;$i<=$n;$i++){$d=sprintf('%s-%02d',$m,$i);$o[$d]=day_status($g[$d]??[],$units);}echo json_encode($o);
