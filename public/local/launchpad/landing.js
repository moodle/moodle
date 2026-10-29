/* ToBa Learn LaunchPad landing interactions */
(function () {
'use strict';
const ASSET = (document.body.dataset.assets || '').replace(/\/?$/, '/');
const LOGIN = document.body.dataset.login || '/login/index.php';
const DASH = document.body.dataset.dash || '/my/';
const LOGGED_IN = document.body.dataset.loggedIn === '1';
const PORTAL_HREF = LOGGED_IN ? DASH : LOGIN;
const PORTAL_ATTR = LOGGED_IN ? 'data-moodle-portal' : 'data-moodle-login';
const PORTAL_LABEL = LOGGED_IN ? 'Access portal' : 'Sign in to Learn';

const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
function toast(m){const t=$('#toast');t.textContent=m;t.classList.add('on');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('on'),2400)}
document.addEventListener('click',e=>{const a=e.target.closest('[data-toast]');if(a){e.preventDefault();toast(a.dataset.toast)}});
/** Hugeicons (Iconify) helper — stroke-rounded set */
const hi=(name,size=18)=>`<iconify-icon icon="hugeicons:${name}" width="${size}" height="${size}" aria-hidden="true"></iconify-icon>`;

/* ----- Data ----- */
const TRACKS=[
 {n:'AI & Automation',s:'Build agents and workflows a reviewer can rerun from evidence.',h:'~30h',lab:'AI',icon:'robot-01',img:(ASSET + 'track5.webp'),cap:'Request-triage workflow',
  who:'Employer cohorts and fellowship cohorts',assign:'Employer or programme admin',out:'Runnable workflow, fixtures, and a reviewable runbook',
  ph:['Input contract, routing rules and error evaluation','Local workflow, exception queue and repeat-run evidence','A simulated help desk changes categories; compare rules and results','Demonstrate routes, abstentions and safe boundaries; submit workflow, fixtures and runbook'],
  w:[['Routing correctness',35],['Reproducible evaluation',30],['Safe review boundary',20],['Explanation / runbook',15]]},
 {n:'Full Stack',s:'Ship product slices with persistence, recovery, and accessible states.',h:'~40h',lab:'STACK',icon:'source-code',img:(ASSET + 'track1.webp'),cap:'Recoverable issue board',
  who:'Employer cohorts and fellowship cohorts',assign:'Employer or programme admin',out:'Working app slice with tests and recovery states',
  ph:['Validation, state, persistence and accessibility','Build a create / list / filter flow with persistence','A simulated client reports failed saves and stale edits','Demonstrate creation through reload and recovery; submit source, tests and setup'],
  w:[['Functional correctness',35],['Tests / reproducibility',25],['Accessible recovery states',25],['Maintainability',15]]},
 {n:'Product Management',s:'Write specs and roadmaps you can defend under new constraints.',h:'~28h',lab:'PROD',icon:'task-01',img:(ASSET + 'track2.webp'),cap:'Lab-booking decision pack',
  who:'Employer cohorts and fellowship cohorts',assign:'Employer or programme admin',out:'Decision pack with prototype and change log',
  ph:['Evidence versus assumptions, problem framing and prioritisation','Brief, backlog, acceptance scenarios and prototype','A simulated stakeholder adds capacity and accessibility constraints','Defend the revised roadmap; submit prototype, change log and decision pack'],
  w:[['Evidence / framing',30],['Acceptance coverage',25],['Prioritisation / tradeoffs',25],['Communication / test plan',20]]},
 {n:'Marketing',s:'Run measured campaigns with claim discipline and clear loops.',h:'~28h',lab:'GROW',icon:'megaphone-01',img:(ASSET + 'track3.webp'),cap:'Campus-session campaign',
  who:'Employer cohorts and fellowship cohorts',assign:'Employer or programme admin',out:'Campaign case study and claims register',
  ph:['Funnel arithmetic, audience and claim discipline','Campaign brief, message variants and measurement plan','Analyse synthetic channel results and attribution problems','Present the recommendation and its limits; submit case study and claims register'],
  w:[['Measurement correctness',30],['Audience / message fit',25],['Claim discipline',25],['Experiment design',20]]},
 {n:'Startup',s:'Validate ventures under ambiguity with evidence, not theatre.',h:'~30h',lab:'VENTURE',icon:'rocket-01',img:(ASSET + 'track4.webp'),cap:'Venture validation dossier',
  who:'Employer cohorts and fellowship cohorts',assign:'Employer or programme admin',out:'Evidence register, economics memo, go/revise/stop call',
  ph:['Hypotheses, neutral interviews and cost assumptions','Validation plan and prototype for a campus printing pickup service','Simulated demand falls and fulfilment costs rise','Defend continue / revise / stop; submit evidence register, economics and memo'],
  w:[['Hypothesis / research',30],['Economics / sensitivity',25],['Evidence discipline',25],['Decision / communication',20]]}
];
const GATES=[
 {t:'Foundations',s:'Skill lessons that unlock the next gate.',w:20,tone:'g-blue',icon:'book-open-01',submit:'Lesson evidence a mentor can score',unlock:'Available at course start',say:'Short, text-first lessons and practice checks. Finish with evidence for mentor review. Practice quizzes carry no grade weight.'},
 {t:'Guided Project',s:'Milestones with mentor checkpoints.',w:25,tone:'g-sky',icon:'task-01',submit:'Milestone pack against the rubric',unlock:'Opens after Foundations passes at 70+',say:'Build the track project in milestones. Mentors review against the published rubric before the next gate opens.'},
 {t:'Simulation',s:'Change under real constraint.',w:20,tone:'g-violet',icon:'cube',submit:'Adapted work + labelled change log',unlock:'Opens after Guided Project passes at 70+',say:'A simulated client changes the brief. Show how you adapt. Clients and data are always labelled simulated.'},
 {t:'Demo Day',s:'Defend the work in the room.',w:35,tone:'g-green',icon:'flag-02',submit:'Capstone defense in the room',unlock:'Opens after Simulation passes at 70+',say:'Present the capstone and defend tradeoffs. Every earlier gate must already pass at 70. Same bar for employers and fellows.'}
];
const FAQ=[
  {
    q: 'Is this a paid internship?',
    a: 'No. ToBa Learn is structured learning for employer cohorts and college fellows. Jobs, stipends, and placements are never implied.',
    say: 'Learning that leaves evidence not employment theatre. I won’t promise a job.',
  },
  {
    q: 'What’s the difference between the two portals?',
    a: 'One Moodle account, two portals. The Employer portal upskills assigned employee cohorts. The Fellowship portal bridges college to real-life craft. Same courses, same 70/100 rubric, same honesty.',
    say: 'Two doors, one standard. Dot Dot walks both paths with you.',
  },
  {
    q: 'How do I get into the right portal?',
    a: 'Sign in once. Your enrolment decides which portal opens you do not pick a marketplace aisle. Programme admins assign the cohort and tracks.',
    say: 'Enrolment is the key. Sign in, then open only what was assigned to you.',
  },
  {
    q: 'Can I browse every course?',
    a: 'No open marketplace. You only see tracks your employer or fellowship programme assigns by design. Courses are the highlight, not a catalogue to shop.',
    say: 'Focus is a feature. Your team assigns the track Dot Dot helps you finish it.',
  },
  {
    q: 'When is the certificate issued?',
    a: 'Only when readiness is confirmed: every gate reviewed at 70+, and issuing switched on by your programme. Until then, your portfolio and mentor notes speak.',
    say: 'A certificate means published criteria were met. Nothing more, nothing less.',
  },
  {
    q: 'How does mentor review work?',
    a: 'Mentors score evidence against the published rubric, write one concrete change note, and keep earlier attempts on record. Below 70 keeps the next gate locked; at 70+ it unlocks.',
    say: 'We score the evidence, not the enthusiasm then we name exactly what to change.',
  },
];

/* ----- Hero: typed word + card tilt + resume ----- */
(function(){const words=['evidence','artifacts','a record','proof'];let i=0;const el=$('#word');if(reduce)return;
  setInterval(()=>{i=(i+1)%words.length;let w=words[i],k=0;el.textContent='';const t=setInterval(()=>{el.textContent=w.slice(0,++k);if(k>=w.length)clearInterval(t)},70)},2800)})();
const stage=$('#stage'),card=$('#contCard'),bg=$('#stage .bg');
stage.addEventListener('mousemove',e=>{if(reduce)return;const r=stage.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;
  card.style.transform=`perspective(900px) rotateY(${x*10}deg) rotateX(${-y*8}deg)`;bg.style.transform=`scale(1.08) translate(${x*-14}px,${y*-10}px)`});
stage.addEventListener('mouseleave',()=>{card.style.transform='';bg.style.transform=''});
let pct=28;
function setPct(p){
  pct=p;
  const op=$('#orbitPct'); if(op) op.textContent=p+'%';
  const dp=$('#dashPct'); if(dp) dp.textContent=p+'%';
  const hb=$('#heroBar'); if(hb) hb.style.width=p+'%';
  const wb=$('#winBar'); if(wb) wb.style.width=p+'%';
}
function resume(){
  if(pct>=48){say('Lesson 3 is already saved. Next is the <b>Foundations check</b> progress comes from saved work.');return}
  setPct(42);setTimeout(()=>setPct(48),700);
  const m='Lesson 3 saved to your record';
  const sh=$('#savedHero'); if(sh) sh.textContent=m;
  const sw=$('#savedWin'); if(sw) sw.textContent=m;
  hop();say('Progress comes from your <b>saved activity</b>, never a timer. You are at <b>48%</b>.');
}
const rh=$('#resumeHero'); if(rh) rh.onclick=resume;
const rw=$('#resumeWin'); if(rw) rw.onclick=resume;

/* ----- Portal switcher ----- */
$$('.portal-switch [data-portal]').forEach(btn => btn.addEventListener('click', () => {
  const id = btn.dataset.portal;
  $$('.portal-switch [data-portal]').forEach(b => {
    const on = b === btn;
    b.classList.toggle('on', on);
    b.setAttribute('aria-selected', on ? 'true' : 'false');
  });
  $$('.portal-card').forEach(card => {
    const on = card.id === 'portal-' + id;
    card.classList.toggle('on', on);
    if (on) card.removeAttribute('hidden'); else card.setAttribute('hidden', '');
  });
}));

/* ----- Trust ----- */
$$('.tcard').forEach(c=>c.addEventListener('click',()=>c.setAttribute('aria-expanded',c.getAttribute('aria-expanded')==='true'?'false':'true')));
$$('.chips .chip').forEach(c=>c.addEventListener('click',()=>{$$('.chips .chip').forEach(x=>x.setAttribute('aria-pressed','false'));c.setAttribute('aria-pressed','true');$('#chipNote').textContent=c.dataset.note}));

/* ----- Courses: 5 cards; full detail in popup ----- */
const row=$('#trackRow');
row.innerHTML=TRACKS.map((t,i)=>`<button type="button" class="track" data-i="${i}" id="track${i}" aria-label="View details for ${t.n}">
  <div class="thumb"><img src="${t.img}" alt=""><span class="lab">${t.lab}</span></div>
  <div class="bd">
    <div class="tags">
      <span class="tag asgn">Assigned by admin</span>
      <span class="tag time">${hi('clock-01',12)} ${t.h} guide</span>
    </div>
    <h3>${t.n}</h3>
    <span class="sub">${t.s}</span>
    <div class="card-meta">
      <span>${hi('flag-01',14)} ${t.cap}</span>
      <span>${hi('task-01',14)} 4 gates · 70+</span>
    </div>
    <span class="btn primary start">View details ${hi('arrow-right-02',16)}</span>
  </div>
</button>`).join('');
row.addEventListener('click',e=>{const b=e.target.closest('.track');if(b)openTrack(+b.dataset.i)});

function openTrack(i){
  const t=TRACKS[i], tot=t.w.reduce((a,b)=>a+b[1],0);
  const gatesHtml=t.ph.map((p,k)=>`<li><div>
    <b>${GATES[k].t}</b>
    <div class="gate-meta"><span>${GATES[k].w}% of arc</span><span>Unlock: ${GATES[k].unlock}</span></div>
    <span class="muted" style="font-size:14px;display:block">${p}</span>
    <span class="muted" style="font-size:13px;display:block;margin-top:4px">You submit: ${GATES[k].submit}</span>
  </div></li>`).join('');
  const scoresHtml=t.w.map(x=>`<li><span>${x[0]}</span><b>${x[1]}</b><div class="sbar"><i style="width:${x[1]/tot*100}%"></i></div></li>`).join('');

  $('#drawer').innerHTML=`
    <div class="dimg">
      <img src="${t.img}" alt="">
      <span class="dimg-lab">${t.lab}</span>
      <button type="button" class="close" id="dClose" aria-label="Close">${hi('cancel-01',20)}</button>
    </div>
    <div class="dbody">
      <div>
        <div class="d-tags">
          <span class="tag asgn">Programme track</span>
          <span class="tag mand">Assigned by admin</span>
          <span class="tag time">${hi('clock-01',12)} ${t.h} guide</span>
          <span class="tag asgn">Pass bar 70/100</span>
        </div>
        <span class="eyebrow">Track 0${i+1} · same for both portals</span>
        <h2 id="dTitle" style="margin-top:6px">${t.n}</h2>
        <p class="muted" style="margin-top:8px;font-size:15.5px">${t.s}</p>
      </div>

      <dl class="d-facts">
        <div><dt>${hi('user-group',16)} Who it’s for</dt><dd>${t.who}</dd></div>
        <div><dt>${hi('bookmark-02',16)} Who assigns</dt><dd>${t.assign}</dd></div>
        <div><dt>${hi('flag-01',16)} Capstone</dt><dd>${t.cap}</dd></div>
        <div><dt>${hi('folder-01',16)} You leave with</dt><dd>${t.out}</dd></div>
        <div><dt>${hi('task-01',16)} Structure</dt><dd>Four gates · unlock only after prior gate at 70+</dd></div>
        <div><dt>${hi('award-01',16)} Pass rule</dt><dd>70+ on each gate; certificate only when all gates pass and issuing is on</dd></div>
      </dl>

      <div class="d-section">
        <h3>Four gates · Foundations to Demo Day</h3>
        <ol class="phase-list">${gatesHtml}</ol>
      </div>

      <div class="d-section">
        <h3>Published rubric weights</h3>
        <div class="wbar">${t.w.map(x=>`<i style="width:${x[1]/tot*100}%"></i>`).join('')}</div>
        <ul class="score-list">${scoresHtml}</ul>
      </div>

      <p class="muted" style="font-size:13.5px">Simulation clients and data are always labelled simulated. No paid tools required to pass. You only see this track in Learn after your employer or programme assigns it.</p>

      <div class="d-foot">
        <a class="btn primary" href="${PORTAL_HREF}" ${PORTAL_ATTR}>${PORTAL_LABEL} ${hi('arrow-right-02',16)}</a>
        <span class="muted" style="font-size:13px">${LOGGED_IN ? 'Opens your dashboard. This popup is a preview of programme tracks.' : 'Opens Moodle. This popup is a preview; enrolment decides which assigned tracks you see.'}</span>
      </div>
    </div>`;

  const scrim=$('#scrim');
  scrim.classList.add('on');
  scrim.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
  $('#dClose').focus();
  $('#dClose').onclick=closeTrack;
}
function closeTrack(){
  const scrim=$('#scrim');
  if(!scrim) return;
  scrim.classList.remove('on');
  scrim.setAttribute('aria-hidden','true');
  document.body.classList.remove('modal-open');
}
$('#scrim').addEventListener('click',e=>{if(e.target.id==='scrim')closeTrack()});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeTrack()});

