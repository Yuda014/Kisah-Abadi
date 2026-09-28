<?php require 'inc/auth.php';require 'inc/layout.php';require 'inc/crud.php';
crud('testimonials','Kelola Testimoni',['image'=>['Gambar Testimoni','image'],'customer_name'=>['Nama Pelanggan','text'],'message'=>['Keterangan','textarea'],'show_home'=>['Tampilkan di Home','check']],['image','customer_name','message','show_home']);
