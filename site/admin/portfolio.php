<?php require 'inc/auth.php';require 'inc/layout.php';require 'inc/crud.php';
crud('portfolio','Kelola Portfolio',['image'=>['Gambar','image'],'title'=>['Judul','text'],'category'=>['Kategori','select',['Prewedding'=>'Prewedding','Siraman'=>'Siraman','Akad'=>'Akad','Resepsi'=>'Resepsi','Wedding'=>'Wedding']]],['image','title','category']);
