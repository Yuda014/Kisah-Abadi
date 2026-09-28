<?php require 'inc/lib.php';$title='Portfolio';$cats=['Prewedding','Siraman','Akad','Resepsi','Wedding'];$c=$_GET['cat']??'';
if(in_array($c,$cats)){$s=db()->prepare("SELECT * FROM portfolio WHERE category=? ORDER BY id DESC");$s->execute([$c]);}else{$s=db()->query("SELECT * FROM portfolio ORDER BY id DESC");}
require 'inc/header.php';?>
<section class="sec"><h2>Our Moments</h2><p class="c"><a class="chip <?=$c?'':'on'?>" href="portfolio.php">Semua</a><?php foreach($cats as $x):?><a class="chip <?=$c==$x?'on':''?>" href="?cat=<?=$x?>"><?=$x?></a><?php endforeach;?></p><?=gallery_html($s->fetchAll())?></section><?php require 'inc/footer.php';
