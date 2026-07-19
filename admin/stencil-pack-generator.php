<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
bos_require_admin();
$wallet = bos_page_start('Admin', 'Stencil Pack Generator Pro', 'Create premium foil stencil packs with a smoky Beyond Tattoo wrapper, high-resolution artwork controls and social-ready exports.');
?>
<main class="bos-main">
<section class="bos-hero">
  <span class="bos-kicker">Beyond Tattoo Admin</span>
  <h1>Stencil Pack Generator Pro</h1>
  <p>Upload finished artwork and build a premium foil-wrapped Stencil of the Day pack with purple smoke, crinkled edges, metallic highlights and BIT$ Atom branding.</p>
  <div class="bos-actions"><a class="bos-btn secondary" href="index.php">Back to Admin</a><a class="bos-btn secondary" href="stencil-library.php">Stencil Library</a></div>
</section>
<section class="bos-section">
<div class="bos-grid" style="grid-template-columns:minmax(310px,.76fr) minmax(360px,1.24fr);align-items:start">
<form class="bos-card" id="packForm" style="display:grid;gap:13px">
  <label><strong>Upload stencil artwork</strong><small> PNG, JPG or WebP · transparent PNG recommended</small><input class="bos-input" id="stencilFile" type="file" accept="image/png,image/jpeg,image/webp" required></label>
  <label><strong>Stencil title</strong><input class="bos-input" id="title" value="Baby Angel Reaper" maxlength="48"></label>
  <label><strong>Collection name</strong><input class="bos-input" id="collection" value="King Boo Collection" maxlength="38"></label>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <label><strong>Edition</strong><input class="bos-input" id="edition" value="Realism Edition" maxlength="26"></label>
    <label><strong>Detail level</strong><input class="bos-input" id="detail" value="High Detail Stencil" maxlength="26"></label>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <label><strong>Access</strong><select class="bos-input" id="access"><option>FREE</option><option>PREMIUM</option></select></label>
    <label><strong>Badge</strong><select class="bos-input" id="badge"><option>Stencil of the Day</option><option>New Drop</option><option>Limited Drop</option><option>Premium Drop</option></select></label>
  </div>
  <label><strong>Pack subtitle</strong><input class="bos-input" id="subtitle" value="Premium Tattoo Stencil" maxlength="36"></label>
  <label><strong>Footer tagline</strong><input class="bos-input" id="tagline" value="Collect · Ink · Evolve" maxlength="38"></label>
  <label><strong>Drop date</strong><input class="bos-input" id="dropDate" type="date" value="<?=date('Y-m-d')?>"></label>

  <details open style="border-top:1px solid rgba(127,58,201,.25);padding-top:12px">
    <summary style="cursor:pointer;font-weight:800">Artwork controls</summary>
    <div style="display:grid;gap:11px;margin-top:12px">
      <label><strong>Fit mode</strong><select class="bos-input" id="fitMode"><option value="contain">Contain — show full artwork</option><option value="cover">Cover — cinematic crop</option></select></label>
      <label><strong>Artwork zoom</strong><input id="artZoom" type="range" min="70" max="180" value="100"><small id="artZoomValue">100%</small></label>
      <label><strong>Horizontal position</strong><input id="artX" type="range" min="-100" max="100" value="0"></label>
      <label><strong>Vertical position</strong><input id="artY" type="range" min="-100" max="100" value="0"></label>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <label><strong>Contrast</strong><input id="contrast" type="range" min="80" max="165" value="112"></label>
        <label><strong>Brightness</strong><input id="brightness" type="range" min="75" max="135" value="100"></label>
      </div>
      <label style="display:flex;align-items:center;gap:8px"><input id="grayscale" type="checkbox" checked> Black-and-grey realism treatment</label>
    </div>
  </details>

  <details open style="border-top:1px solid rgba(127,58,201,.25);padding-top:12px">
    <summary style="cursor:pointer;font-weight:800">Pack effects</summary>
    <div style="display:grid;gap:11px;margin-top:12px">
      <label><strong>Purple smoke</strong><input id="smoke" type="range" min="0" max="100" value="78"></label>
      <label><strong>Foil glow</strong><input id="foil" type="range" min="20" max="130" value="94"></label>
      <label><strong>Wrapper crinkles</strong><input id="crinkle" type="range" min="0" max="100" value="72"></label>
      <label style="display:flex;align-items:center;gap:8px"><input id="animatePreview" type="checkbox" checked> Animated shimmer preview</label>
    </div>
  </details>

  <div class="bos-actions" style="margin-top:4px">
    <button class="bos-btn" type="button" id="downloadPng">Download 4K package PNG</button>
    <button class="bos-btn secondary" type="button" id="downloadWebp">Download WebP</button>
    <button class="bos-btn secondary" type="button" id="downloadJson">Download pack info</button>
  </div>
  <p style="margin:0;opacity:.68;font-size:.9rem">Exports at 2160 × 2700. Your original upload is never modified.</p>
