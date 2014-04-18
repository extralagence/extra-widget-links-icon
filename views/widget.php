<!-- This file is used to markup the public-facing widget. -->
<?php
extract( $args );
// these are the widget options
$title = apply_filters('widget_title', $instance['title']);

// Display the widget
// Check if title is set
if ( $title ) {
	echo $before_title . $title . $after_title;
}

$links = $instance['links'];
?>

<?php foreach($links as $link) : ?>
	<a href="<?php echo $link['link_url']; ?>"><span class="icon-link <?php echo $link['link_icon']; ?>"></span><?php echo $link['link_title']; ?></a>
<?php endforeach; ?>