/* ----- Professor track finder ----- */
const FQ=[
 ['What do you enjoy making most?',[['Things that run by themselves',[0]],['Apps people click through',[1]],['Decisions and plans',[2,4]],['Stories that persuade',[3]]]],
 ['A friend asks for help. You…',[['Automate the boring part',[0,1]],['Ask what problem they’re really solving',[2]],['Work out who to tell, and how',[3]],['Wonder if others would pay for it',[4]]]],
 ['Which result would make you proudest?',[['A workflow a reviewer can rerun',[0]],['A form that never loses data',[1]],['A roadmap that survives tough questions',[2]],['A dossier that says “stop” — with evidence',[4,3]]]]
];
let fs=0,score=[0,0,0,0,0];
function renderFinder(){const dots=$$('#dots i');dots.forEach((d,i)=>d.classList.toggle('on',i<=fs&&fs<3||fs>=3));
  if(fs===0&&score.every(v=>!v)){$('#fBubble').innerHTML='Answer three quick questions for a suggested starting track. Your employer or programme admin still assigns what you take.';$('#fOpts').innerHTML='<button class="btn primary" id="fStart">Start questions</button>';$('#fStart').onclick=()=>{fs=0;ask()};return}}
function ask(){const q=FQ[fs];$('#fBubble').innerHTML=`<b>${fs+1}/3 ·</b> ${q[0]}`;$('#fOpts').innerHTML=q[1].map((o,i)=>`<button class="opt" data-i="${i}">${o[0]}</button>`).join('');
  $$('#dots i').forEach((d,i)=>d.classList.toggle('on',i<=fs));
  $$('#fOpts .opt').forEach(b=>b.onclick=()=>{q[1][+b.dataset.i][1].forEach(k=>score[k]++);fs++;setTimeout(()=>{fs<3?ask():result()},reduce?0:180)})}
