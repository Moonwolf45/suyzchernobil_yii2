(function () {
    'use strict';

    let contentWayPoint = function() {
        let i = 0;

        $('.animate-box').waypoint( function(direction) {
            if ( direction === 'down' && !$(this.element).hasClass('animated-fast')) {
                i++;
                $(this.element).addClass('item-animate');
                setTimeout(function() {
                    $('body .animate-box.item-animate').each(function(k) {
                        let el = $(this);

                        setTimeout( function () {
                            let effect = el.data('animate-effect');
                            if (effect === 'fadeIn') {
                                el.addClass('fadeIn animated-fast');
                            } else if (effect === 'fadeInLeft') {
                                el.addClass('fadeInLeft animated-fast');
                            } else if (effect === 'fadeInRight') {
                                el.addClass('fadeInRight animated-fast');
                            } else {
                                el.addClass('fadeInUp animated-fast');
                            }

                            el.removeClass('item-animate');
                        }, k * 50, 'easeInOutExpo');
                    });
                }, 100);
            }
        }, { offset: '85%' });
    };

	let goToTop = function() {
		$('.js-goTop').on('click', function(event) {
			event.preventDefault();

			$('html, body').animate({
				scrollTop: $('html').offset().top
			}, 500, 'swing');
			
			return false;
		});

		$(window).scroll(function() {
			let $win = $(window);
			if ($win.scrollTop() > 200) {
				$('.js-top').addClass('active');
			} else {
				$('.js-top').removeClass('active');
			}
		});
	};
	
	$(function() {
        contentWayPoint();
		goToTop();
	});
}());