async page => {
 const base='http://127.0.0.1:9411',report={passed:[],php:{}};
 const check=(v,s)=>{if(!v){throw new Error(s);}report.passed.push(s);};
 for(const port of [9411,9412,9413]){
  const response=await page.request.post('http://127.0.0.1:'+port+'/wp-json/stc-test/v1/recheck');
  check(response.ok(),'Fresh PHP and data invariants: '+port);report.php[port]=await response.json();
 }
 // Hold image responses while exercising the already-rendered navigation.
 const p=await page.context().newPage();await p.setViewportSize({width:390,height:844});
 let release;const gate=new Promise(resolve=>release=resolve);let held=0;
 await p.route('**/*',async route=>{if(route.request().resourceType()==='image'){held++;await gate;}await route.continue();});
 try {
  await p.goto(base+'/',{waitUntil:'domcontentloaded'});
  const more=p.locator('[data-stc-guide-reveal]').first();await more.waitFor();
  check(await p.locator('#home-city-grid .stc-image-card:visible').count()===4,'Initial More is collapsed while images are delayed');
  await more.click();check(await p.locator('#home-city-grid .stc-image-card:visible').count()===8,'More responds before delayed image completion');
  await more.click();check(await p.locator('#home-city-grid .stc-image-card:visible').count()===4 && held>0,'More collapses without a slow-image race');
 } finally {release();}
 await p.waitForLoadState('networkidle');await p.close();
 for(const width of [390,1440]){
  await page.setViewportSize({width,height:width===390?844:1000});await page.goto(base+'/upgrade-long-guide/');
  await page.locator('img').evaluateAll(async nodes=>{for(const img of nodes){img.loading='eager';await img.decode().catch(()=>{});}});
  await page.waitForLoadState('networkidle');
  check(await page.locator('main img').evaluateAll(nodes=>nodes.length>0 && nodes.every(n=>n.complete&&n.naturalWidth>0)),'Article images decoded at '+width);
  check(await page.locator('.stc-content-block--steps figure').evaluateAll(nodes=>nodes.length>0 && nodes.every(n=>n.getBoundingClientRect().width>=150)),'Step screenshots use the text column at '+width);
  if(width===1440){check(await page.locator('.stc-nav a').first().evaluate(n=>getComputedStyle(n).color==='rgb(32, 40, 50)'),'Desktop navigation has dark text on its light header');}
  await page.screenshot({path:'output/playwright/upgrade-article-'+width+'.png',fullPage:true});
  await page.locator('.stc-content-block--steps').screenshot({path:'output/playwright/upgrade-steps-'+width+'.png'});
 }
 return report;
}
