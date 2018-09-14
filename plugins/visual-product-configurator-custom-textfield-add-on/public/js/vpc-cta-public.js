(function( $ ) {
	'use strict';

	$(document).ready(function () {
		if(typeof(vpc)!='undefined'){
			hide_colors();
			hide_fonts();
			vpc_load_custom_color_picker();
			create_vpc_text_container();
			setTimeout(function () {
				load_text();
			},2000);
		}

		function hide_colors() {
			setTimeout(function () {
				$('[id$="color-selector"]').each(function ()
				{
					if (typeof(vpc.show_colors) !== 'undefined' && (vpc.show_colors === "Yes" || vpc.show_colors === "")) {
						$(this).parents(".vpc-textfield-color").show();
					}else {
						$(this).parents(".vpc-textfield-color").hide();
					}
				});
			},2000);
		}

		function hide_fonts() {
			setTimeout(function () {
				$('[id$="font-selector"]').each(function ()
				{
					if (typeof(vpc.show_fonts) !== 'undefined' && (vpc.show_fonts === "Yes" || vpc.show_fonts === "")) {
						$(this).parents(".vpc-textfield-font").show();
					}else {
						$(this).parents(".vpc-textfield-font").hide();
					}
				});
			});
		}

		function vpc_load_custom_color_picker(){
			setTimeout(function () {
				$('[id$="color-selector"]').each(function ()
				{
					var id=$(this).attr('id');
					var field_id=$(this).data('field');
					var field_settings=vpc.text_settings[field_id];
					var field_datas=$.parseJSON(field_settings);
					$('#' + id).qtip({
						content: "<div class='wpc-custom-colors-container' data-id='" + id + "'>"+field_datas.palettes+"</div>",
						position: {
							my: 'middle left',
						},
						style: {
							tip: false,
							width: 200,
							classes: 'qtip-rounded qtip-light text-color',
						},
						show: 'click',
						hide:{
							event: 'unfocus'
						},
						events: {
							show: function() {
								// Tell the tip itself to not bubble up clicks on it
								$($(this).qtip('api').elements.tooltip).click(function() { return true; });

								// Tell the document itself when clicked to hide the tip and then unbind
								// the click event (the .one() method does the auto-unbinding after one time)
								$(document).one("click", function() { $('.vpc-action-buttons .btn_share').qtip('hide'); });
							}
						},
					});
				});
			},2000);
		}

		function create_vpc_text_container(){
			var result = "";
			setTimeout(function () {
				if (typeof(active_views) !== 'undefined') {
					var activeViews=JSON.parse(active_views);
				}
				$('[id$="-field"]').each(function ()
				{
					var id=$(this).attr('id');
					if(typeof(vpc.text_settings[id]) != 'undefined'){
						var field_settings=vpc.text_settings[id];
						var field_datas=$.parseJSON(field_settings);
						var angle="rotate("+field_datas.angle+"deg)";
						var multi_views = false;
						if (typeof(vpc.config) !== 'undefined') {
							$.each(vpc.config,function (config_key, config_value) {
								if (config_key === 'multi-views' && config_value === 'Yes') {
									multi_views = true;
								}
							})
						}
						if (multi_views) {
							if (typeof(activeViews) != 'undefined' && Array.isArray(activeViews)) {
								$.each(activeViews, function (index, value) {
									if (id.includes(value)) {
										setTimeout(function(){
											var preview_html='<div id="text_panel_'+index+'" ><div id="'+field_datas.container+'" class="text_field jtextfill" style="font-size:'+field_datas.size+'px;font-family:'+field_datas.font+';top:'+field_datas.top+'%;transform:'+angle+';left:'+field_datas.left+'%; position:absolute;"><span></span></div></div>';
											$('.bxslider').find("[data-view='"+index+"']").not(".bx-clone").append(preview_html);
										},1000);
									}
								});
							}
						}else {
							result+='<div id="'+field_datas.container+'" class="text_field jtextfill" style="font-size:'+field_datas.size+'px;font-family:'+field_datas.font+';top:'+field_datas.top+'%;transform:'+angle+';left:'+field_datas.left+'%;"><span></span></div>';
							$('#text_panel').html(result);
						}
					}
				});
			},2000);
		}

		function change_component_subtitle_class(that) {
			//Remove the subtitle of the component after loading
			$(that).parents('.vpc-component').find(".vpc-component-header .vpc-selected").addClass('vpc-texts')
			$(that).parents('.vpc-component').find(".vpc-component-header .vpc-texts").removeClass('vpc-selected')
			$(that).parents('.vpc-component').find(".vpc-component-header .vpc-texts").css({
				"color": "#becbd2",
				"display": "block",
				"font-size": "12px",
				"font-weight": "normal",
				"line-height": "normal",
			});
		}

		function set_component_text(that,text,current_field_id) {
			if (that.parents('.vpc-component').find(".vpc-texts").length === 0) {
				change_component_subtitle_class(that);
			}
			if (that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).length === 0) {
				var new_text_bloc = "<span class='"+current_field_id+"'></span>"
				if (that.parents('.vpc-component').find(".vpc-texts").text() === 'none') {
					that.parents('.vpc-component').find(".vpc-texts").html(new_text_bloc);
				}else {
					that.parents('.vpc-component').find(".vpc-texts").append(new_text_bloc);
				}
			}

			if (that.parents('.vpc-component').find(".vpc-texts").children().length === 1 || that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).index() === 0) {
				if (text === '') {
					that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).html('none');
				}else {
					that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).html(text);
				}
			}else {
				if (text === '') {
					that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).html(' - none');
				}else {
					that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).html(' - '+text);
				}
			}
			that.parents('.vpc-component').find(".vpc-texts ."+current_field_id).css({
				"display": "inline",
				"padding-right": "5px"
			});
		}

		function load_text(){
			$('[id$="-field"]').each(function ()
			{
				var text =$(this).val();
				var current_field_id=$(this).attr('id');
				set_component_text($(this),text,current_field_id);

				if ($(this).val().length > 0){
					var text =$(this).val();
					var current_field_id=$(this).attr('id');
					setTimeout(function(){
						add_text_on_preview(text,current_field_id);
					},1000);
				}
			});
		}

		//Vider le champs imput lorsque l'option ou le component de text est caché

		wp.hooks.addAction('vpc.hide_options_or_component',vpc_cta_hide_component_selector);

		function vpc_cta_hide_component_selector(rules_groups){
			if (rules_groups.result.scope == "option")
			{
				$('[id$="-field"]').each(function ()
				{
					var text_field_id = $(this).attr('id');
					var text_field_name = text_field_id.replace('-field','');
					var text_field = $('#'+text_field_id).parent().parent();
					if(rules_groups.result.apply_on==text_field.attr('data-oid')){
						$('#'+text_field_id).val('');
						$('#'+text_field_name+'-container span').html('');
					}
				}
			);
		}
		else if (rules_groups.result.scope == "component")
		{
			$('[id$="-field"]').each(function ()
			{
				var text_field_id = $(this).attr('id');
				var text_field_name = text_field_id.replace('-field','');
				var component_id = $(this).parent().parent().parent().parent().parent().attr('data-component_id');
				if(rules_groups.result.apply_on == component_id){
					$('#'+text_field_id).val('');
					$('#'+text_field_name+'-container span').html('');
				}

			}
		);
	}
}
$(document).on("keyup",'[id$="-field"]',function ()
{
	var text =$(this).val();
	var current_field_id=$(this).attr('id');
	set_component_text($(this),text,current_field_id);
	add_text_on_preview(text,current_field_id);
	window.vpc_build_preview();
});