function result(){const best=score.indexOf(Math.max(...score)),t=TRACKS[best];
  $('#fBubble').innerHTML=`Suggested start: <b>${t.n}</b> — ${t.s.toLowerCase()}. Capstone: <b>${t.cap.toLowerCase()}</b>. <span class="muted">Suggestion only; assignment comes from your programme.</span>`;
  $('#fOpts').innerHTML=`<button class="btn primary" id="fOpen">View ${t.n}</button><button class="btn light" id="fAgain">Try again</button>`;
  $$('.track').forEach(x=>x.classList.remove('pick'));const el=$('#track'+best);el.classList.add('pick');
  el.scrollIntoView({behavior:reduce?'auto':'smooth',block:'nearest'});
  $('#fOpen').onclick=()=>openTrack(best);$('#fAgain').onclick=()=>{fs=0;score=[0,0,0,0,0];$$('.track').forEach(x=>x.classList.remove('pick'));ask()};
  say(`Suggested track: <b>${t.n}</b>. Open it to see all four gates.`)}
renderFinder();

/* ----- Journey ----- */
let g=0;
$('#gates').innerHTML=GATES.map((x,i)=>`<button type="button" class="gate ${x.tone}" role="tab" aria-selected="${i===0}" data-i="${i}">
  <span class="gi">${hi(x.icon,20)}</span>
  <div class="gb"><b>${x.t}</b><span>${x.s}</span></div>
  <span class="gw">${x.w}%</span>
  <span class="gchev">${hi('arrow-right-02',16)}</span>
</button>`).join('');
function setGate(i){
  g=(i+4)%4;
  const x=GATES[g];
  $$('.gate').forEach((b,k)=>b.setAttribute('aria-selected',k===g));
  $$('.jan').forEach((n,k)=>n.classList.toggle('on',k===g));
  $('#jBubble').innerHTML=x.say;
  $('#spine').style.width=x.w+'%';
  $('#jCount').textContent=`Gate ${g+1} of 4`;
  $('#jWeightPct').textContent=x.w+'%';
  const ring=$('#jRing');
  if(ring) ring.style.strokeDashoffset=String(100-x.w);
  $('#jTitle').textContent=x.t;
  $('#jSummary').textContent=x.s;
  $('#jSubmit').textContent=x.submit;
  $('#jUnlock').textContent=x.unlock;
  $('#jPct').textContent=x.w+'% Complete';
  $('#jNext').innerHTML=g===3?`Start over ${hi('refresh',16)}`:`Next gate ${hi('arrow-right-02',16)}`;
}
$('#gates').addEventListener('click',e=>{const b=e.target.closest('.gate');if(b)setGate(+b.dataset.i)});
$('#gates').addEventListener('keydown',e=>{if(e.key==='ArrowRight'||e.key==='ArrowLeft'){setGate(g+(e.key==='ArrowRight'?1:-1));$$('.gate')[g].focus()}});
$('#jNext').onclick=()=>setGate(g+1);$('#jPrev').onclick=()=>setGate(g-1);
setGate(0);

