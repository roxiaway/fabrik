/**
 * Tags Element
 *
 * @copyright: Copyright (C) 2005-2015, fabrikar.com - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

var FbTags = new Class({

	options: {
		'rowid': '',
		'id': 0
	},

	Extends: FbElement,
	initialize: function (element, options) {
		this.parent(element, options);
		if (this.options.editable) {
			this.setUp();
		}
	},

	setUp: function () {
		
		jQuery('#' + this.options.element).chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});
		
		this.sel = jQuery('#' + this.options.element).ajaxChosen({
			type: 'GET',
			url: Fabrik.liveSite + 'index.php?option=com_tags&task=tags.searchAjax',
			dataType: 'json',
			jsonTermKey: 'like',
			afterTypeDelay: '500',
			minTermLength: '3'
		}, function (data) {
			
			var results = [];

			jQuery.each(data, function (i, val) {
				results.push({ value: val.value, text: val.text });
			});
			return results;
		});
		
		var sel = this.sel;
		this.sel.on('change', function (e) {
			var opts = jQuery(sel).find('option');
			jQuery(sel.data().chosen.results_data).each(function () {
				jQuery(opts[this.options_index]).attr('selected', this.selected);
			});
		});
		
		this.watchNew();
	},
	
	watchNew: function () {
		// Method to add tags pressing enter
		var customTagPrefix = '#fabrik#',
		container = jQuery(this.getContainer()),
		el = this.options.element,
		tagOption,
		field = container.find('.search-field input');
		
		field.keydown(function (event) {

			// Tag is greater than 3 chars and enter pressed
			if (this.value.length >= 3 && (event.which === 13 || event.which === 188)) {

				// Search an highlighted result
				var highlighted = container.find('li.active-result.highlighted').first();

				// Add the highlighted option
				if (event.which === 13 && highlighted.text() !== '')
				{
					// Extra check. If we have added a custom tag with this text remove it
					var customOptionValue = customTagPrefix + highlighted.text();
					container.find('option').filter(function () {
						return jQuery(this).val() === customOptionValue;
					}).remove();

					// Select the highlighted result
					tagOption = container.find('option').filter(function () {
						return jQuery(this).html() === highlighted.text();
					});
					tagOption.attr('selected', 'selected');
				}
				// Add the custom tag option
				else
				{
					var customTag = this.value;

					// Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
					tagOption = container.find('option').filter(function () {
						return jQuery(this).html() === customTag; 
					});
					if (tagOption.text() !== '')
					{
						tagOption.attr('selected', 'selected');
					}
					else
					{
						var option = jQuery('<option>');
						option.text(this.value).val(customTagPrefix + this.value);
						option.attr('selected', 'selected');

						// Append the option an repopulate the chosen field
						container.find('select').append(option);
					}
				}

				this.value = '';
				jQuery('#' + el).trigger('liszt:updated');
				event.preventDefault();
			}
		});
	},

	cloned: function (c) {
		Fabrik.fireEvent('fabrik.tags.update', this);
		this.parent(c);
		this.setUp();
	}
});