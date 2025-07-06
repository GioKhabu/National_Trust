// JavaScript Document

$('.autoClose').click(function(){
	cl=$(this).hasClass('closed');
	table=$(this).closest('table');
	console.log(cl);
	console.log(this);
	if(cl){
		$(table).find('tr').show();
		$(this).removeClass('closed');	
		}else{
		$(table).find('tr:not(.autoClose)').hide();
		$(this).addClass('closed');
		}
})