/* ----- Rubric (mentor review sample) ----- */
const CR = [
  ['Evidence / problem framing', 30],
  ['Acceptance coverage', 25],
  ['Prioritisation / tradeoffs', 25],
  ['Communication / test plan', 20],
];
const LVL = ['Missing', 'Partial', 'Solid', 'Strong'];
const FR = [0.4, 0.6, 0.8, 1];
const critsEl = $('#crits');
if (critsEl) {
  // Default Partial (0.6) on each criterion = 60/100, below the 70 pass bar.
  critsEl.innerHTML = CR.map((c, i) => `
    <div class="crit">
      <label for="c${i}">${c[0]}<small id="cl${i}">Partial</small></label>
      <input type="range" id="c${i}" min="0" max="3" value="1" aria-valuemin="0" aria-valuemax="3" aria-describedby="cl${i}">
      <output id="co${i}">${Math.round(c[1] * 0.6)}/${c[1]}</output>
    </div>`).join('');

  let lastPass = false;
  let started = false;

  function calc() {
    let t = 0;
    CR.forEach((c, i) => {
      const v = +$('#c' + i).value;
      const s = Math.round(c[1] * FR[v]);
      t += s;
      $('#co' + i).textContent = s + '/' + c[1];
      $('#cl' + i).textContent = LVL[v];
    });

    const ok = t >= 70;
    const vd = $('#verdict');
    if ($('#tot')) $('#tot').textContent = t;
    if (vd) {
      vd.className = 'verdict ' + (ok ? 'ok' : 'no');
      vd.textContent = ok ? '70+ met' : 'Below 70';
    }

    if (ok !== lastPass && started) {
      say(ok
        ? `<b>${t}/100</b>: 70+ met. Guided project unlocks.`
        : `<b>${t}/100</b>: below 70. Next gate stays locked.`);
    }
    lastPass = ok;
  }

  critsEl.addEventListener('input', () => { started = true; calc(); });
  calc();
}

