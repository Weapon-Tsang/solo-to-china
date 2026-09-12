async page => {
    const base = 'http://127.0.0.1:9411';
    const report = {environment:'Chromium desktop browser; simulated viewports; localhost; no CPU/network throttle',pages:[],errors:[],performance:[]};
    page.on('pageerror', error => report.errors.push(error.message));
    await page.addInitScript(() => {
        window.stcLab = {cls:0,lcp:0,longTasks:[]};
        new PerformanceObserver(list => {for(const e of list.getEntries()){if(!e.hadRecentInput){window.stcLab.cls+=e.value;}}}).observe({type:'layout-shift',buffered:true});
        new PerformanceObserver(list => {for(const e of list.getEntries()){window.stcLab.lcp=e.startTime;}}).observe({type:'largest-contentful-paint',buffered:true});
        new PerformanceObserver(list => {window.stcLab.longTasks.push(...list.getEntries().map(e=>Math.round(e.duration)));}).observe({type:'longtask',buffered:true});
    });
    const routes = ['/', '/survival-kit/','/city-guides/','/attraction-guides/','/upgrade-long-guide/','/planner/','/tools/','/tools/find-this-place/','/tools/taxi-card/?entity_key=jingshan-park','/faq/','/contact/','/about/','/privacy-policy/','/terms-of-use/','/not-a-real-page/','/design-system/'];
    for(const path of routes){
        await page.setViewportSize({width:390,height:844});
        const response = await page.goto(base+path);
        await page.locator('main').waitFor();
        const data=await page.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,h1:document.querySelectorAll('h1').length,links:[...document.querySelectorAll('.stc-image-card__link')].map(a=>a.href)}));
        report.pages.push({path,status:response.status(),...data});
        if(data.scroll>data.width+1){throw new Error('Horizontal overflow: '+path+' '+JSON.stringify(data));}
        if(data.h1!==1){throw new Error('Expected one H1: '+path);}
        if(response.status() !== (path==='/not-a-real-page/'?404:200)){throw new Error('Unexpected HTTP status: '+path);}
    }
    const representative=[['home','/'],['cities','/city-guides/'],['article','/upgrade-long-guide/'],['finder','/tools/find-this-place/'],['taxi','/tools/taxi-card/?entity_key=jingshan-park']];
    for(const [name,path] of representative){
        for(const width of [390,1440]){
            await page.setViewportSize({width,height:width===390?844:1000});await page.goto(base+path);
            await page.locator('img').evaluateAll(async imgs=>{for(const img of imgs){img.loading='eager';await img.decode().catch(()=>{});}});
            await page.waitForLoadState('networkidle');
            await page.waitForFunction(()=>[...document.querySelectorAll('img')].every(img=>img.complete && img.naturalWidth>0));
            if(name==='article'){
                const widths=await page.locator('.stc-content-block--steps figure').evaluateAll(nodes=>nodes.map(n=>n.getBoundingClientRect().width));
                if(!widths.length || widths.some(w=>w<150)){throw new Error('Step screenshot collapsed into its marker column');}
            }
            await page.screenshot({path:'output/playwright/upgrade-'+name+'-'+width+'.png',fullPage:true});
        }
    }
    await page.goto(base+'/');
    for(const width of [320,375,390,430,768,839,840,841,1280,1440]){
        await page.setViewportSize({width,height:900});
        await page.waitForFunction(()=>document.documentElement.scrollWidth<=innerWidth+1);
        const reveal=page.locator('[data-stc-guide-reveal]').first();
        if(width<=840){
            if(!await reveal.isVisible()){throw new Error('Missing More at '+width);}
            if(await reveal.getAttribute('aria-expanded')!=='true'){await reveal.click();}
            await page.setViewportSize({width:841,height:900});
            await page.waitForFunction(()=>document.querySelector('[data-stc-guide-reveal]').hidden);
            await page.setViewportSize({width,height:900});
            await page.waitForFunction(()=>!document.querySelector('[data-stc-guide-reveal]').hidden);
            if(await reveal.getAttribute('aria-expanded')!=='true'){throw new Error('More lost state across breakpoint');}
            await reveal.click();
            if(await page.locator('#home-city-grid .stc-image-card:visible').count()!==4){throw new Error('More collapse failed');}
        }
    }
    // Canonical entity links must navigate to real posts, never back to a hub.
    const cityLinks=await page.locator('#home-city-grid .stc-image-card__link').evaluateAll(nodes=>nodes.map(a=>a.href));
    for(const link of cityLinks){if(link.endsWith('/city-guides/')){throw new Error('Self-link');}const r=await page.request.get(link);if(r.status()!==200){throw new Error('Broken entity link');}}
    // Same local conditions, three reloads; laboratory values, not field INP/p75.
    await page.setViewportSize({width:390,height:844});
    for(let i=0;i<3;i++){
        await page.goto(base+'/');await page.locator('.stc-hero__image').evaluate(img=>img.decode());
        await page.screenshot({path:'output/playwright/upgrade-lab-'+i+'.png'});
        const sample=await page.evaluate(()=>({...window.stcLab,hero:document.querySelector('.stc-hero__image').currentSrc,resources:performance.getEntriesByType('resource').map(r=>({url:r.name,transfer:r.transferSize,decoded:r.decodedBodySize}))}));
        if(sample.resources.some(r=>/\/assets\/js\/(tools|place-finder)\.js|hero-home\.png/.test(r.url))){throw new Error('Homepage loads unused tool code or original hero');}
        report.performance.push(sample);
    }
    await page.goto(base+'/upgrade-long-guide/');
    const html=await (await page.request.get(base+'/upgrade-long-guide/')).text();
    if(!/data-stc-guide-toc-list><li><a href=/.test(html)){throw new Error('TOC missing from initial HTML');}
    const ids=await page.locator('.stc-entry-content--guide h2').evaluateAll(nodes=>nodes.map(n=>n.id));
    if(new Set(ids).size!==ids.length){throw new Error('Duplicate H2 IDs');}
    await page.addStyleTag({content:'html {font-size:200% !important}'});
    if(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1)){throw new Error('200% text overflow');}
    await page.emulateMedia({reducedMotion:'reduce'});await page.reload();
    if(await page.evaluate(()=>getComputedStyle(document.documentElement).scrollBehavior)==='smooth'){throw new Error('Reduced motion still forces smooth scrolling');}
    if(report.errors.length){throw new Error('Browser errors: '+report.errors.join(';'));}
    return report;
}