</form>
<article class="bos-card" style="position:sticky;top:18px">
  <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap">
    <span class="bos-kicker">High-quality live preview</span>
    <button class="bos-btn secondary" type="button" id="resetArtwork" style="padding:.55rem .8rem">Reset artwork</button>
  </div>
  <canvas id="packCanvas" width="2160" height="2700" style="width:100%;height:auto;border-radius:22px;background:#050108;display:block;box-shadow:0 22px 70px rgba(67,17,107,.35)"></canvas>
  <p style="margin-bottom:0;opacity:.75">Premium 4:5 pack render with procedural smoke, zipper seams, foil reflections, film grain and crinkled wrapper edges.</p>
</article>
</div>
</section>
</main>
<script>
(() => {
  const canvas = document.getElementById('packCanvas');
  const ctx = canvas.getContext('2d', {alpha:false});
  const fields = ['title','collection','edition','detail','access','badge','subtitle','tagline','dropDate','fitMode','artZoom','artX','artY','contrast','brightness','grayscale','smoke','foil','crinkle','animatePreview'];
  const data = Object.fromEntries(fields.map(id => [id, document.getElementById(id)]));
  let stencil = null;
  let frame = 0;
  let raf = 0;
  const W=2160,H=2700,S=2;

  const seeded = (n) => {
    const x = Math.sin(n * 999.91 + 17.17) * 43758.5453;
    return x - Math.floor(x);
  };
  const rounded = (x,y,w,h,r) => {ctx.beginPath();ctx.roundRect(x,y,w,h,r);ctx.closePath();};
  const fitText = (text,maxWidth,start,weight='900',family='Arial Black, Arial') => {
    let size=start;
    do {ctx.font=`${weight} ${size}px ${family}`;size-=2;} while(ctx.measureText(text).width>maxWidth&&size>38);
    return size+2;
  };
  const glowText=(text,x,y,size,fill='#f8efff',blur=36,align='center')=>{
    ctx.save();ctx.textAlign=align;ctx.textBaseline='middle';ctx.font=`900 ${size}px Arial Black, Arial`;ctx.fillStyle=fill;ctx.shadowColor='#a54cff';ctx.shadowBlur=blur;ctx.fillText(text,x,y);ctx.restore();
  };
  const wrapText=(text,x,y,maxWidth,lineHeight)=>{
    const words=String(text).split(' ');let line='',yy=y;
    for(let n=0;n<words.length;n++){const test=line+words[n]+' ';if(ctx.measureText(test).width>maxWidth&&n>0){ctx.fillText(line.trim(),x,yy);line=words[n]+' ';yy+=lineHeight;}else line=test;}
    ctx.fillText(line.trim(),x,yy);
  };

  function drawSmoke(intensity, phase=0){
    const alpha=intensity/100;
    if(alpha<=0)return;
    ctx.save();ctx.globalCompositeOperation='screen';
    for(let i=0;i<42;i++){
      const side=i%2===0?-1:1;
      const x=side<0?seeded(i)*430:W-seeded(i)*430;
      const y=280+seeded(i+70)*(H-440);
      const drift=Math.sin(phase*.025+i)*18;
      const r=90+seeded(i+140)*270;
      const g=ctx.createRadialGradient(x+drift,y,0,x+drift,y,r);
      g.addColorStop(0,`rgba(183,80,255,${.12*alpha})`);
      g.addColorStop(.36,`rgba(111,28,190,${.08*alpha})`);
      g.addColorStop(1,'rgba(42,4,65,0)');
      ctx.fillStyle=g;ctx.fillRect(x-r+drift,y-r,r*2,r*2);
    }
    ctx.restore();
  }

  function drawStars(){
    ctx.save();
    for(let i=0;i<95;i++){
      const x=seeded(i)*W,y=seeded(i+200)*H,r=.8+seeded(i+400)*3.6;
      ctx.fillStyle=`rgba(195,121,255,${.05+seeded(i+600)*.16})`;
      ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fill();
    }
    ctx.restore();
  }

  function serratedEdge(y, flip=false){
    ctx.save();ctx.beginPath();ctx.moveTo(190,y);
    const amp=flip?-13:13;
    for(let x=190;x<=1970;x+=16){ctx.lineTo(x,y+(Math.floor((x-190)/16)%2?amp:0));}
    ctx.strokeStyle='#b562ff';ctx.lineWidth=4;ctx.shadowColor='#9c40ff';ctx.shadowBlur=18;ctx.stroke();ctx.restore();
  }

  function drawWrapper(phase){
    const foil=+data.foil.value/100;
    const crinkle=+data.crinkle.value/100;
    ctx.save();
    rounded(175,300,1810,2130,84);
    const shell=ctx.createLinearGradient(175,0,1985,0);
    shell.addColorStop(0,'#16081f');shell.addColorStop(.07,'#531582');shell.addColorStop(.13,'#0a050e');shell.addColorStop(.5,'#120518');shell.addColorStop(.87,'#09040d');shell.addColorStop(.94,'#5a168a');shell.addColorStop(1,'#14071e');
    ctx.fillStyle=shell;ctx.shadowColor='#8d2fff';ctx.shadowBlur=60*foil;ctx.fill();
    ctx.lineWidth=17;ctx.strokeStyle='#7e2cd7';ctx.stroke();ctx.restore();

    // Foil rim reflection
    ctx.save();rounded(202,328,1756,2076,60);ctx.lineWidth=4;
    const rim=ctx.createLinearGradient(202,328,1958,2404);
    rim.addColorStop(0,'#f2d7ff');rim.addColorStop(.18,'#7a2dca');rim.addColorStop(.5,'#22102d');rim.addColorStop(.76,'#c15cff');rim.addColorStop(1,'#f1d7ff');
    ctx.strokeStyle=rim;ctx.shadowColor='#b84fff';ctx.shadowBlur=20*foil;ctx.stroke();ctx.restore();

    serratedEdge(310,false);serratedEdge(2420,true);

    // Crinkle / foil folds
    ctx.save();ctx.globalCompositeOperation='screen';
    for(let i=0;i<58;i++){
      const side=i%2===0?1:-1;
      const baseX=side<0?205:1955;
      const y=360+seeded(i+30)*1970;
      const len=45+seeded(i+90)*145;
      const shimmer=(Math.sin(phase*.035+i*.8)+1)/2;
      const grad=ctx.createLinearGradient(baseX,y,baseX-side*len,y+30);
      grad.addColorStop(0,`rgba(255,236,255,${(.17+.22*shimmer)*crinkle})`);
      grad.addColorStop(.32,`rgba(181,68,255,${.24*crinkle})`);
      grad.addColorStop(1,'rgba(255,255,255,0)');
      ctx.strokeStyle=grad;ctx.lineWidth=2+seeded(i+180)*6;ctx.beginPath();ctx.moveTo(baseX,y);ctx.quadraticCurveTo(baseX-side*len*.45,y-18+seeded(i+270)*36,baseX-side*len,y+seeded(i+360)*45-20);ctx.stroke();
    }
    ctx.restore();

    // Shimmer sweep
    ctx.save();rounded(175,300,1810,2130,84);ctx.clip();ctx.globalCompositeOperation='screen';
    const sweepX=((phase%240)/240)*(W+900)-450;
    const sweep=ctx.createLinearGradient(sweepX-220,0,sweepX+220,0);
    sweep.addColorStop(0,'rgba(255,255,255,0)');sweep.addColorStop(.5,`rgba(215,145,255,${.08*foil})`);sweep.addColorStop(1,'rgba(255,255,255,0)');
    ctx.fillStyle=sweep;ctx.fillRect(sweepX-250,300,500,2130);ctx.restore();
  }

  function drawAtomLogo(x,y){
    ctx.save();ctx.translate(x,y);ctx.strokeStyle='#fff';ctx.fillStyle='#fff';ctx.lineWidth=4;
    for(let r=0;r<3;r++){ctx.save();ctx.rotate(r*Math.PI/3);ctx.beginPath();ctx.ellipse(0,0,70,28,0,0,Math.PI*2);ctx.stroke();ctx.restore();}
    ctx.beginPath();ctx.arc(0,0,8,0,Math.PI*2);ctx.fill();
    ctx.font='800 20px Arial';ctx.textAlign='center';ctx.fillText('BIT$ ATOM',0,98);ctx.restore();
  }

  function drawArtwork(){
    const x=300,y=720,w=1560,h=1260;
    ctx.save();rounded(x,y,w,h,20);ctx.clip();
    const bg=ctx.createRadialGradient(W/2,1320,100,W/2,1320,900);bg.addColorStop(0,'#4a394f');bg.addColorStop(.4,'#211925');bg.addColorStop(1,'#050407');ctx.fillStyle=bg;ctx.fillRect(x,y,w,h);
    // cloudy depth behind subject
    for(let i=0;i<20;i++){
      const cx=x+seeded(i+20)*w,cy=y+seeded(i+80)*h,r=90+seeded(i+160)*230;
      const g=ctx.createRadialGradient(cx,cy,0,cx,cy,r);g.addColorStop(0,'rgba(226,220,230,.09)');g.addColorStop(1,'rgba(0,0,0,0)');ctx.fillStyle=g;ctx.fillRect(cx-r,cy-r,r*2,r*2);
    }
    if(stencil){
      const base=data.fitMode.value==='cover'?Math.max(w/stencil.width,h/stencil.height):Math.min(w/stencil.width,h/stencil.height);
      const scale=base*(+data.artZoom.value/100);
      const dw=stencil.width*scale,dh=stencil.height*scale;
      const dx=x+(w-dw)/2+(+data.artX.value/100)*(w*.22);
      const dy=y+(h-dh)/2+(+data.artY.value/100)*(h*.22);
      const filters=[];
      if(data.grayscale.checked)filters.push('grayscale(1)');
      filters.push(`contrast(${data.contrast.value}%)`,`brightness(${data.brightness.value}%)`);
      ctx.filter=filters.join(' ');
      ctx.drawImage(stencil,dx,dy,dw,dh);ctx.filter='none';
    }else{
      ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillStyle='#b8a5c2';ctx.font='800 54px Arial';ctx.fillText('UPLOAD STENCIL ARTWORK',W/2,1340);
    }
    const vign=ctx.createRadialGradient(W/2,1320,410,W/2,1320,1100);vign.addColorStop(.42,'rgba(0,0,0,0)');vign.addColorStop(1,'rgba(0,0,0,.82)');ctx.fillStyle=vign;ctx.fillRect(x,y,w,h);
    ctx.restore();
    ctx.save();rounded(x,y,w,h,20);ctx.strokeStyle='#8b39df';ctx.lineWidth=8;ctx.shadowColor='#8b39df';ctx.shadowBlur=18;ctx.stroke();ctx.restore();
  }

  function pill(text,x,y,w,h){
    ctx.save();rounded(x,y,w,h,14);ctx.fillStyle='#100817';ctx.fill();ctx.strokeStyle='#8436d6';ctx.lineWidth=5;ctx.stroke();ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillStyle='#ead7ff';ctx.font='900 34px Arial';wrapText(String(text).toUpperCase(),x+w/2,y+h/2-17,w-28,36);ctx.restore();
  }

  function draw(phase=0){
    const g=ctx.createRadialGradient(W/2,H*.55,120,W/2,H*.55,1500);g.addColorStop(0,'#4d136f');g.addColorStop(.44,'#12031c');g.addColorStop(1,'#020104');ctx.fillStyle=g;ctx.fillRect(0,0,W,H);
    drawStars();drawSmoke(+data.smoke.value,phase);
    glowText('BEYOND TATTOO',W/2,125,130,'#fff7ff',54);
    ctx.save();ctx.textAlign='center';ctx.font='800 58px Arial';ctx.fillStyle='#c06cff';ctx.shadowColor='#8c2fff';ctx.shadowBlur=24;ctx.fillText('S  T  E  N  C  I  L     D  R  O  P',W/2,220);ctx.restore();
    drawWrapper(phase);

    // Header branding inside package
    drawAtomLogo(W/2,405);
    const title=(data.title.value||'UNTITLED').toUpperCase();
    glowText(title,W/2,575,fitText(title,1620,110),'#d8d0dc',34);
    ctx.save();rounded(475,625,1210,88,44);ctx.fillStyle='#431060';ctx.fill();ctx.strokeStyle='#a94cff';ctx.lineWidth=4;ctx.stroke();ctx.textAlign='center';ctx.textBaseline='middle';ctx.font='800 42px Arial';ctx.fillStyle='#dfbcff';ctx.fillText((data.subtitle.value||'TATTOO STENCIL').toUpperCase(),W/2,669);ctx.restore();

    drawArtwork();
    pill(data.edition.value||'EDITION',320,1900,470,112);
    pill(data.detail.value||'HIGH DETAIL',1370,1900,470,112);

    // Access ribbon
    ctx.save();ctx.translate(330,470);ctx.rotate(-.69);ctx.fillStyle='#d9b7ff';ctx.shadowColor='#a747ff';ctx.shadowBlur=20;ctx.fillRect(-110,-45,470,92);ctx.fillStyle='#2a0d3a';ctx.font='900 58px Arial';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText(data.access.value,125,0);ctx.restore();

    // Badge seal
    ctx.save();ctx.beginPath();ctx.arc(1710,480,128,0,Math.PI*2);ctx.fillStyle='#17101d';ctx.fill();ctx.lineWidth=10;ctx.strokeStyle='#b75eff';ctx.shadowColor='#9c3cff';ctx.shadowBlur=20;ctx.stroke();ctx.textAlign='center';ctx.fillStyle='#fff';ctx.font='900 34px Arial';wrapText(data.badge.value.toUpperCase(),1710,453,172,40);ctx.restore();

    const collection=(data.collection.value||'BEYOND COLLECTION').toUpperCase();
    glowText(collection,W/2,2180,fitText(collection,1640,112),'#b65cff',48);
    ctx.save();ctx.textAlign='center';ctx.font='800 38px Arial';ctx.fillStyle='#c5a9d5';ctx.fillText((data.tagline.value||'COLLECT · INK · EVOLVE').toUpperCase(),W/2,2270);ctx.restore();
    ctx.save();ctx.textAlign='center';ctx.font='700 30px Arial';ctx.fillStyle='#9a7cac';ctx.fillText(`DROP ${data.dropDate.value || ''}`,W/2,2340);ctx.restore();

    // subtle grain
    ctx.save();ctx.globalAlpha=.035;for(let i=0;i<1800;i++){const x=seeded(i+900)*W,y=seeded(i+2700)*H;ctx.fillStyle=i%2?'#fff':'#8b36d1';ctx.fillRect(x,y,1.5,1.5);}ctx.restore();
  }

  function loop(){frame++;draw(frame);if(data.animatePreview.checked)raf=requestAnimationFrame(loop);}
  function refresh(){cancelAnimationFrame(raf);draw(frame);if(data.animatePreview.checked)raf=requestAnimationFrame(loop);}

  document.getElementById('stencilFile').addEventListener('change',e=>{const f=e.target.files[0];if(!f)return;const img=new Image();img.onload=()=>{stencil=img;refresh();URL.revokeObjectURL(img.src)};img.src=URL.createObjectURL(f)});
  Object.values(data).forEach(el=>el.addEventListener('input',()=>{if(el.id==='artZoom')document.getElementById('artZoomValue').textContent=el.value+'%';refresh();}));
  document.getElementById('resetArtwork').addEventListener('click',()=>{data.fitMode.value='contain';data.artZoom.value=100;data.artX.value=0;data.artY.value=0;data.contrast.value=112;data.brightness.value=100;data.grayscale.checked=true;document.getElementById('artZoomValue').textContent='100%';refresh();});

  const slug=()=>((data.title.value||'stencil-pack').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,''));
  document.getElementById('downloadPng').addEventListener('click',()=>{draw(96);const a=document.createElement('a');a.download=slug()+'-beyond-tattoo-pack-4k.png';a.href=canvas.toDataURL('image/png');a.click();refresh();});
  document.getElementById('downloadWebp').addEventListener('click',()=>{draw(96);const a=document.createElement('a');a.download=slug()+'-beyond-tattoo-pack.webp';a.href=canvas.toDataURL('image/webp',.94);a.click();refresh();});
  document.getElementById('downloadJson').addEventListener('click',()=>{const out={title:data.title.value,collection:data.collection.value,edition:data.edition.value,detail_level:data.detail.value,access:data.access.value,badge:data.badge.value,subtitle:data.subtitle.value,tagline:data.tagline.value,drop_date:data.dropDate.value,export_size:'2160x2700',artwork:{fit:data.fitMode.value,zoom:+data.artZoom.value,x:+data.artX.value,y:+data.artY.value,contrast:+data.contrast.value,brightness:+data.brightness.value,grayscale:data.grayscale.checked},effects:{smoke:+data.smoke.value,foil:+data.foil.value,crinkle:+data.crinkle.value}};const a=document.createElement('a');a.download=slug()+'-pack-info.json';a.href=URL.createObjectURL(new Blob([JSON.stringify(out,null,2)],{type:'application/json'}));a.click();setTimeout(()=>URL.revokeObjectURL(a.href),1000)});

  if(new URLSearchParams(location.search).get('from')==='ai'){
    const raw=localStorage.getItem('beyond_stencil_ai_payload')||'';
    if(raw){try{const match=raw.match(/\{[\s\S]*\}/);const ai=JSON.parse(match?match[0]:raw);const map={title:'title',collection:'collection',edition:'edition',detail:'detail',detail_level:'detail',access:'access',badge:'badge',subtitle:'subtitle',tagline:'tagline',drop_date:'dropDate'};Object.entries(map).forEach(([source,target])=>{if(ai[source]&&data[target])data[target].value=ai[source];});}catch(e){console.warn('Beyond AI response was not structured pack JSON.');}}
  }
  refresh();
})();
</script>
<?php bos_page_end(); ?>
