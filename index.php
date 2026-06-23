<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article>
            <?php the_content(); ?>
        </article>
    <?php endwhile; endif; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
