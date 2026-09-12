<?php
/** Real WordPress invariant checks, invoked only by the disposable Playground MU fixture. */
function stc_verify_upgrade_invariants() {
    $passed=array();
    $assert=function($condition,$name) use(&$passed){if(!$condition){throw new RuntimeException($name);} $passed[]=$name;};
    require_once '/tmp/solo-to-china-scripts/verify-php-syntax-runtime.php';
    $count=stc_verify_owned_php_syntax();
    $assert($count>30,'All owned PHP token-parses');
    $saved_content=$GLOBALS['stc_rendered_article_content'] ?? null;
    $GLOBALS['stc_rendered_article_content']='<p>A short article without headings.</p>';
    ob_start();stc_render_guide_toc();$toc=ob_get_clean();
    $assert(''===$toc,'An article without H2 does not render an empty navigation');
    $GLOBALS['stc_rendered_article_content']=$saved_content;
    if (!function_exists('stc_tools_get_places')) {return array('passed'=>$passed,'php_files'=>$count,'tools'=>'inactive');}
    $catalog=json_decode(file_get_contents(STC_TOOLS_PATH.'data/destinations-v1.json'),true);
    $assert(!is_wp_error(stc_tools_validate_catalog($catalog)),'Bundled catalog validates');
    $before=get_option('stc_destination_catalog_active','');$previous=get_option('stc_destination_catalog_previous','');
    try {
        foreach(array('version','unknown','duplicate','evidence','unknown-evidence') as $case) {
            $bad=$catalog;
            if('version'===$case){$bad['schema_version']='2';}
            if('unknown'===$case){$bad['places'][0]['raw_html']='<script>bad</script>';}
            if('duplicate'===$case){$bad['places'][]=$bad['places'][0];}
            if('evidence'===$case){$bad['places'][0]['evidence']['address']=array('source_url'=>'https://www.dpm.org.cn/','checked_at'=>'2999-12-31');}
            if('unknown-evidence'===$case){$bad['places'][0]['sources']['address']='UNKNOWN';$bad['places'][0]['evidence']['address']=array('unexpected'=>'bad');}
            $assert(is_wp_error(stc_tools_import_catalog($bad)) && get_option('stc_destination_catalog_active','')===$before,'Rejected catalog leaves active data intact: '.$case);
        }
        $valid=$catalog;$valid['version']='1.0.1';
        $valid['places'][0]['viewpoint_name_zh']='Synthetic fixture viewpoint';$valid['places'][0]['viewpoint_name_en']='Synthetic fixture viewpoint';
        $valid['places'][0]['sources']['viewpoint']='VERIFIED';$valid['places'][0]['evidence']['viewpoint']=$valid['places'][0]['evidence']['name_zh'];
        $assert(!is_wp_error(stc_tools_import_catalog($valid)),'Valid catalog update activates');
        $place=stc_tools_normalize_provider_candidate(array('candidate_entity_key'=>'forbidden-city','viewpoint_name_en'=>'MODEL MUST NOT REPLACE VERIFIED DATA'));
        $assert('Synthetic fixture viewpoint'===$place['viewpoint_name_en'] && 'VERIFIED'===$place['sources']['viewpoint'],'Provider cannot overwrite verified viewpoint text');
        $assert(!is_wp_error(stc_tools_rollback_catalog()) && get_option('stc_destination_catalog_active','')===$before,'Catalog rollback restores prior active pointer');
    } finally {update_option('stc_destination_catalog_active',$before,false);update_option('stc_destination_catalog_previous',$previous,false);}
    $matches=stc_tools_resolve_curated_place('West Lake, Huizhou');
    $assert(count($matches)===1 && 'west-lake-huizhou'===$matches[0]['entity_key'],'City-qualified alias disambiguates locally');
    $lease=stc_tools_acquire_lease('fixture-owner',5);$assert($lease && !stc_tools_acquire_lease('fixture-owner',5),'Lease excludes concurrent owner');
    update_option($lease['name'],(time()-1).':expired',false);$new=stc_tools_acquire_lease('fixture-owner',5);stc_tools_release_lease($lease);
    $assert($new && get_option($new['name'])===$new['value'],'Late lease release cannot unlock a newer owner');stc_tools_release_lease($new);
    $bucket='fixture_'.wp_generate_uuid4();
    $assert(true===stc_tools_rate_limit($bucket,2,60,'global') && true===stc_tools_rate_limit($bucket,2,60,'global') && is_wp_error(stc_tools_rate_limit($bucket,2,60,'global')),'Fixed-window rate limit enforces attempt ceiling');
    $reusable=wp_insert_post(array('post_type'=>'wp_block','post_status'=>'publish','post_content'=>'[solo_to_china_taxi_card entity_key="jingshan-park"]'));
    try {$scan=stc_asset_content('<!-- wp:group --><div class="wp-block-group"><!-- wp:block {"ref":'.$reusable.'} /--></div><!-- /wp:group -->');$assert(has_shortcode($scan,'solo_to_china_taxi_card'),'Pre-head discovery resolves nested synced blocks');}
    finally {wp_delete_post($reusable,true);}
    $block=array('type'=>'steps','variant'=>'screenshots','data'=>array('items'=>array('First'),'screenshots'=>array(array('step'=>2,'media_id'=>1,'alt'=>'Synthetic screenshot'))));
    $assert(is_wp_error(stc_cms_validate_page_block($block,0)),'Screenshot cannot target a missing step');
    foreach(array('provider_timeout'=>504,'provider_unavailable'=>503) as $code=>$expected){
        $filter=function() use($code){return new class($code) implements STC_Place_Resolver_Provider {
            private $code;
            public function __construct($code){$this->code=$code;}
            public function is_available(){return true;}
            public function resolve($query){return new WP_Error($this->code,'PRIVATE RESOLVER TOKEN');}
        };};
        add_filter('stc_tools_place_resolver_provider',$filter);
        try {
            $request=new WP_REST_Request('POST','/stc/v1/taxi-card');$request->set_param('query','Synthetic unknown fixture destination');
            $result=stc_tools_rest_taxi_card($request);
            $assert(is_wp_error($result) && $result->get_error_data()['status']===$expected && false===strpos($result->get_error_message(),'PRIVATE'),'Resolver service failure stays distinct from unknown: '.$expected);
        } finally {remove_filter('stc_tools_place_resolver_provider',$filter);}
    }
    return array('passed'=>$passed,'php_files'=>$count,'catalog_records'=>count($catalog['places']));
}
