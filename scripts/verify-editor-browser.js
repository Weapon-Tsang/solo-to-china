async page => {
 const base='http://127.0.0.1:9411',context=await page.context().browser().newContext({viewport:{width:1440,height:1000}}),p=await context.newPage();
 const posts=await (await p.request.get(base+'/wp-json/wp/v2/posts?slug=upgrade-long-guide')).json();
 await p.goto(base+'/wp-login.php');await p.locator('#user_login').fill('admin');await p.locator('#user_pass').fill('password');await p.locator('#wp-submit').click();
 await p.goto(base+'/wp-admin/post.php?post='+posts[0].id+'&action=edit');
 await p.waitForFunction(()=>window.wp && wp.data && wp.data.select('core/block-editor') && wp.data.select('core/block-editor').getBlocks().length>0);
 const result=await p.evaluate(()=>{
  const walk=blocks=>blocks.flatMap(b=>[{name:b.name,isValid:b.isValid,content:b.originalContent},...walk(b.innerBlocks||[])]);
  const blocks=walk(wp.data.select('core/block-editor').getBlocks());return {count:blocks.length,invalid:blocks.filter(b=>b.isValid===false),imageCount:blocks.filter(b=>b.name==='core/image').length,editorTitle:wp.data.select('core/editor').getEditedPostAttribute('title')};
 });
 const close=p.getByRole('button',{name:'Close',exact:true});if(await close.count()){await close.first().click().catch(()=>{});}
 await p.screenshot({path:'output/playwright/upgrade-editor-1440.png'});await context.close();
 if(result.invalid.length){throw new Error('Invalid Gutenberg blocks: '+JSON.stringify(result.invalid));}
 if(result.imageCount<2){throw new Error('Expected nested screenshot and editorial image blocks');}
 return result;
}