wp.hooks.addFilter('vpc.total_price', update_total_price);

function update_total_price(price) {
	$('[id$="-field"]').each(function ()
	{
		if ($(this).val().length > 0) {
			var option_price = $(this).attr('data-price');
			if (vpc.views && $("#vpc-add-to-cart").attr("data-currency-rate") > 0) {
				price += parseFloat(option_price * $("#vpc-add-to-cart").attr("data-currency-rate"));
			}else {
				price += parseFloat(option_price);
			}
		}
	});
	return price;
}

function set_vpc_text_size(selector_id) {
	$('#' + selector_id).textfill({
		maxFontPixels: 150,
		minFontPixels: 13,
		debug: true,
		innerTag: 'span'
	});
}

function add_text_on_preview(text,field_id){
	var field_settings=vpc.text_settings[field_id];
	var field_datas=$.parseJSON(field_settings);
	$('#'+field_datas.container+' span').html('');
	if (text.length>0){
		var font = $("select#"+field_datas.font_selector_id+" option:selected").val();
		var color = $("#"+field_datas.color_selector_id).css('background-color');
		$('#'+field_datas.container+' span').html(text);
		$('#'+field_datas.container).css('font-family',font);
		$('#'+field_datas.container).css('color',color);
		$('#'+field_datas.container+' span').show();
	}
}

$(document).on("change", '.font-selector', function ()
{

	change_component_subtitle_class(this);
	var current_font=$(this).val();
	var field_id=$(this).data('field');
	var field_settings=vpc.text_settings[field_id];
	var field_datas=$.parseJSON(field_settings);
	var hidden_id=field_datas.hidden_field_id;
	var color=$('#'+field_datas.color_selector_id).data('color');
	$('#'+field_datas.container).css('font-family',current_font);
	get_text_properties(current_font,color,hidden_id);
});

$(document).on("click", '.vpc-custom-color', function ()
{
	var selector =$(this).data('selector');
	var color =$(this).data('color');
	var field_settings=vpc.text_settings[selector];
	var field_datas=$.parseJSON(field_settings);
	if($('#'+selector).hasClass('monogram_text')){
		var monogram_class=field_datas.option_id+'-monogram';
		var font=$('.'+monogram_class).find('.active').data('font');
		font=get_font_family(font,selector);
	}
	else
	var font=$('#'+field_datas.font_selector_id).val();
	var hidden_id=field_datas.hidden_field_id;
	$('#'+field_datas.color_selector_id).css('background-color',color);
	$('#'+field_datas.container).css('color',color);
	get_text_properties(font,color,hidden_id);
});

function get_text_properties(font,color,hidden_id){
	var properties="font-family:"+font+" <br> color :"+color;
	$('#'+hidden_id).val(properties);
}

wp.hooks.addAction('vpc.ajax_loading_complete',function() {
	create_vpc_text_container();
	hide_colors();
	hide_fonts();
	vpc_load_custom_color_picker();
	setTimeout(function () {
		load_text();
	},2000);
});

/*remove component text selected image icon*/
$('.vpc-single-option-wrap.textfield').parents('.vpc-component').find('span.vpc-selected-icon').hide();

});

})( jQuery );
