<?php 
    global $post;
    $post_info = $this->ibn_get_post_info( $post->ID );

    // In case the User is not logged don't show the Dashboard
    if ( !$post_info ) { exit; }
    else if ( !isset( $post_info->success ) ) {
        die( "ERROR: Post ID is invalid!" );
    }

    // Prepare Days Select
    $days_select = '<select id="ibn-expiration-day" name="ibn-expiration-day">';
    for ( $count_day = 1; $count_day <= 31; $count_day++ ) {
        $count_day = $count_day < 10 ? "0". $count_day : $count_day;
        $is_selected = $post_info->expiration_date !== false && $post_info->expiration_date->day == $count_day ? "selected='selected'" : "";
        $days_select .= '<option value="'. $count_day .'" '. $is_selected .'>'. $count_day .'</option>';
    }
    $days_select .= '</select>';

    // Prepare Months Select
    $months_select = '<select id="ibn-expiration-month" name="ibn-expiration-month">';
    for ( $count_month = 1; $count_month <= 12; $count_month++ ) {
        $count_month = $count_month < 10 ? "0". $count_month : $count_month;
        $is_selected = $post_info->expiration_date !== false && $post_info->expiration_date->month == $count_month ? "selected='selected'" : "";
        $months_select .= '<option value="'. $count_month .'" '. $is_selected .'>'. $count_month .'</option>';
    }
    $months_select .= '</select>';

    // Prepare Years Select
    $years_select = '<select id="ibn-expiration-year" name="ibn-expiration-year">';
    for ( $count_year = date( "Y" ); $count_year <= date( "Y" ) + 5; $count_year++ ) {
        $is_selected = $post_info->expiration_date !== false && $post_info->expiration_date->year == $count_year ? "selected='selected'" : "";
        $years_select .= '<option value="'. $count_year .'" '. $is_selected .'>'. $count_year .'</option>';
    }
    $years_select .= '</select>';

    // Prepare Hours Select
    $hours_select = '<select id="ibn-expiration-hour" name="ibn-expiration-hour">';
    for ( $count_hour = 0; $count_hour < 24; $count_hour++ ) {  
        $count_hour = $count_hour < 10 ? "0". $count_hour : $count_hour;      
        $is_selected = $post_info->expiration_date !== false && $post_info->expiration_date->hour == $count_hour ? "selected='selected'" : "";
        $hours_select .= '<option value="'. $count_hour .'" '. $is_selected .'>'. $count_hour .'</option>';
    }
    $hours_select .= '</select>';

    // Prepare Minutes Select
    $minutes_select = '<select id="ibn-expiration-minute" name="ibn-expiration-minute">';
    for ( $count_minute = 0; $count_minute <= 59; $count_minute++ ) {  
        $count_minute = $count_minute < 10 ? "0". $count_minute : $count_minute;
        $is_selected = $post_info->expiration_date !== false && $post_info->expiration_date->minute == $count_minute ? "selected='selected'" : "";
        $minutes_select .= '<option value="'. $count_minute .'" '. $is_selected .'>'. $count_minute .'</option>';
    }
    $minutes_select .= '</select>';

    // Print the Metabox content
    echo '
    <script type="text/javascript">
    var ibnIsPinned = JSON.parse( "'. json_encode( $post_info->is_pinned ) .'" );
    </script>
    <div id="ibn-metabox-container" class="ibn-container no-wrap">
        <div class="ibn-row-container">
            <input type="checkbox" id="ibn-pin-post" name="ibn-pin-post" data-post-id="'. $post->ID .'" '. ( $post_info->is_pinned ? "checked='checked'" : "" ) .' />
            <label for="ibn-pin-post" class="label-right">'. __( "Make this post breaking news", "textdomain" ). '</label>
        </div>
        <div class="ibn-row-container">
            <label for="ibn-pin-post-title">'. __( "Custom breaking news title:", "textdomain" ). '</label>
            <input type="text" id="ibn-pin-post-title" name="ibn-pin-post-title" value="'. $post_info->breaking_title .'" />
        </div>
        <div class="ibn-row-container wrap">
            <input type="checkbox" id="ibn-pin-post-expiration" name="ibn-pin-post-expiration" '. ( $post_info->expiration_date !== false ? "checked='checked'" : "" ) .' />
            <label for="ibn-pin-post-expiration" class="label-right">'. __( "Set an expiration Date & Time", "textdomain" ). '</label>
            <div id="ibn-expiration-setup-container" class="ibn-expiration-setup-container '. ( $post_info->expiration_date !== false ? "" : "hidden" ) .'">
                '. $days_select .'
                <span>.</span>
                '. $months_select .'
                <span>.</span>
                '. $years_select .'
                <span class="ibn-separator"></span>
                '. $hours_select .'
                <span>:</span>
                '. $minutes_select .'
            </div>
        </div>
    </div>
    ';
?>