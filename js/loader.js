jQuery(function ($){
	$(document).ajaxStop(function(){
        $('body').css({"overflow":"auto"});
		$("#ajax_loader").fadeOut();
	 });
    
	 $(document).ajaxStart(function(){ 
         $('body').css({"overflow":"hidden"});
		 $("#ajax_loader").fadeIn();
         var x = Math.floor((Math.random() * 5) + 1);
         var attrt="img/gloader"+x+".svg";
        $("#ajax_loader img").attr("src",attrt);
	 });    
});    
