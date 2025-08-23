jQuery( document ).ready( function(){
    if ( typeof ibnBreakingNews !== "undefined" ) {
        let $container = jQuery( '<div>', {
            id: 'ibn-public-news-container',
            class: 'ibn-public-news-container'
        } ).css( 'background-color', ibnBreakingNews.backgroundColor );

        let $link = jQuery( '<a>', {
            href: ibnBreakingNews.post.url,
            class: 'ibn-public-url'
        } ).css( 'color', ibnBreakingNews.textColor ).text( ibnBreakingNews.title + ': ' + ibnBreakingNews.post.title );

        $container.append( $link );

        let $firstHeader = jQuery( 'header' ).first();
        $container.insertAfter( $firstHeader );
    }
} );