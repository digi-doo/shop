jQuery(document).ready(function($) {
    if ($('.bottom-nav__pagination').length && !$('.bottom-nav__pagination ul li:last-child').hasClass('disabled')) {
        
        var $container = $('#products');

        $container.infiniteScroll({
            path: '.bottom-nav__pagination a[rel=\'next\']',
            append: '.product__box__wrapper',
            checkLastPage: true,
            prefill: false,
            responseType: 'document',
            outlayer: false,
            scrollThreshold: false,
            elementScroll: false,
            loadOnScroll: false,
            history: 'push',
            historyTitle: true,
            hideNav: false,
            status: false,
            button: '.load-next-button',
            onInit: function() {
                $('.load-next-button__wrapper').removeClass('d-none');
            },
            debug: false,
        });

        $container.on('load.infiniteScroll', function() {
            redrawPaginator($container);
            $('.load-next-button').removeClass('running');
        });

        $container.on('last.infiniteScroll', function(event, response, path) {
            $('.load-next-button__wrapper').addClass('d-none');
        });

        $container.on('error.infiniteScroll', function(event, response, path) {
            $('.load-next-button__wrapper').addClass('d-none');
        });
    }

    function getUrl(index) {
        return window.location.href.split('?')[0] + '?page=' + (index);
    }

    function linkLi(index) {
        return '<li><a href="' + getUrl(index) + '">' + index + '</a></li>';
    }

    function currentLi(index) {
        return '<li class="current"><a>' + index + '</a></li>';
    }

    function pagination(c, m) {
        var current = c,
            last = m,
            delta = 2,
            left = current - delta,
            right = current + delta + 1,
            range = [],
            rangeWithDots = [],
            l;

        for (let i = 1; i <= last; i++) {
            if (i == 1 || i == last || i >= left && i < right) {
                range.push(i);
            }
        }

        for (let i of range) {
            if (l) {
                if (i - l === 2) {
                    rangeWithDots.push(linkLi(l + 1));
                } else if (i - l !== 1) {
                    rangeWithDots.push('<li class="dots"><a>...</a></li>');
                }
            }
            if (i == c) {
                rangeWithDots.push(currentLi(i));                
            } else {
                rangeWithDots.push(linkLi(i));                
            }
            l = i;
        }

        return rangeWithDots;
    }

    function redrawPaginator($container) {
        $wrapper = $('.bottom-nav__pagination ul');
        $pages = $("li:not(:first-child):not(:last-child)", $wrapper);
        $lis = $("li", $wrapper);
        infScroll = $container.data('infiniteScroll');
        pagesCount = parseInt($pages.last().text());
        
        prevArrow = function() {
            return '<li><a href="' + getUrl(infScroll.pageIndex - 1) + '" rel="prev"><i class="fas fa-angle-left"></i></a></li>';
        }
        nextArrow = function(disabled = false) {
            if (disabled) return '<li class="disabled"><a><i class="fas fa-angle-right"></i></a></li>';

            return '<li><a href="' + getUrl(infScroll.pageIndex + 1) + '" rel="next"><i class="fas fa-angle-right"></i></a></li>';
        }
        
        // Remove all li's
        $lis.remove();

        // Prev button
        $wrapper.append(prevArrow());

        // Main li's with dots
        $wrapper.append(pagination(infScroll.pageIndex, pagesCount));
        
        // Next button
        infScroll.pageIndex != pagesCount ? $wrapper.append(nextArrow()) : $wrapper.append(nextArrow(true));
    }
});