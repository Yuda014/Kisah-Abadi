<?php require 'inc/lib.php';$title='Ketersediaan Tanggal';require 'inc/header.php';?>
<section class="sec"><h2>Ketersediaan Tanggal</h2><p class="c mut">Pilih tanggal untuk melihat jam yang masih tersedia.</p>
<div class="calw"><div class="calh"><button class="btn ol sm" id="prev">‹</button><b id="mt"></b><button class="btn ol sm" id="next">›</button></div><div class="cal" id="cal"></div>
<p class="leg"><span><span class="dot available"></span>Tersedia</span><span><span class="dot limited"></span>Terbatas</span><span><span class="dot full"></span>Terbooking</span></p><div id="detail" class="card dt" hidden></div></div></section><?php require 'inc/footer.php';
