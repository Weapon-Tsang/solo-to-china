<?php
/** LOCAL PLAYGROUND FIXTURE ONLY. Not included in any release ZIP. */
add_shortcode('stc_test_share',function(){
    $post=get_page_by_path('beijing-first-time-city-guide',OBJECT,'post');
    ob_start();stc_render_share_this_page(array('post_id'=>$post->ID));return ob_get_clean();
});
add_filter('stc_tools_place_vision_provider',function($provider){
    if (!isset($_GET['stc_fixture']) && empty($_SERVER['HTTP_X_STC_FIXTURE'])) {return $provider;}
    return new class implements STC_Place_Vision_Provider {
        public function is_available(){return true;}
        public function identify($images,$context=array()){
            $image=$images[0];
            update_option('stc_test_prepared_image',array('mime'=>$image['mime'],'width'=>$image['width'],'height'=>$image['height'],'bytes'=>strlen($image['bytes']),'metadata_found'=>false!==strpos($image['bytes'],'PRIVATE_METADATA_TEST')),false);
            $mode=$context['city_hint'] ?? '';
            if (in_array($mode,array('413','415','429','503','504'),true)) {return new WP_Error('fixture_provider','PRIVATE UPSTREAM MESSAGE',array('status'=>(int)$mode));}
            if ('throw'===$mode) {throw new RuntimeException('PRIVATE UPSTREAM TOKEN');}
            if ('malformed'===$mode) {return 'not json';}
            if ('missing'===$mode) {return array('confidence'=>'high');}
            if ('schema'===$mode) {return array('schema'=>'wrong','status'=>'matched','confidence'=>'high','match_level'=>'attraction','primary_candidate'=>array('candidate_entity_key'=>'forbidden-city'),'alternative_candidates'=>array());}
            return array('status'=>'matched','confidence'=>'high','match_level'=>'attraction','primary_candidate'=>array('candidate_entity_key'=>'forbidden-city','canonical_name_zh'=>'Untrusted provider name','verified_address_zh'=>'Untrusted provider address','viewpoint_name_en'=>'Suggested angle'),'alternative_candidates'=>array());
        }
    };
});
add_action('rest_api_init',function(){
    register_rest_route('stc-test/v1','/recheck',array('methods'=>'POST','permission_callback'=>'__return_true','callback'=>function(){require_once '/tmp/solo-to-china-scripts/verify-upgrade-invariants.php';return stc_verify_upgrade_invariants();}));
    register_rest_route('stc-test/v1','/status',array('methods'=>'GET','permission_callback'=>'__return_true','callback'=>function(){return array('php_syntax'=>get_option('stc_upgrade_php_syntax'),'image'=>get_option('stc_test_prepared_image'),'upgrade'=>get_option('stc_upgrade_results'));}));
    register_rest_route('stc-test/v1','/reset',array('methods'=>'POST','permission_callback'=>'__return_true','callback'=>function(){
        if(!function_exists('stc_tools_client_key')){return false;}
        delete_transient('stc_tool_vision_'.substr(stc_tools_client_key(),0,32));return true;
    }));
});
