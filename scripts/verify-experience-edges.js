async page => {
 const base='http://127.0.0.1:9411', report={passed:[],api:[],fallbacks:[],diagnostics:null};
 const check=(value,message)=>{if(!value){throw new Error(message);}report.passed.push(message);};
 const browser=page.context().browser();
 const mobile=await browser.newContext({viewport:{width:390,height:844},isMobile:true,hasTouch:true});const p=await mobile.newPage();
 await p.addInitScript(()=>{navigator.share=()=>Promise.reject(new DOMException('Cancelled','AbortError'));Object.defineProperty(navigator,'clipboard',{configurable:true,value:undefined});document.execCommand=()=>false;});
 await p.goto(base+'/upgrade-long-guide/');await p.locator('[data-stc-share-trigger]').click();await p.waitForFunction(()=>!document.querySelector('[data-stc-share-trigger]').disabled);
 check(!await p.locator('[data-stc-share-panel]').isVisible(),'Native share cancellation does not open a second surface');
 await p.evaluate(()=>{navigator.share=()=>{throw new Error('Native service unavailable');};});await p.locator('[data-stc-share-trigger]').click();await p.locator('[data-stc-share-panel]').waitFor();
 check(await p.locator('[data-stc-share-panel]').isVisible(),'Synchronous native share failure falls back');
 await p.locator('[data-stc-share-copy]').click();await p.waitForFunction(()=>document.querySelector('[data-stc-share]').classList.contains('has-copy-fallback'));
 check(await p.locator('[data-stc-share-url]').isVisible(),'Absent Clipboard API provides manual copy');
 await p.screenshot({path:'output/playwright/upgrade-share-fallback-390.png'});await p.keyboard.press('Escape');
 // Enlarged annotated images retain normalized markers and their always-visible legend.
 await p.locator('[data-stc-enlarge]').first().click();await p.locator('.stc-image-dialog').waitFor();
 check(await p.locator('.stc-image-dialog .stc-image-marker').count()>0 && await p.locator('.stc-image-dialog .stc-image-annotations').count()===1,'Enlargement preserves image annotations and text');await p.keyboard.press('Escape');
 const timeout=await mobile.newPage();
 await timeout.addInitScript(()=>{const native=window.setTimeout;window.setTimeout=function(fn,ms,...args){return native(fn,ms===45000?150:ms,...args);};const fetch=window.fetch;window.fetch=function(url,options){if(String(url).includes('/stc/v1/place-finder')){return new Promise((_,reject)=>options.signal.addEventListener('abort',()=>reject(new DOMException('Aborted','AbortError'))));}return fetch.apply(this,arguments);};});
 await timeout.goto(base+'/tools/find-this-place/?stc_fixture=1');await timeout.locator('[data-stc-place-input]').setInputFiles('C:/Users/Mloong/Documents/ChatGPT/solo-to-china/output/playwright/fixture-images/photo.png');await timeout.waitForFunction(()=>!document.querySelector('[data-stc-place-submit]').disabled);await timeout.locator('[data-stc-place-submit]').click();await timeout.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state==='error');
 check((await timeout.locator('[data-stc-place-status]').textContent()).includes('too long') && !(await timeout.locator('[data-stc-place-submit]').isDisabled()),'Deadline abort allows retry; timer accelerated only by test fixture');
 // Live HTTP multipart and WordPress validation with a local provider stub.
 const image=await (await page.request.get(base+'/wp-content/themes/solo-to-china/assets/images/hero-home-640.webp')).body();
 for(const mode of ['ok','413','415','429','503','504','throw','malformed','missing','schema']) {
  await page.request.post(base+'/wp-json/stc-test/v1/reset');
  const response=await page.request.post(base+'/wp-json/stc/v1/place-finder',{headers:{'X-STC-Fixture':'1'},multipart:{image:{name:'source.webp',mimeType:'image/webp',buffer:image},city_hint:mode}});
  const body=await response.text(), expected=mode==='ok'?200:['413','415','429','503','504'].includes(mode)?Number(mode):503;
  report.api.push({mode,status:response.status(),cache:response.headers()['cache-control']});
  check(response.status()===expected,'REST provider fixture maps '+mode+' to '+expected);
  check((response.headers()['cache-control']||'').includes('no-store') && !body.includes('PRIVATE UPSTREAM'),'Private responses and upstream secrets stay protected: '+mode);
  if(mode==='ok'){const data=JSON.parse(body);check(data.primary_candidate.name_zh==='故宫博物院' && data.primary_candidate.sources.viewpoint==='AI_INFERRED','Provider cannot replace canonical name or verify an inferred viewpoint');}
 }
 const status=await page.request.get(base+'/wp-json/stc-test/v1/status');report.diagnostics=await status.json();check(report.diagnostics.php_syntax.passed && report.diagnostics.upgrade.passed,'All owned PHP parsed and upgrade integration assertions passed');
 check(report.diagnostics.image.mime==='image/jpeg' && !report.diagnostics.image.metadata_found,'WordPress re-encodes uploaded pixels before forwarding');
 // Plugin unavailable and parent-only visual fallback use real independent WordPress instances.
 for(const port of [9412,9413]) {
  const fallback=await page.context().newPage();await fallback.setViewportSize({width:390,height:844});
  for(const route of ['/','/upgrade-long-guide/','/tools/','/tools/find-this-place/','/tools/taxi-card/','/design-system/']) {
   const response=await fallback.goto('http://127.0.0.1:'+port+route);const state=await fallback.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,h1:document.querySelectorAll('h1').length,text:document.querySelector('main').textContent}));
   check(response.status()===200 && state.h1===1 && state.scroll<=state.width+1,'Runtime fallback '+port+' '+route);
   if(port===9413){check(!/\[solo_to_china_|Fatal error|Warning:/.test(state.text),'Plugin unavailable shows safe fallback '+route);}
   report.fallbacks.push({port,route,status:response.status()});
  }
  await fallback.screenshot({path:'output/playwright/upgrade-fallback-'+port+'-390.png',fullPage:true});await fallback.close();
 }
 await mobile.close();return report;
}