/* ----- FAQ ----- */
const faqList = $('#faqList');
const faqBubbleTxt = $('#faqBubbleTxt');
const faqDefaultSay = faqBubbleTxt ? faqBubbleTxt.textContent : '';
if (faqList) {
  faqList.innerHTML = FAQ.map((f, i) => `
    <details class="q" data-i="${i}">
      <summary>
        <span class="q-text">${f.q}</span>
        <span class="q-toggle" aria-hidden="true">
          <iconify-icon icon="hugeicons:arrow-down-02" width="16" height="16"></iconify-icon>
        </span>
      </summary>
      <p>${f.a}</p>
    </details>`).join('');

  $$('details.q').forEach(d => d.addEventListener('toggle', () => {
    if (!d.open) {
      const anyOpen = $$('details.q').some(o => o.open);
      if (!anyOpen && faqBubbleTxt) faqBubbleTxt.textContent = faqDefaultSay;
      return;
    }
    $$('details.q').forEach(o => { if (o !== d) o.open = false; });
    const item = FAQ[+d.dataset.i];
    if (faqBubbleTxt && item) faqBubbleTxt.textContent = item.say;
    const p = $('#faqProf');
    if (p) { p.classList.remove('nod'); void p.offsetWidth; p.classList.add('nod'); }
  }));
}

