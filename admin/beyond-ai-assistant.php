<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/app-layout.php';
require_once __DIR__ . '/../includes/beyond-ai.php';
bos_require_admin();
$wallet = bos_page_start('Admin', 'Beyond AI Assistant', 'Ask. Generate. Fix. Build.');
$today = beyond_ai_today_usage();
?>
<main class="bos-main">
<section class="bos-hero">
  <span class="bos-kicker">Private Admin AI</span><h1>Beyond AI Assistant</h1><p>Ask. Generate. Fix. Build.</p>
  <div class="bos-actions"><a class="bos-btn secondary" href="index.php">Back to Admin</a><a class="bos-btn secondary" href="stencil-pack-generator.php">Stencil Pack Generator</a></div>
</section>
<section class="bos-section">
<div class="bos-grid" style="grid-template-columns:minmax(0,1.45fr) minmax(280px,.55fr);align-items:start">
<article class="bos-card" style="padding:0;overflow:hidden">
  <div id="conversation" style="min-height:440px;max-height:620px;overflow:auto;padding:22px;display:grid;gap:14px;background:linear-gradient(180deg,#fff,#f5f3fb)"></div>
  <form id="aiForm" style="padding:18px;border-top:1px solid #ddd6eb;display:grid;gap:12px" enctype="multipart/form-data">
    <textarea class="bos-input" id="prompt" name="prompt" rows="4" placeholder="Ask Beyond AI to generate, explain, troubleshoot or plan…" required style="resize:vertical"></textarea>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <label class="bos-btn secondary" style="cursor:pointer">Attach image or file<input id="attachment" name="attachment" type="file" hidden accept="image/*,.pdf,.txt,.md,.json,.csv,.php,.js,.css,.html"></label>
      <span id="fileName" style="font-size:.9rem;opacity:.72">No attachment</span>
      <div style="margin-left:auto;display:flex;gap:8px">
        <label><input type="radio" name="mode" value="quick" checked> Quick</label>
        <label><input type="radio" name="mode" value="advanced"> Advanced</label>
      </div>
    </div>
    <div class="bos-actions" style="margin:0"><button class="bos-btn" id="sendBtn" type="submit">Send request</button><button class="bos-btn secondary" id="clearBtn" type="button">Clear conversation</button></div>
  </form>
</article>
<aside style="display:grid;gap:16px">
  <article class="bos-card"><span class="bos-kicker">Today · Vancouver</span><div class="bos-stat-grid" style="grid-template-columns:1fr 1fr;margin-top:12px"><div class="bos-stat"><b id="requestCount"><?= (int)$today['requests'] ?></b><span>Requests</span></div><div class="bos-stat"><b id="costDisplay">$<?=number_format((float)$today['estimated_cost'],4)?></b><span>Estimated API cost</span></div></div><p style="margin-bottom:0;opacity:.7;font-size:.88rem">Cost remains $0 until per-million-token prices are added to protected configuration.</p></article>
  <article class="bos-card"><h2 style="margin-top:0">Prompt templates</h2><div id="templates" style="display:grid;gap:8px"></div></article>
  <article class="bos-card"><h2 style="margin-top:0">Response actions</h2><div class="bos-actions" style="display:grid"><button class="bos-btn secondary" id="copyBtn" type="button">Copy response</button><button class="bos-btn secondary" id="stencilBtn" type="button">Send to Stencil Pack Generator</button></div></article>
</aside>
</div>
</section>
</main>
<script>
(() => {
 const key='beyond_ai_conversation_v1'; let messages=[]; let lastResponse='';
 const convo=document.getElementById('conversation'), form=document.getElementById('aiForm'), prompt=document.getElementById('prompt'), attachment=document.getElementById('attachment');
 const templates=[
  ['Stencil pack info','Create JSON pack details for today’s stencil. Ask me for any missing title or collection information.'],
  ['Fix a PHP error','Review this PHP error and give me the exact likely cause, safest fix, and test steps:\n'],
  ['Release caption','Write a polished Instagram caption for this Beyond OS release:\n'],
  ['Build checklist','Turn these notes into a prioritized implementation checklist:\n'],
  ['Brainstorm','Brainstorm practical Beyond OS ideas for:\n']
 ];
 const escape=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 function render(){convo.innerHTML=''; if(!messages.length) convo.innerHTML='<div style="text-align:center;padding:80px 20px;opacity:.7"><strong>Beyond AI is ready.</strong><br>Ask a question or choose a template.</div>'; messages.forEach(m=>{const d=document.createElement('div');d.style.cssText=`max-width:88%;padding:14px 16px;border-radius:16px;white-space:pre-wrap;line-height:1.5;${m.role==='user'?'margin-left:auto;background:#2f1456;color:#fff':'margin-right:auto;background:#fff;border:1px solid #ddd6eb;color:#21152d'}`;d.innerHTML=escape(m.text);convo.appendChild(d)});convo.scrollTop=convo.scrollHeight;localStorage.setItem(key,JSON.stringify(messages.slice(-20)));}
 try{messages=JSON.parse(localStorage.getItem(key)||'[]');if(!Array.isArray(messages))messages=[]}catch(e){messages=[]} render();
 templates.forEach(([name,text])=>{const b=document.createElement('button');b.type='button';b.className='bos-btn secondary';b.style.textAlign='left';b.textContent=name;b.onclick=()=>{prompt.value=text;prompt.focus()};document.getElementById('templates').appendChild(b)});
 attachment.onchange=()=>document.getElementById('fileName').textContent=attachment.files[0]?.name||'No attachment';
 form.onsubmit=async e=>{e.preventDefault();const text=prompt.value.trim();if(!text)return;const history=messages.slice(-10);messages.push({role:'user',text});render();prompt.value='';const fd=new FormData(form);fd.set('prompt',text);fd.set('history',JSON.stringify(history));document.getElementById('sendBtn').disabled=true;document.getElementById('sendBtn').textContent='Thinking…';try{const r=await fetch('api/beyond-ai.php',{method:'POST',body:fd});const j=await r.json();if(!r.ok||!j.ok)throw new Error(j.error||'Request failed');lastResponse=j.text;messages.push({role:'assistant',text:j.text});render();document.getElementById('requestCount').textContent=j.usage.today.requests;document.getElementById('costDisplay').textContent='$'+Number(j.usage.today.estimated_cost||0).toFixed(4);}catch(err){messages.push({role:'assistant',text:'Error: '+err.message});render()}finally{document.getElementById('sendBtn').disabled=false;document.getElementById('sendBtn').textContent='Send request';attachment.value='';document.getElementById('fileName').textContent='No attachment'}};
 document.getElementById('clearBtn').onclick=()=>{messages=[];lastResponse='';localStorage.removeItem(key);render()};
 document.getElementById('copyBtn').onclick=async()=>{if(!lastResponse)lastResponse=[...messages].reverse().find(m=>m.role==='assistant')?.text||'';if(lastResponse)await navigator.clipboard.writeText(lastResponse)};
 document.getElementById('stencilBtn').onclick=()=>{if(!lastResponse)lastResponse=[...messages].reverse().find(m=>m.role==='assistant')?.text||'';if(!lastResponse)return;localStorage.setItem('beyond_stencil_ai_payload',lastResponse);location.href='stencil-pack-generator.php?from=ai'};
})();
</script>
<?php bos_page_end(); ?>
