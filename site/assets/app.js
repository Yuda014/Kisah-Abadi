const $=s=>document.querySelector(s);
$('#bg')?.addEventListener('click',()=>$('#nv').classList.toggle('open'));
document.querySelectorAll('[data-car]').forEach(b=>b.onclick=()=>$('#car').scrollBy({left:320*b.dataset.car,behavior:'smooth'}));
const cal=$('#cal');
if(cal){
 const q=new URLSearchParams(location.search),pk=q.get('package')||'',p2=n=>String(n).padStart(2,'0');
 const ymd=d=>d.getFullYear()+'-'+p2(d.getMonth()+1)+'-'+p2(d.getDate());
 const tmr=new Date();tmr.setDate(tmr.getDate()+1);const min=ymd(tmr);
 let cur=new Date();cur.setDate(1);
 const L={available:'<span class="dot available"></span>Tersedia',limited:'<span class="dot limited"></span>Terbatas',full:'<span class="dot full"></span>Terbooking'};
 async function load(){
  $('#mt').textContent=cur.toLocaleDateString('id-ID',{month:'long',year:'numeric'});
  const key=cur.getFullYear()+'-'+p2(cur.getMonth()+1),st=await (await fetch('api.php?month='+key)).json();
  let h=['Min','Sen','Sel','Rab','Kam','Jum','Sab'].map(x=>`<i>${x}</i>`).join('')+'<span></span>'.repeat(cur.getDay());
  for(let d=1;d<=new Date(cur.getFullYear(),cur.getMonth()+1,0).getDate();d++){
   const k=key+'-'+p2(d),s=st[k]||'available';h+=`<button class="d ${s}" data-d="${k}" ${k<min?'disabled':''}>${d}</button>`;}
  cal.innerHTML=h;
 }
 cal.onclick=async e=>{
  const k=e.target.dataset.d;if(!k)return;
  const r=await (await fetch('api.php?date='+k)).json(),box=$('#detail');
  let h=`<h3>${new Date(k).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric',timeZone:'UTC'})}</h3><p><b>${L[r.status]}</b></p>`;
  if(r.bookings.length)h+='<p class="mut">Booking:</p><ul>'+r.bookings.map(b=>`<li>${b.time} → ${b.pkg} → Terbooking</li>`).join('')+'</ul>';
  h+='<p class="mut">Jam tersedia:</p><ul>'+(r.free.length?r.free.map(f=>`<li>${f} → Tersedia</li>`).join(''):'<li>Tidak ada</li>')+'</ul>';
  if(r.status!=='full')h+=`<a class="btn" href="booking.php?date=${k}${pk?'&package='+pk:''}">BOOKING TANGGAL INI</a>`;
  box.innerHTML=h;box.hidden=false;box.scrollIntoView({behavior:'smooth',block:'nearest'});
 };
 $('#prev').onclick=()=>{cur.setMonth(cur.getMonth()-1);load()};$('#next').onclick=()=>{cur.setMonth(cur.getMonth()+1);load()};load();
}
