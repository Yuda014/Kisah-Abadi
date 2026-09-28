<?php require 'inc/lib.php';$title='Home';$pdo=db();
$pf=$pdo->query("SELECT * FROM portfolio ORDER BY id DESC LIMIT 6")->fetchAll();
$ts=$pdo->query("SELECT * FROM testimonials WHERE show_home=1 ORDER BY id DESC LIMIT 12")->fetchAll();
require 'inc/header.php';?>
<section class="hero" <?php if($pf):?>style="--bg:url('uploads/<?=e($pf[0]['image'])?>')"<?php endif;?>><div><p class="eyebrow">Wedding Content Creator</p><h1>Kisah Abadi,<br>tersimpan indah.</h1><p class="lead">Kami merekam momen sakral pernikahan Anda menjadi konten cinematic yang siap dibagikan — real-time, estetik, dan personal.</p><div class="cta"><a class="btn" href="booking.php">BOOK NOW</a><a class="btn ol" href="availability.php">KETERSEDIAAN TANGGAL</a></div></div></section>
<section class="sec" id="paket"><h2>Paket</h2><?=packages_html()?></section>
<section class="sec alt"><h2>What’s Included</h2><?=included_html()?></section>
<section class="sec"><h2>Our Moments</h2><?=gallery_html($pf)?><p class="c"><a class="btn ol" href="portfolio.php">LIHAT SEMUA</a></p></section>
<section class="sec alt"><h2>What They Say</h2><div class="car" id="car"><?=testi_html($ts)?></div><p class="c"><button class="btn ol sm" data-car="-1">‹</button> <button class="btn ol sm" data-car="1">›</button> <a class="btn ol sm" href="testimoni.php">LIHAT TESTIMONI</a></p></section>
<section class="sec c"><h2>Siap mengabadikan hari bahagia Anda?</h2><div class="cta j"><a class="btn" href="availability.php">KETERSEDIAAN TANGGAL</a><a class="btn ol" href="booking.php">BOOK NOW</a></div></section>
<?php require 'inc/footer.php';
