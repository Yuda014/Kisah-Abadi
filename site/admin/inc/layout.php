<?php
function adm_head($t){$cur=basename($_SERVER['SCRIPT_NAME']);$m=['dashboard.php'=>'Dashboard','pesanan.php'=>'Kelola Pesanan','availability.php'=>'Kalender Booking','paket.php'=>'Kelola Paket','unit.php'=>'Kelola Unit','testimoni.php'=>'Kelola Testimoni','portfolio.php'=>'Kelola Portfolio','password.php'=>'Ganti Password','logout.php'=>'Logout'];
echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($t).' · Admin</title><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Inter:wght@400;500&display=swap" rel="stylesheet"><link rel="stylesheet" href="../assets/style.css"></head><body><div class="adm"><aside class="side"><img src="../assets/logo-white.png" alt="'.BRAND.'" style="width:150px;margin:0 12px 18px">';
foreach($m as $f=>$n)echo '<a href="'.$f.'" class="'.($cur==$f?'on':'').'">'.$n.'</a>';
echo '</aside><main class="main"><div class="top"><h1>'.e($t).'</h1></div>';}
function adm_foot(){echo '</main></div></body></html>';}
