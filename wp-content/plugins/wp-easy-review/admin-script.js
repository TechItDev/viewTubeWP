jQuery(document).ready(function($) {
	"use strict";
	
	$(document).on("click", ".addfield", function(e) {
				
		var stringInput="";
		stringInput += "											<tr class=\"review_criterias_item\">";
		stringInput += "												<td scope=\"row\">";
		stringInput += "													<label for=\"review_criterias_name\">"+wp_easy_review_js_vars.name+"<\/label>";
		stringInput += "												<\/td>";
		stringInput += "												<td>";
		stringInput += "													<input name=\""+wp_easy_review_js_vars.form+"[review][review_criterias][name][]\" type=\"text\" id=\"review_criterias_name\" class=\"regular-text\">";
		stringInput += "												<\/td>";
		stringInput += "												<td scope=\"row\"><label for=\"review_criterias_score\">"+wp_easy_review_js_vars.score+"<\/label><\/td>";
		stringInput += "												<td>";
		stringInput += "													<input name=\""+wp_easy_review_js_vars.form+"[review][review_criterias][score][]\" type=\"text\" id=\"review_criterias_score\" class=\"regular-text\">";
		stringInput += "											<\/td>";
		stringInput += "												<td>";
		stringInput += "													<input class=\"button addfield\" type=\"button\" name=\"addfield\" value=\""+wp_easy_review_js_vars.addtext+"\">";
		stringInput += "													<input class=\"button deletefield\" type=\"button\" name=\"deletefield\" value=\""+wp_easy_review_js_vars.deletetext+"\">";
		stringInput += "												<\/td>";
		stringInput += "											<\/tr>";

		$('.review_criterias_body').append( stringInput );

	});
	
	$(document).on("click", ".deletefield", function(e) {
		var rowCount = $('#wp_easy_review_item_table >tbody >tr').length - 1;
		
		if( rowCount == 1 ){
			return;
		}
		
		var $currentrow = $(this).parents(".review_criterias_item");
		$currentrow.remove();
	});
});