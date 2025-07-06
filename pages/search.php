<style>
	.NewsHeader {
		font-size: 24px;
		text-align: center;
		margin-bottom: 35px;
		padding-bottom: 20px;
		border-bottom: 3px dotted #0002;
	}
	.container.Search {
		margin-top: 50px;
        font-size: 14px;
	}
	hr{
		clear: both;
	}
	.recent_post li {
		margin-bottom: 10px;
		padding-bottom: 10px;
		border-bottom: 1px dotted #0002;
	}
	.sidebar {
		background-color: #0301;
		padding: 10px;
	}
	.recent_post li {
		margin-left: -20px;
	}
	
	

</style>
<div class="container Search">

    <div class="row">

	

		
		


<section class="col-md-12 col-sm-12">
	<div class="row">
		<div id="content"></div>
    </div><!-- end col-right-->
</section> <!-- end section-->
<script>
function Paging(Count, RowsPerPage, url='') { 
	
	if(url=='') url=window.location.pathname;
	
	url=url.split('/');
	
	var lang=url[1];
	var action=url[2];
	var st=parseInt('0'+url[3]);
	var param=url[4];
	
	
	var PagesCount=Math.floor(Count/RowsPerPage);
	if(PagesCount*RowsPerPage<Count) PagesCount++;
	
	var CurPage=Math.floor(st/RowsPerPage);
	if(CurPage*RowsPerPage<st) CurPage++;
	
	var ppCount=10;
	var p1=Math.floor(CurPage/ppCount)*ppCount+1;
	var p2=p1+ppCount-1;
	if(p2>PagesCount) p2=PagesCount;
	
	var rec='<div ><ul class="pagination">';
	if(p1>1) rec=rec+ '<li><a class="prev-next prev" href="/'+lang+'/'+action+'/'+((p1-2)*RowsPerPage)+'/'+param+'"><i class="ion-ios-arrow-left"></i> &larr; </a>';
	for(var i=p1; i<=p2; i++)
		rec=rec+ '<li ><a class="'+((CurPage+1)==i?'active':'')+'" href="/'+lang+'/'+action+'/'+((i-1)*RowsPerPage)+'/'+param+'">'+i+'</a></li>';
			
	if(p2<PagesCount) rec=rec+ '<li><a class="prev-next next" href="/'+lang+'/'+action+'/'+((p2)*RowsPerPage)+'/'+param+'"> &rarr; </a></li>';
	rec=rec+ '</ul></div>';	
	return rec;
	}	
</script>
		
	<div align="center" id="pagingContent"></div>
		
</div><!-- end row--> 
</div> <!-- end container-->

<style>
	.container.Search .srcItm img {
		max-width0: 50px;
		max-height: 85px;
		float: left;
		margin-right: 10px;
		background-color: #0008;
	}
	.srcItm{margin-bottom: 10px;	padding-bottom: 10px;	border-bottom: 1px dotted #0002;}
	.srcTitle {    font-size: 18px;}
</style>
		
<script>
  function hndlr(response) {
	  console.log(response);
      if(response.searchInformation){
	  if(response.searchInformation.totalResults==0){
		  $("#content").append('<div align="center" style="margin:100px 0 100px ">	<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #aabbff; height:60px; margin:10px;" align="center" class="normal"><tr>	<td align="center" valign="middle"><div style="width:70px; height:70px; background:url(/img/error.png) center right no-repeat;"></div></td>	<td valign="middle" align="center" style="padding:20px;"><?=_Interface('ასეთი ფრაზა საიტზე არ მოიძებნა')?></td></tr></table></div>');
	  }
	  else
	  for (var i = 0; i < response.items.length; i++) {
		var item = response.items[i];
		var img='';
		if(item.pagemap)
			if(item.pagemap.cse_image){
				img=item.pagemap.cse_image[0].src;
				if(img.substr(0,5)!='x-raw')
					img='<img src="'+img+'">';
					else img='';
				}
		// Make sure HTML in item.htmlTitle is escaped.
		$("#content").append('<div class="srcItm srcItem'+i+'"></div>')
		$(".srcItem"+i).append(img);
		$(".srcItem"+i).append('<div class="srcLink">'+item.link+'</div>');
		$(".srcItem"+i).append('<div class="srcTitle"><a href="'+item.link+'">'+item.htmlTitle+'</a></div>' );
		if(item.htmlSnippet)
			$(".srcItem"+i).append('<div class="srcSnippet">'+item.htmlSnippet+'</div>');
	  	}
	if(response.searchInformation && response.searchInformation.totalResults){
		var Count=response.searchInformation.totalResults;
		var RowsPerPage=10;
		var pagingHtml=Paging(Count, RowsPerPage);
		$('#pagingContent').html(pagingHtml);
		}
      } else $("#content").html(JSON.stringify(response));
	}
</script>
<?
require_once 'conf.php'; 
$st = (int)($page ?? 0);
$rowsPerPage = 10;
?>
<script src="https://www.googleapis.com/customsearch/v1/?key=<?= CUSTOM_SEARCH_API_KEY ?>&cx=<?= CUSTOM_SEARCH_CX ?>&hl=<?= $Lang == 'ge' ? 'ka' : 'en' ?>&q=<?= urlencode($param1) ?>&num=<?= $rowsPerPage ?>&start=<?= $st + 1 ?>&callback=hndlr"></script>






