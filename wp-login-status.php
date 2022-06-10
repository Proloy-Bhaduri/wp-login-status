<?php 
add_action( 'init' , function(){
	date_default_timezone_set(  get_option('timezone_string') );
	if( is_user_logged_in() ) :
	  $uid = get_current_user_id();
	if( get_user_meta($uid,'_online',true ) == '' ){
		add_user_meta($uid,'_online',1);	
	}
	if(get_user_meta($uid,'_online',true ) == 0 ){
		update_user_meta($uid,'_online',1 );
	}
	if( get_user_meta($uid,'_last_seen',true ) == '' ){
		date_default_timezone_set(  get_option('timezone_string') );
		add_user_meta($uid,'_last_seen',current_time( 'timestamp',0 ) );	
	}
	endif;
} );
add_action('wp_login',function($user_login, WP_User $user){
		  update_user_meta( $user->ID,'_online',1);
},10,2);

//when logged out 
add_action('wp_logout',function( $user_id ){
	date_default_timezone_set(  get_option('timezone_string') );
	 update_user_meta( $user_id,'_online',0);
	 update_user_meta($user_id,'_last_seen', current_time( 'timestamp',0 )  );	
});

function pb_online_time_ago( $user_id ) {
	date_default_timezone_set(  get_option('timezone_string') );
	$last_seen = get_user_meta( $user_id , '_last_seen',true);
	return human_time_diff( $last_seen, current_time( 'timestamp',0 ) ).' '.__( ' ago' );
}



/* New Column */

function pb_new_user_table( $column ) {
    $column['online'] = '<strong>Online</strong>';
    return $column;
}
add_filter( 'manage_users_columns', 'pb_new_user_table' );

function pb_modify_user_table_row( $val, $column_name, $user_id ) {
    if($column_name == 'online') {
			  $online_s = get_user_meta( $user_id , '_online',true );
			 if($online_s == 1 ){
				 $val = '<span title="Online" class="pb_dot pb__online" style="color: #fff;width:6px;height:6px; padding: 0.2em 0.4em; background : 	#03ab54; border-radius : 5px;">Online</span>' ;
			 } else {
		            $val = '<span title="Offline" class="pb_dot pb__offline" style="width:6px;height:6px; background : 	rgba(0,0,0,0.1); border-radius : 5px;padding: 0.2em 0.4em;">Last Seen '.pb_online_time_ago( $user_id ).'</span>';
			 }
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'pb_modify_user_table_row', 10, 3 );
?>
