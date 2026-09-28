<?php require 'inc/lib.php';$title='Testimoni';$r=db()->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();require 'inc/header.php';?>
<section class="sec"><h2>What They Say</h2><div class="gal tg"><?=testi_html($r)?></div></section><?php require 'inc/footer.php';
