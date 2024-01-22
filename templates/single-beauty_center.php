<?php get_header(); ?>
<div class="entry-content">
        <div class="beauty_center_container">
            <div class="beauty_center_header">
                <?php if ( has_post_thumbnail() ) {
                    the_post_thumbnail();
                 }  ?>
            </div>
            <div class="beauty_center_body">

            </div>
        </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>


