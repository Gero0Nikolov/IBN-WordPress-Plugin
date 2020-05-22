jQuery( document ).ready( function(){
    if ( typeof ibnBreakingNews !== "undefined"  ) {
        let container = "\
        <div id='ibn-public-news-container' class='ibn-public-news-container' style='background-color: "+ ibnBreakingNews.backgroundColor +";'>\
            <a href='"+ ibnBreakingNews.post.url +"' class='ibn-public-url' style='color: "+ ibnBreakingNews.textColor +";'>\
                "+ ibnBreakingNews.title +": \
                "+ ibnBreakingNews.post.title +"\
            </a>\
        </div>\
        ";

        let $firstHeader = jQuery( "header" ).first();
        jQuery( container ).insertAfter( $firstHeader );
    }
} );