<?php
/** Disposable-only integration assertions and realistic edge-case fixtures. */
function stc_upgrade_assert( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }

function stc_playground_upgrade() {
	require_once '/tmp/solo-to-china-scripts/verify-php-syntax-runtime.php';
	stc_verify_owned_php_syntax();
	$media = stc_playground_install_media_fixture();
	$beijing = get_page_by_path( 'beijing-first-time-city-guide', OBJECT, 'post' );
	$forbidden = get_page_by_path( 'forbidden-city-first-time-visitor-guide', OBJECT, 'post' );
	update_post_meta( $beijing->ID, '_stc_entity_key', 'beijing' );
	update_post_meta( $forbidden->ID, '_stc_entity_key', 'forbidden-city' );
	stc_upgrade_assert( stc_resolve_entity_guide( 'beijing', 'city-guide' )['id'] === $beijing->ID, 'Entity link missing.' );
	stc_upgrade_assert( null === stc_resolve_entity_guide( 'beijing', 'attraction-guide' ), 'City leaked into attraction resolver.' );
	wp_update_post( array( 'ID' => $forbidden->ID, 'post_status' => 'draft' ) );
	stc_upgrade_assert( null === stc_resolve_entity_guide( 'forbidden-city' ), 'Draft entity was public.' );
	wp_update_post( array( 'ID' => $forbidden->ID, 'post_status' => 'publish' ) );
	stc_upgrade_assert( null !== stc_resolve_entity_guide( 'forbidden-city' ), 'Published entity cache was stale.' );
	$before = get_option( 'stc_destination_catalog_active', '' );
	stc_upgrade_assert( is_wp_error( stc_tools_import_catalog( array( 'schema_version' => 'bad' ) ) ), 'Invalid catalog was accepted.' );
	stc_upgrade_assert( $before === get_option( 'stc_destination_catalog_active', '' ), 'Failed import modified catalog.' );
	$record = array( 'entity_key'=>'test-place','entity_type'=>'attraction','name_en'=>'Test Place','name_zh'=>'测试地点','city_en'=>'Test City','city_zh'=>'测试城市','aliases'=>array('Test Place'),'sources'=>array('name_zh'=>'UNKNOWN','city'=>'UNKNOWN'),'evidence'=>array() );
	$catalog = array( 'schema_version'=>'1.0','version'=>'1.0.0','places'=>array($record) );
	stc_upgrade_assert( ! is_wp_error( stc_tools_import_catalog( $catalog ) ), 'Valid catalog failed.' );
	stc_upgrade_assert( null !== stc_tools_get_place('test-place'), 'Imported catalog is not active.' );
	stc_tools_rollback_catalog();
	stc_upgrade_assert( $before === get_option('stc_destination_catalog_active',''), 'Catalog rollback failed.' );
	$record['sources']['address']='VERIFIED'; $catalog['places']=array($record);
	stc_upgrade_assert( is_wp_error(stc_tools_import_catalog($catalog)), 'Unsourced verified address was accepted.' );
	$blocks = array();
	foreach ( array('place_info_card','annotated_image','related_guides','quick_facts','steps','route_timeline','pros_cons','comparison_table','image') as $id ) {
		$def = stc_get_component_definition($id); $data=$def['example']; unset($data['type'],$data['variant']);
		if (isset($data['media_id'])) {$data['media_id']=$media;}
		if ('related_guides'===$id) {$data['post_ids']=array($beijing->ID,$forbidden->ID,$forbidden->ID);}
		if ('steps'===$id) {$data['screenshots']=array(array('step'=>1,'media_id'=>$media,'alt'=>'Preparation screenshot fixture','caption'=>'Illustrative fixture, not current app instructions.'));}
		if ('image'===$id) {$data['enlarge']=true;}
		$variant='steps'===$id?'screenshots':('quick_facts'===$id?'detailed':($def['example']['variant'] ?? $def['variants'][0]));
		$block=array('type'=>$id,'variant'=>$variant,'data'=>$data);
		stc_upgrade_assert( true===stc_cms_validate_page_block($block,0), 'New block validation failed: '.$id );
		$serialized=stc_serialize_cms_component_to_post_content($block);
		stc_upgrade_assert(!is_wp_error($serialized), 'New block serialization failed: '.$id);
		$blocks[]=$serialized;
	}
	$bad=array('type'=>'annotated_image','variant'=>'default','data'=>array('media_id'=>$media,'alt'=>'Example','annotations'=>array(array('x'=>2,'y'=>.5,'text'=>'Bad'))));
	stc_upgrade_assert(is_wp_error(stc_cms_validate_page_block($bad,0)),'Out-of-range annotation accepted.');
	$content='<!-- wp:heading --><h2 class="wp-block-heading">Repeated heading</h2><!-- /wp:heading --><!-- wp:paragraph --><p>'.str_repeat('Readable editorial text with practical context. ',100).'</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Repeated heading</h2><!-- /wp:heading -->'.implode("\n",$blocks);
	$id=wp_insert_post(array('post_type'=>'post','post_status'=>'publish','post_title'=>'A long guide with annotated images and destination details','post_name'=>'upgrade-long-guide','post_content'=>$content,'post_excerpt'=>'Disposable integration fixture for the frontend experience upgrade.'));
	update_post_meta($id,'_stc_show_toc',true);update_post_meta($id,'_stc_show_share',true);update_post_meta($id,'_stc_hero_variant','compact');
	$gallery=get_page_by_path('design-system');
	$lab=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Tool interaction fixtures','post_name'=>'upgrade-tools-lab','post_content'=>'[solo_to_china_place_finder][solo_to_china_place_finder][solo_to_china_taxi_card entity_key="jingshan-park"][solo_to_china_taxi_card entity_key="forbidden-city"][solo_to_china_ticket_tool]'));
	// Duplicated Share must use unique IDs and one open surface at a time.
	add_shortcode('stc_test_share',function() use ($beijing) {ob_start();stc_render_share_this_page(array('post_id'=>$beijing->ID));stc_render_share_this_page(array('post_id'=>$beijing->ID));return ob_get_clean();});
	$lab_content=get_post_field('post_content',$lab);
	wp_update_post(array('ID'=>$lab,'post_content'=>$lab_content.'[stc_test_share][stc_test_share]'));
	if($gallery){wp_update_post(array('ID'=>$gallery->ID,'post_content'=>$gallery->post_content."\n".implode("\n",$blocks)));}
	// Enough real local posts to exercise folding; these fixtures never enter release ZIPs.
	foreach(array('shanghai','guangzhou','chengdu','chongqing','xian','hangzhou','zhangjiajie') as $key){
		$id=wp_insert_post(array('post_type'=>'post','post_status'=>'publish','post_title'=>ucfirst($key).' local collection fixture','post_name'=>'upgrade-'.$key,'post_content'=>'<!-- wp:paragraph --><p>Disposable city-link fixture. Not production travel advice.</p><!-- /wp:paragraph -->'));
		update_post_meta($id,'_stc_entity_key',$key);update_post_meta($id,'_stc_guide_type','city-guide');
	}
	// Opt-in local page configuration for browser HTTP mocks, with no paid call.
	wp_mkdir_p(WPMU_PLUGIN_DIR);
	copy('/tmp/solo-to-china-scripts/playground-upgrade-mu.php',WPMU_PLUGIN_DIR.'/stc-upgrade-fixture.php');
	stc_upgrade_assert(!is_wp_error(stc_tools_validate_catalog(json_decode(file_get_contents(STC_TOOLS_PATH.'data/destinations-v1.json'),true))),'Bundled destination catalog invalid.');
	update_option('stc_upgrade_results',array('passed'=>true,'registry'=>STC_COMPONENT_REGISTRY_VERSION,'catalog'=>'1.0.0'),false);
	echo "Upgrade integration assertions passed; local edge-case fixtures installed.\n";
}