/* ----- Smooth Moodle login / portal handoff ----- */
document.addEventListener('click', e => {
  const a = e.target.closest('[data-moodle-login], [data-moodle-portal]');
  if (!a || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || a.target === '_blank') return;
  e.preventDefault();
  const toPortal = a.hasAttribute('data-moodle-portal');
  const href = a.getAttribute('href') || (toPortal ? DASH : LOGIN);
  toast(toPortal ? 'Opening your portal…' : 'Opening Moodle sign-in…');
  document.body.classList.add('is-leaving');
  setTimeout(() => { location.href = href; }, reduce ? 0 : 220);
});

/* ----- In-page nav (smooth + close mobile) ----- */
function goSection(hash) {
  const id = (hash || '').replace(/^#/, '');
  const el = id ? document.getElementById(id) : null;
  if (!el) return;
  el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
  history.replaceState(null, '', '#' + id);
}
document.addEventListener('click', e => {
  const a = e.target.closest('[data-nav]');
  if (!a) return;
  const href = a.getAttribute('href') || '';
  if (!href.startsWith('#')) return;
  e.preventDefault();
  $('#links')?.classList.remove('open');
  $('#menuBtn')?.setAttribute('aria-expanded', 'false');
  goSection(href);
});

/* ----- Nav: active section + mobile menu ----- */
$('#menuBtn').onclick = () => {
  const o = $('#links').classList.toggle('open');
  $('#menuBtn').setAttribute('aria-expanded', o);
};
document.addEventListener('click', e => {
  if (!$('#links')?.classList.contains('open')) return;
  if (e.target.closest('#links') || e.target.closest('#menuBtn')) return;
  $('#links').classList.remove('open');
  $('#menuBtn').setAttribute('aria-expanded', 'false');
});

const NAV_IDS = ['portals', 'courses', 'trust', 'journey', 'mentors', 'become-mentor', 'faq'];
function setActiveNav(id) {
  const href = '#' + id;
  $$('#links a[data-nav]').forEach(a => {
    const on = a.getAttribute('href') === href;
    a.classList.toggle('on', on);
    if (on) a.setAttribute('aria-current', 'true');
    else a.removeAttribute('aria-current');
  });
}

/* ----- Reveal (fail-safe: show after timeout) ----- */
const io = new IntersectionObserver(es => es.forEach(e => {
  if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
}), { threshold: .12 });
$$('.reveal').forEach(el => io.observe(el));
setTimeout(() => $$('.reveal').forEach(el => el.classList.add('in')), 2500);

/* ----- Product window tabs ----- */
$$('.wside [data-panel]').forEach(btn => btn.addEventListener('click', () => {
  $$('.wside [data-panel]').forEach(b => { b.classList.remove('on'); b.setAttribute('aria-selected', 'false'); });
  btn.classList.add('on'); btn.setAttribute('aria-selected', 'true');
  $$('.wpanel').forEach(p => p.classList.toggle('on', p.id === btn.dataset.panel));
}));

/* ----- Orbit companion (quiet: greet once, tips on click) ----- */
const orbit = $('#orbit'), sayBox = $('#say');
let hidden = false, sayT;
const TIPS = [
  'Progress comes from saved records, never a timer.',
  'A saved draft isn’t handed in until you submit.',
  'Simulated clients are always labelled simulated.',
  'A high capstone mark can’t make up for an unpassed phase.',
  'Practice quizzes carry no grade weight just learning.',
  'Employer teams and fellows share the same 70/100 gate.',
  'Two portals, one standard Employer and Fellowship.',
  'Use the top nav to jump between sections.'
];
let tipI = 0, cur = '';
function say(html, ms = 6500) {
  if (hidden) return;
  $('#sayTxt').innerHTML = html;
  sayBox.classList.remove('hide');
  clearTimeout(sayT);
  sayT = setTimeout(() => sayBox.classList.add('hide'), ms);
}
function hop() { orbit.classList.remove('hop'); void orbit.offsetWidth; orbit.classList.add('hop'); }
$('#sayX').onclick = e => { e.stopPropagation(); sayBox.classList.add('hide'); };
$('#botBtn').onclick = () => { hop(); say(TIPS[tipI++ % TIPS.length]); };
$('#orbitHide').onclick = () => {
  hidden = !hidden;
  orbit.style.display = hidden ? 'none' : '';
  $('#orbitHide').textContent = hidden ? 'Show Dot Dot' : 'Hide Dot Dot';
};
const secIO = new IntersectionObserver(es => {
  es.forEach(e => {
    if (!e.isIntersecting) return;
    const id = e.target.id;
    if (id === cur) return;
    cur = id;
    if (NAV_IDS.includes(id)) setActiveNav(id);
  });
}, { rootMargin: '-40% 0px -45% 0px' });
['hero', 'portals', 'courses', 'trust', 'journey', 'product', 'mentors', 'become-mentor', 'outcomes', 'faq'].forEach(id => {
  const el = document.getElementById(id);
  if (el) secIO.observe(el);
});
addEventListener('mousemove', e => {
  if (reduce || hidden) return;
  const r = orbit.getBoundingClientRect(), dx = e.clientX - (r.left + r.width / 2), dy = e.clientY - (r.top + r.height / 2);
  $('#tilt').style.transform = `rotate(${Math.max(-12, Math.min(12, dx / 40))}deg) translateY(${Math.max(-6, Math.min(6, dy / 80))}px)`;
});
setTimeout(() => say('Hi, I’m <b>Dot Dot</b>. Click me for tips, or use the nav to jump around.'), 900);

})();
