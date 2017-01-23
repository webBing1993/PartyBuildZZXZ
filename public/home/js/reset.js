/**
 * Created by rawraw on 2017/1/22.
 */
function tabSwitch(a,b){
	$(a).off('click').on('click',function(){
		var this_ = this ;
		var box = $(b ).parent();
		var index = $(this_ ).index();
		var ww = $(b ).parent().width();
		$(this_ ).addClass('active' );
		$(this_ ).siblings(a).removeClass('active');
		$(b).removeClass('hidden');
		ww = ww * index;
		box.stop().animate({left: -ww +'px'},300,function(){
			$(b).eq(index).siblings(b).addClass('hidden');
			setCookie( 'tab', index )
		});
	})
}
function tabRecord(a,b){
	var tab = getCookie('tab');
	if(tab){
		var index = tab;
		var box = $(b).parent();
		var ww = $(b).parent().width();
		$(a).eq(index).addClass('active');
		$(a).eq(index).siblings(a).removeClass('active');
		$(b).removeClass('hidden');
		ww = ww * index;
		box.css({left: -ww +'px'});
		setTimeout(function(){
			$(b).eq(index).siblings(b).addClass('hidden');
		},100)
	}
	//清除tab值
	pushHistory();
	window.addEventListener( "popstate", function( e ){
		delCookie( 'tab' );
		window.history.go( -1 );
	}, false );
}
function pushHistory(){
	var sta = {
		title: "title"
	};
	if( window.history.state === null ){
		window.history.pushState( sta, "title" );
	}
}
function setCookie( name, value ){
	var Days = 30;
	var exp = new Date();
	exp.setTime( exp.getTime() + Days * 24 * 60 * 60 * 1000 );
	document.cookie = name + "=" + value + ";expires=" + exp.toGMTString();
}
function getCookie( name ){
	var arr, reg = new RegExp( "(^| )" + name + "=([^;]*)(;|$)" );
	if( arr = document.cookie.match( reg ) )
		return arr[ 2 ];
	else
		return null;
}
function delCookie( name ){
	var exp = new Date();
	exp.setTime( exp.getTime() - 1 );
	var cval = getCookie( name );
	if( cval != null )
		document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}