/**
 * @file   modules/multidomain/tpl/js/common.js
 * @author largeden (cbrghost@gmail.com)
 * @brief  multidomain javascript
 **/

var multidomain = xe.createApp('multidomainJS',
	{
		init : function()
		{
			jQuery('html').ready(function()
			{
				jQuery('a._multidomain_insert')
					.bind('before-open.mw', function()
					{
						jQuery('#multidomain_insert').find('input[name=multidomain_srl]').val('');
						jQuery('#multidomain_insert').find('input[name=domain]').val('');
						jQuery('#multidomain_insert').find('input[name=index_module_srl]').val('');
						jQuery('#multidomain_insert').find('input[readonly]').val('');
					});

				jQuery('a._multidomain_update')
					.bind('before-open.mw', function()
					{
						var data = $multidomain.multidomainData(this);

						jQuery('#multidomain_insert').find('input[name=multidomain_srl]').val(data['multidomain_srl']);
						jQuery('#multidomain_insert').find('input[name=domain]').val(data['domain']);
						jQuery('#multidomain_insert').find('input[name=index_module_srl]').val(data['module_srl']);
						jQuery('#multidomain_insert').find('input[readonly]').val(data['mid']);
					});

				jQuery('a._multidomain_delete')
					.bind('before-open.mw', function()
					{
						var data = $multidomain.multidomainData(this);

						jQuery('#multidomain_delete').find('input[name=multidomain_srl]').val(data['multidomain_srl']);
						jQuery('#multidomain_delete').find('.domain').text(data['domain']);
						jQuery('#multidomain_delete').find('.module').text(data['mid']);
					});
			});
		},
		multidomainData : function(val)
		{
			var params = new Array();
			params['multidomain_srl'] = jQuery(val).parent().parent().attr('id');
			params['multidomain_srl'] = params['multidomain_srl'].split('_');
			params['multidomain_srl'] = params['multidomain_srl'][2];
			params['module_srl'] = jQuery(val).parent().parent().find('td[id*="module_srl_"]').attr('id');
			params['module_srl'] = params['module_srl'].split('_');
			params['module_srl'] = params['module_srl'][2];
			params['domain'] = jQuery(val).parent().parent().find('.domain').text();
			params['mid'] = jQuery(val).parent().parent().find('td[id*="module_srl_"]').text();

			return params;
		}
	});

// App 객체의 인스턴스 생성
var $multidomain = new multidomain();

function md_complete(ret_obj) {
	var message = ret_obj['message'];

	alert(message);

	jQuery('section._type_alert').find("._ok").click(function(){
		location.reload();
	});
}