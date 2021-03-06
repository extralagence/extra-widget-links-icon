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
	<?php
		$link_url = isset($link['link_url']) ? $link['link_url'] : '#';
		$link_title = isset($link['link_title']) ? $link['link_title'] : '';
		$link_icon = isset($link['link_icon']) ? $link['link_icon'] : '';
	?>
	<a href="<?php echo $link_url; ?>" title="<?php echo $link_title; ?>" target="_blank"><span class="icon-link <?php echo $link_icon; ?>"></span><?php echo $link_title; ?></a>
<?php endforeach; ?>                                   