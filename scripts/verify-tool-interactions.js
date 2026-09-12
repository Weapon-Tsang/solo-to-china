async page => {
 const base='http://127.0.0.1:9411', files='C:/Users/Mloong/Documents/ChatGPT/solo-to-china/output/playwright/fixture-images/';
 const report={passed:[],preparation:[],errors:[]};
 const check=(value,message)=>{if(!value){throw new Error(message);}report.passed.push(message);};
 page.on('pageerror',error=>report.errors.push(error.message));
 await page.emulateMedia({reducedMotion:'reduce'});await page.setViewportSize({width:390,height:844});
 await page.addInitScript(()=>{
  window.fixtureRequests=[];window.fixtureBodies=[];
  const original=window.fetch;
  window.fetch=function(url,options){
   if(String(url).includes('/stc/v1/place-finder')) {
    // Deliberately ignore AbortSignal to test stale response guards as well as cancellation.
    window.fixtureBodies.push(options.body);
    return new Promise((resolve,reject)=>window.fixtureRequests.push({resolve:payload=>resolve(new Response(JSON.stringify(payload),{status:200,headers:{'Content-Type':'application/json'}})),reject}));
   }
   return original.apply(this,arguments);
  };
 });
 await page.goto(base+'/upgrade-tools-lab/?stc_fixture=1');
 const root=page.locator('[data-stc-place-finder]').first(), input=root.locator('[data-stc-place-input]'), submit=root.locator('[data-stc-place-submit]');
 const ready=async()=>{await page.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state!=='processing');};
 const clear=async()=>{if(await root.locator('[data-stc-place-remove]').isVisible()){await root.locator('[data-stc-place-remove]').click();}};
 const upload=async name=>{await input.setInputFiles(files+name);await ready();};
 check(await page.locator('[data-stc-place-finder]').count()===2,'Multiple finder instances render');
 for(const name of ['rotated.jpg','transparent.png','long.png','lowtext.png']) {
  await upload(name);
  const img=root.locator('[data-stc-place-previews] img').last();
  const info=await img.evaluate(async img=>{await img.decode();const blob=await (await fetch(img.src)).blob();return {width:img.naturalWidth,height:img.naturalHeight,bytes:blob.size,metadata:(await blob.text()).includes('PRIVATE_METADATA_TEST')};});
  report.preparation.push({name,...info});
  check(!info.metadata && info.bytes<=2500000,'Prepared copy bounded and metadata removed: '+name);
  if(name==='rotated.jpg'){check(info.width===64 && info.height===32,'EXIF orientation preserved');}
  if(name==='long.png'){check(info.height===4096,'Long screenshot uses separate 4096px budget');}
  if(name==='transparent.png'){check(await img.evaluate(img=>{const c=document.createElement('canvas');c.width=c.height=1;const x=c.getContext('2d');x.drawImage(img,100,100,1,1,0,0,1,1);return x.getImageData(0,0,1,1).data[3]===255;}),'Transparent input composited on opaque background');}
 }
 await upload('photo.png');check((await root.locator('[data-stc-place-status]').textContent()).includes('up to 4'),'Fifth photo rejected without losing four');
 await root.getByRole('button',{name:'Move photo 4 earlier',exact:true}).click();check((await root.locator('[data-stc-place-status]').textContent()).includes('order updated'),'Photos reorder');
 await root.getByRole('button',{name:'Remove photo 2',exact:true}).click();check(await root.locator('[data-stc-place-previews] img').count()===3,'Individual remove preserves remaining photos');
 await clear();
 for(const name of ['corrupt.jpg','unsupported.gif','oversize.jpg','pixels.png']) {await upload(name);check(await root.getAttribute('data-state')==='error','Invalid image rejected: '+name);check(await root.locator('[data-stc-place-previews] img').count()===0,'Invalid image never enters request: '+name);}
 await upload('photo.png');
 const payload=(name='Latest response')=>({status:'matched',confidence:'high',match_level:'attraction',primary_candidate:{name_en:name,name_zh:'测试地点',city_en:'Test city',city_zh:'测试城市',entity_key:'',match_level:'attraction',confidence:'high',sources:{name_zh:'AI_INFERRED',city:'AI_INFERRED',address:'UNKNOWN',viewpoint:'UNKNOWN'},viewpoint_status:'unknown'},alternative_candidates:[]});
 await submit.click();check(await root.getAttribute('data-state')==='identifying','Submit enters identifying');
 await root.locator('[data-stc-place-cancel]').click();check(await root.getAttribute('data-state')==='cancelled','Cancel retains prepared input');
 await submit.click();
 await page.evaluate(data=>window.fixtureRequests[0].resolve(data),payload('STALE A'));
 check(await root.getAttribute('data-state')==='identifying' && await submit.isDisabled(),'Old response and finally cannot clear new loading state');
 await page.evaluate(data=>window.fixtureRequests[1].resolve(data),payload());
 await root.locator('[data-stc-place-result] h3').waitFor();check(!(await root.textContent()).includes('STALE A'),'Only latest response displayed');
 check(!await root.getByRole('button',{name:'Show Taxi Card',exact:true}).count(),'Unknown entity offers no fake Taxi Card');
 // Changing the hint or photos invalidates the current request too.
 await submit.click();await root.locator('[name=city_hint]').fill('Shanghai');await upload('lowtext.png');await submit.click();
 await page.evaluate(data=>window.fixtureRequests[2].resolve(data),payload('STALE B'));
 check(await root.getAttribute('data-state')==='identifying','Replacing inputs protects new request');
 await page.evaluate(()=>window.fixtureRequests[3].resolve({status:'ambiguous',confidence:'medium',match_level:'city',primary_candidate:null,alternative_candidates:[{name_en:'West Lake',city_en:'Hangzhou'},{name_en:'West Lake',city_en:'Huizhou'}]}));
 await root.locator('.stc-place-choice').first().waitFor();check(await root.locator('.stc-place-choice').count()===2,'Medium confidence requires explicit candidate choice');
 await submit.click();await page.evaluate(()=>window.fixtureRequests[4].resolve({status:'unknown',confidence:'low',match_level:'unknown',primary_candidate:null,alternative_candidates:[]}));
 await page.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state==='unknown');check(await root.locator('[data-stc-place-previews] img').count()===2,'Low confidence preserves input');
 await submit.click();await page.evaluate(()=>window.fixtureRequests[5].reject(new TypeError('Network connection lost')));await page.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state==='error');
 check(await root.locator('[data-stc-place-previews] img').count()===2,'Network error preserves inputs and allows retry');
 check(await page.locator('[data-stc-place-finder]').nth(1).locator('[data-stc-place-previews] img').count()===0,'Second finder state stays independent');
 await page.screenshot({path:'output/playwright/upgrade-finder-error-390.png',fullPage:true});
 // Real transport handling, controlled HTTP status responses (no provider call).
 await page.goto(base+'/tools/find-this-place/?stc_fixture=1');
  const real=await page.context().newPage();
 // Context did not receive page-specific init scripts: this page uses actual fetch.
 await real.setViewportSize({width:390,height:844});
 for(const code of [413,415,429,503,504]) {
  await real.route('**/stc/v1/place-finder',route=>route.fulfill({status:code,contentType:'application/json',body:JSON.stringify({code:'fixture_error',message:'Controlled service error '+code})}));
  await real.goto(base+'/tools/find-this-place/?stc_fixture=1');await real.locator('[data-stc-place-input]').setInputFiles(files+'photo.png');
  await real.waitForFunction(()=>!document.querySelector('[data-stc-place-submit]').disabled);await real.locator('[data-stc-place-submit]').click();
  await real.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state==='error');
  check((await real.locator('[data-stc-place-status]').textContent()).includes(String(code)),'HTTP '+code+' has visible error and retry');
  await real.unroute('**/stc/v1/place-finder');
 }
 for(const body of ['not json','{}','{"confidence":"invalid"}']) {
  await real.route('**/stc/v1/place-finder',route=>route.fulfill({status:200,body}));await real.locator('[data-stc-place-submit]').click();await real.waitForFunction(()=>document.querySelector('[data-stc-place-finder]').dataset.state==='error');
  check(!(await real.locator('[data-stc-place-submit]').isDisabled()),'Malformed response allows retry: '+body);await real.unroute('**/stc/v1/place-finder');
 }
 await real.goto(base+'/tools/find-this-place/');check(await real.locator('[data-stc-place-submit]').isDisabled() && (await real.locator('[data-stc-place-status]').textContent()).includes('not configured'),'Missing provider configuration is visible without paid probe');
 await real.goto(base+'/upgrade-tools-lab/');
 const taxi=real.locator('[data-stc-taxi-tool]').first(), driver=taxi.locator('[data-stc-driver-mode]');
 await taxi.locator('[data-stc-taxi-fullscreen]').click();check(await driver.evaluate(n=>n.open),'Native Driver Mode opens');
 await real.keyboard.press('Shift+Tab');check(await driver.evaluate(n=>n.contains(document.activeElement)),'Driver Mode keeps keyboard focus inside');
 await real.keyboard.press('Escape');check(await taxi.locator('[data-stc-taxi-fullscreen]').evaluate(n=>n===document.activeElement),'Driver Mode Escape restores focus');
 await real.evaluate(()=>{Object.defineProperty(navigator,'clipboard',{configurable:true,value:{writeText:()=>Promise.reject(new Error('Denied'))}});document.execCommand=()=>false;});
 await taxi.locator('[data-stc-taxi-copy]').click();await taxi.locator('[data-manual-copy]').waitFor();check((await taxi.locator('[data-manual-copy]').inputValue()).includes('景山公园'),'Copy refusal reveals selectable destination');
 await taxi.locator('[data-stc-taxi-fullscreen]').click();await driver.locator('[data-stc-driver-copy]').click();await driver.locator('[data-manual-copy]').waitFor();await real.screenshot({path:'output/playwright/upgrade-driver-copy-fallback-390.png'});await real.keyboard.press('Escape');
 await taxi.locator('[name=query]').fill('west lake');await taxi.locator('button[type=submit]').click();await taxi.locator('[data-stc-taxi-choices] button').first().waitFor();check(await taxi.locator('[data-stc-taxi-choices] button').count()===2,'Same-name destinations disambiguate by city');
 await taxi.locator('[data-stc-taxi-choices] button').last().click();check((await taxi.locator('[data-stc-taxi-name-en]').textContent()).includes('West Lake'),'Candidate selection fills a destination');
 check((await real.locator('[data-stc-taxi-tool]').nth(1).locator('[data-stc-taxi-name-en]').textContent()).includes('Forbidden City'),'Second Taxi instance unchanged');
 const shares=real.locator('[data-stc-share]');check(await shares.count()===2,'Two Share instances render');
 const ids=await real.locator('[id]').evaluateAll(nodes=>nodes.map(n=>n.id));check(ids.length===new Set(ids).size,'Multiple tools and Share use unique DOM IDs');
 await shares.first().locator('[data-stc-share-trigger]').click();await shares.first().locator('[data-stc-share-panel]').waitFor();
 await shares.nth(1).locator('[data-stc-share-trigger]').click();check(!await shares.first().locator('[data-stc-share-panel]').isVisible(),'Opening Share closes the previous instance');
 await shares.nth(1).locator('[data-stc-share-copy]').click();await shares.nth(1).locator('[data-stc-share-url]').waitFor();check(await shares.nth(1).locator('[data-stc-share-url]').isVisible(),'Share copy failure exposes canonical URL');
 await real.keyboard.press('Escape');check(await shares.nth(1).locator('[data-stc-share-trigger]').evaluate(n=>n===document.activeElement),'Share Escape restores trigger focus');
 await shares.first().locator('[data-stc-share-trigger]').click();await real.locator('h1').click();check(!await shares.first().locator('[data-stc-share-panel]').isVisible(),'Outside click closes nonmodal Share');
 await real.locator('[data-stc-ticket-tool] input[type=date]').fill('2020-01-01');await real.locator('[data-stc-ticket-tool] button[type=submit]').click();check((await real.locator('[data-stc-ticket-result]').textContent()).includes('VISIT DATE PASSED'),'Past ticket date is rejected');
 await real.locator('[data-stc-ticket-tool] input[type=date]').fill('2030-01-01');await real.locator('[data-stc-ticket-tool] button[type=submit]').click();check((await real.locator('[data-stc-ticket-result]').textContent()).includes('RULE NOT CONFIRMED'),'Unknown booking rule makes no exact opening claim');
 check(!await real.locator('[data-stc-ticket-link]').isVisible(),'No fabricated ticket destination');
 const nojs=await page.context().browser().newContext({javaScriptEnabled:false,viewport:{width:390,height:844}});const plain=await nojs.newPage();
 await plain.goto(base+'/');check(await plain.locator('#home-city-grid .stc-image-card:visible').count()===8,'No-JS collection keeps all real links');check(await plain.locator('.stc-nav').isVisible(),'No-JS mobile navigation remains available');
 await plain.goto(base+'/upgrade-long-guide/');check(await plain.locator('[data-stc-guide-toc-list] a').count()>0,'No-JS TOC has initial links');
 await plain.goto(base+'/tools/taxi-card/?entity_key=jingshan-park');check(await plain.locator('[data-stc-taxi-name-zh]').isVisible(),'No-JS prefilled Taxi destination remains readable');check(!await plain.locator('[data-stc-taxi-form]').isVisible(),'No-JS private query never falls through to GET');
 await nojs.close();await real.close();
 check(!report.errors.length,'No uncaught browser errors');return report;
}
