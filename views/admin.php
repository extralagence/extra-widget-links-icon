<!-- This file is used to markup the administration form of the widget. -->
<?php
	$links = array();
	if (array_key_exists('links', $instance)) {
		$links = $instance['links'];
	}
?>

<div class="extra-widget-links-icon-container" data-last-index="<?php echo count($links); ?>">
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Titre :", "extra-widget-links-icon"); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
	</p>

	<ul class="extra-widget-links-icon-list">
		<?php foreach ($links as $link_id => $link) : ?>
			<li>
				<?php $this->admin_item_template($link_id, $link); ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<p>
		<button class="button extra-widget-links-icon-add-button"><?php _e("Ajouter un lien", "extra"); ?></button>
	</p>

	<?php
	$this->admin_item_template();
	?>
</div>
