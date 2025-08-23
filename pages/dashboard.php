<?php
$settings = $this->ibn_get_settings();
if ( ! $settings ) { exit; }
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__( 'Instant Breaking News Settings', 'textdomain' ); ?></h1>
    <form id="ibn-settings-form" method="post">
        <?php wp_nonce_field( 'ibn_settings', '_wpnonce' ); ?>
        <div id="ibn-dashboard-container" class="ibn-container">
            <div class="ibn-row-container">
                <label for="ibn-banner-title"><?php echo esc_html__( 'Banner Title:', 'textdomain' ); ?></label>
                <input type="text" placeholder="<?php echo esc_attr__( 'e.g. BREAKING NEWS', 'textdomain' ); ?>" id="ibn-banner-title" value="<?php echo esc_attr( $settings->title ); ?>" />
            </div>
            <div class="ibn-row-container">
                <label for="ibn-background-color"><?php echo esc_html__( 'Background Color:', 'textdomain' ); ?></label>
                <input type="text" id="ibn-background-color" class="ibn-hidden-on-start" data-default-color="<?php echo esc_attr( $settings->background_color ); ?>" value="<?php echo esc_attr( $settings->background_color ); ?>" />
            </div>
            <div class="ibn-row-container">
                <label for="ibn-text-color"><?php echo esc_html__( 'Text Color:', 'textdomain' ); ?></label>
                <input type="text" id="ibn-text-color" class="ibn-hidden-on-start" data-default-color="<?php echo esc_attr( $settings->text_color ); ?>" value="<?php echo esc_attr( $settings->text_color ); ?>" />
            </div>
            <div class="ibn-row-container">
                <span class="ibn-picked-post"><?php echo esc_html__( 'Selected Post:', 'textdomain' ); ?></span>
                <?php if ( 0 === $settings->pinned_post->id ) { ?>
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=post' ) ); ?>" class="ibn-selected-post" target="_blank"><?php echo esc_html__( 'Post is not selected yet!', 'textdomain' ); ?></a>
                <?php } else { ?>
                    <a href="<?php echo esc_url( $settings->pinned_post->edit_url ); ?>" class="ibn-selected-post" target="_blank"><?php echo esc_html( $settings->pinned_post->title ); ?></a>
                <?php } ?>
            </div>
            <div class="ibn-row-container right">
                <button id="ibn-save-settings" class="button button-primary"><?php echo esc_html__( 'Save', 'textdomain' ); ?></button>
            </div>
        </div>
    </form>
</div>