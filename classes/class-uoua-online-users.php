<?php

class UOUA_Online_Users{
    
    public function __construct(){        
        add_action('init', array($this,'init_online_check') );
        add_action('init', array($this,'verify_user_online_status') );
        add_action('init', array($this,'update_user_online_status') );
        add_action('wp_login', array($this,'init_online_user') ); 

        add_action('clear_auth_cookie', array($this,'update_online_status_logout'),1); 

        add_filter('upme_after_sidebar_mini_profile_title', array($this,'display_online_status'),10,2);
        add_filter('upme_after_mini_profile_title', array($this,'display_online_status'),10,2);
        add_filter('upme_after_profile_title',array($this,'display_online_status'),10,2);

                    
    }

    public function init_online_check(){
        $this->online_users =  get_option('upme_users_online');
    }
    
    public function verify_user_online_status(){
        
        if(is_user_logged_in()  ){

            $this->online_users[get_current_user_id()] = current_time('timestamp');

            update_option('upme_users_online', $this->online_users );
            
        }
    }

    public function init_online_user(){        
        $this->verify_user_online_status();
    }

    public function update_user_online_status(){

        $last_updated = get_option( 'upme_users_online_last_updated' );

        if( $last_updated && $last_updated > strtotime( '-10 minutes' ) )
            return;
            
        if ( is_array( $this->online_users ) ) {
            foreach( $this->online_users as $user_id => $last_seen ) {
                if ( ( current_time('timestamp') - $last_seen ) > ( 60 * 10 ) ) {
                    unset( $this->online_users[$user_id] );
                }
            }
            update_option('upme_users_online', $this->online_users );
        }
    
        update_option( 'upme_users_online_last_updated', time() );

    }

    public function is_user_online( $user_id ) {
        if ( isset( $this->online_users[ $user_id ] ) )
            return true;
        return false;
    }

    public function update_online_status_logout(){
        global $current_user;
        $user_id = isset($current_user->ID) ? $current_user->ID : 0;
        if(isset($this->online_users[$user_id])){
            unset( $this->online_users[$user_id] );
            update_option('upme_users_online', $this->online_users );
        }
    }

    public function display_online_status($display,$params){

        extract($params);
        if(isset($this->online_users[$user_id])){
            $display = '<span class="uoua-online-panel">
                            <span class="uoua-online">&nbsp;</span>
                            <span class="uoua-online-text">'.__('Online','uoua').'</span>
                            
                        </span>';
        }else{
            $display = '<span class="uoua-offline-panel">
                            <span class="uoua-offline">&nbsp;</span>
                            <span class="uoua-offline-text">'.__('Offline','uoua').'</span>
                            
                        </span>';
            
        }
        return $display;
    }
}


