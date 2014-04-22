(function ($) {
	"use strict";
	$(function () {
		// Place your administration-specific JavaScript here

		function replaceIdAndName($input, lastIndex) {
			var name = $input.attr('name');

			$input.attr('id', $input.attr('id')+'-'+lastIndex);

			//'widget-extra-widget-links-icon[2][title]'
			// widget-extra-widget-links-icon[2][link_title][1]


			var namePattern = /[\[]([^\]]*)[\]]/g,
				nameMatches = name.match(namePattern),
				shortName = nameMatches[nameMatches.length-1];

			shortName = shortName.substr(1, shortName.length-2);
			name = name.replace(shortName, (shortName+'('+lastIndex+')'));

			$input.attr('name', name);
		}

		function enableDrag() {
			$('.extra-widget-links-icon-list').sortable({
				forcePlaceholderSize: true,
				placeholder: 'extra-widget-links-icon-list-placeholder',
				opacity: 1,
				handle: '.extra-widget-links-icon-list-handle'
			});
		}


		$(document).on('click', '.extra-widget-links-icon-add-button', function (event) {
			event.preventDefault();
			var $button = $(this),
				$container = $button.parents('.extra-widget-links-icon-container'),
				$template = $container.find('.extra-widget-links-icon-template'),
				$list = $container.find('.extra-widget-links-icon-list'),
				lastIndex = $container.data('last-index'),
				$clone = $template.clone();

			lastIndex = parseInt(lastIndex)+1;
			$container.data('last-index', lastIndex);

			$clone.find('label').each(function () {
				var $label = $(this);
				$label.attr('for', $label.attr('for')+'-'+lastIndex);
			});
			$clone.find('input').each(function () {
				replaceIdAndName($(this), lastIndex);
			});
			$clone.find('select').each(function () {
				replaceIdAndName($(this), lastIndex);
			});

			$clone.removeClass('extra-widget-links-icon-template').appendTo($list);
		});

		$(document).on('click', '.extra-widget-links-icon-remove-button', function (event) {
			event.preventDefault();

			if (confirm("Êtes vous sûr de vouloir supprimer ce lien ?")) {
				$(this).parents('.extra-widget-links-icon-item').remove();
			}
		});

		$(document).ajaxSuccess(function(e, xhr, settings) {
			var widget_id_base = 'my-widget-id-base';
			if(settings.data && settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=extra-widget-links-icon') != -1) {
				//do something
				enableDrag();
			}
		});

		enableDrag();
	});
}(jQuery));