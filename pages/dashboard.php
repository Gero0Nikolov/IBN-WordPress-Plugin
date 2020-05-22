<?php 
// Get Saved Settings
$settings = $this->ibn_get_settings();

// In case the User is not logged don't show the Dashboard
if ( !$settings ) { exit; }
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __( "Instant Breaking News Settings", "textdomain" ); ?></h1>
    <div id="ibn-dashboard-container" class="ibn-container">
        <div class="ibn-row-container">
            <label for="ibn-banner-title"><?php echo __( "Banner Title:", "textdomain" ); ?></label>
            <input type="text" placeholder="e.g. BREAKING NEWS" id="ibn-banner-title" value="<?php echo $settings->title; ?>" />
        </div>
        <div class="ibn-row-container">
            <label for="ibn-background-color"><?php echo __( "Background Color:", "textdomain" ); ?></label>
            <input type="text" id="ibn-background-color" class="ibn-hidden-on-start" data-default-color="<?php echo $settings->background_color; ?>" value="<?php echo $settings->background_color; ?>" />
        </div>
        <div class="ibn-row-container">
            <label for="ibn-text-color"><?php echo __( "Text Color:", "textdomain" ); ?></label>
            <input type="text" id="ibn-text-color" class="ibn-hidden-on-start" data-default-color="<?php echo $settings->text_color; ?>" value="<?php echo $settings->text_color; ?>" />
        </div>
        <div class="ibn-row-container">
            <span class="ibn-picked-post"><?php echo __( "Selected Post:", "textdomain" ); ?></span>
            <?php if ( $settings->pinned_post->id == 0 ) { ?>
                <a href="<?php echo admin_url( "edit.php?post_type=post" ); ?>" class="ibn-selected-post" target="_blank"><?php echo __( "Post is not selected yet!", "textdomain" ); ?></a>
            <?php } else { ?>
                <a href="<?php echo $settings->pinned_post->edit_url; ?>" class="ibn-selected-post" target="_blank"><?php echo $settings->pinned_post->title; ?></a>
            <?php } ?>
        </div>
        <div class="ibn-row-container right">
            <button id="ibn-save-settings" class="button button-primary"><?php echo __( "Save", "textdomain" ); ?></button>
        </div>
    </div>
</div>