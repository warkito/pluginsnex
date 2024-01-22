<?php
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../../../../wp-load.php";

function wp999_export(){
    wp999_export_centers();
    wp999_export_services();
    wp999_export_cities();
}
function wp999_import(){

    //wp999_import_services();
    //wp999_import_cities();
    //wp999_import_center();
    wp99_update_page_city();
}
function wp999_import_center(){
    $json_file= file_get_contents(BEAUTY_CENTER_PLUGIN_PATH.'sample-data/centers.json');
    $centers = json_decode($json_file,true);
    $acfs=array('googlemap_link','doclib_link','name','phone','email','address','zipcode','transport','opening_hours','description');
    $prefix ="beauty_center_";
    wp900_echo_ligne("START IMPORT CENTERS");
    foreach ($centers as $center){

        $center_data = [
            'post_status'    => 'publish',
            'post_type'      => "beauty_center",
            'post_name'      => sanitize_title($center['post_title']),
            'post_title'     => $center['post_title'],
            'comment_status' => 'closed',
        ];

        $center_id   = wp_insert_post( $center_data );
        wp900_echo_ligne("CENTER ID ".$center_id);
        foreach ($acfs as $acf){
            update_post_meta($center_id,$prefix.$acf,$center[$acf]);
        }
    }

    wp900_echo_ligne("END IMPORT CENTERS");
}
function wp999_import_services(){
    wp900_echo_ligne("START IMPORT SERVICES");
    $json_file= BEAUTY_CENTER_PLUGIN_PATH.'sample-data/services.json';

    $services  = json_decode( file_get_contents($json_file) ,true);
    $parent = 0;
    foreach ($services as $service){
        $args = [
            'name' => $service['name'],
            'slug' => $service['slug'],
            'description' => "",
            'parent' => (int) $parent,
        ];

        $new_term = wp_insert_term( wp_slash( $args['name'] ),'beauty_center_service', $args );

        wp900_echo_ligne("SERVICE TERM ".$new_term['term_id']);
    }

    wp900_echo_ligne("END IMPORT SERVICES");


}
function wp999_import_cities(){
    wp900_echo_ligne("START IMPORT CITIES");
    $json_file = BEAUTY_CENTER_PLUGIN_PATH.'sample-data/cities.json';

    $cities  = json_decode( file_get_contents($json_file) ,true);
    $parent = 0;
    foreach ($cities as $city){
        $args = [
            'name' => $city['name'],
            'slug' => $city['slug'],
            'description' => "",
            'parent' => (int) $parent,
        ];

        $new_term = wp_insert_term( wp_slash( $args['name'] ),'beauty_center_city', $args );

        wp900_echo_ligne("CITY TERM ".$new_term['term_id']);
    }
    wp900_echo_ligne("END IMPORT CITIES");

}
function wp99_update_page_city(){
    wp900_echo_ligne("START IMPORT PAGES CITIES");
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'page',
        'post_status'      => 'publish',
        'fields'           => '',
    );

    $pages = get_posts( $args );

    foreach ($pages as $page){
        $page_city= get_post_meta($page->ID,'nomducentrelanding3',true);

        if(!empty($page_city)){
            $beauty_center = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'beauty_center',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'beauty_center_city',
                            'field' => 'slug',
                            'terms' => "%".sanitize_title($page_city).'%',
                            'compare' => 'LIKE'
                        )
                    )
                )
            );
            if(!empty($beauty_center)){
                echo 'FOUND :' .$page_city.'-->'.$page->ID.'-->'.$beauty_center[0]->post_name."\n";
                 update_post_meta($page->ID, '_beauty_center_name', $page_city);
                 update_post_meta($page->ID, '_beauty_center_id',$beauty_center[0]->ID);
            }else{
                echo 'NOT FOUND :' .$page_city.'-->'.$page->ID."\n";

            }
        }

    }

    wp900_echo_ligne("END IMPORT PAGES CITIES");
}
function wp999_export_centers(){
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'beauty_center',
        'post_status'      => 'publish',
        'fields'           => '',
    );

    $centers = get_posts( $args );
    $data =array();
    $csv_file= BEAUTY_CENTER_PLUGIN_PATH.'sample-data/centers.csv';
    $json_file= BEAUTY_CENTER_PLUGIN_PATH.'sample-data/centers.json';
    $acfs=array('googlemap_link','doclib_link','name','phone','email','address','zipcode','transport','opening_hours','description');
    $header=array('title','name','googlemap_link','doclib_link','name','phone','email','address','zipcode','transport','opening_hours','description');
    file_put_contents( $csv_file,implode(";",$header), FILE_APPEND );
    foreach ($centers as $center){
        $implode= array();
        $id= $center->ID;
        $data[$id]["post_title"]= $center->post_title;
        $data[$id]["post_name"]= $center->post_name;

        $implode["post_title"]= $center->post_title;
        $implode["post_name"]= $center->post_name;
        $prefix ="beauty_center_";

        foreach ($acfs as $acf){
            $data[$id][$acf] = get_post_meta($id,$prefix.$acf,true);
            $implode[$acf]= get_post_meta($id,$prefix.$acf,true);
        }
        file_put_contents( $csv_file, implode(';',$implode), FILE_APPEND );
    }
    file_put_contents( $json_file, json_encode(array_values($data)) , FILE_APPEND );
}
function wp999_export_services(){
    $json_file =   $json_file= BEAUTY_CENTER_PLUGIN_PATH.'sample-data/services.json';
    $args_service = [
        'taxonomy' =>'beauty_center_service',
        'hide_empty' => false,
    ];
    $services  = get_terms( $args_service );

    foreach ($services as $service){
        $id= $service->term_id;
        $data[$id]["name"]= $service->name;
        $data[$id]["slug"]= $service->slug;

    }

    file_put_contents( $json_file, json_encode(array_values($data)) , FILE_APPEND );
}
function wp999_export_cities(){
    $json_file =   $json_file= BEAUTY_CENTER_PLUGIN_PATH.'sample-data/cities.json';
    $args_city = [
        'taxonomy' =>'beauty_center_city',
        'hide_empty' => false,
    ];
    $cities  = get_terms( $args_city );

    foreach ($cities as $city){
        $id= $city->term_id;
        $data[$id]["name"]= $city->name;
        $data[$id]["slug"]= $city->slug;

    }

    file_put_contents( $json_file, json_encode(array_values($data)) , FILE_APPEND );
}

function wp900_echo_ligne($message){

    echo "###############".$message." ##################"."\n";
}
wp999_import();


//wp999_export();


