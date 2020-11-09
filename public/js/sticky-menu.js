//kiedy dokument jest gotowy, wczytany
$(document).ready(function(){
	
	//odczytamy top diva nawigacji
	
	var NavY=$('.nawigacja').offset().top;
	
	//funkcja porownująca
	
	var stickyNav = function(){
		//top scrollbara
		var scrollY=$(window).scrollTop();
		
		if(scrollY>NavY){
			//dodajemy do diva nawigacja klase sticky
			$('.nawigacja').addClass('sticky');
		}
		else{
			//usuwamy z diva nawigacja klase sticky
			$('.nawigacja').removeClass('sticky');
		}
	};
	//wywolujemy funkcje stickyNav
	stickyNav();
	
	//jesli bedzie scrolowanie to wywoluj funkcje 
	$(window).scroll(function(){
		stickyNav();
	});
});