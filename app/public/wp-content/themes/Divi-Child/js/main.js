function setsameHeight(classToGet){
    var heightOld = 0;
    jQuery(classToGet).css('height', "auto");
    jQuery(classToGet).each(function(i, object) {
        var heightObject = jQuery(this).outerHeight();
        if(heightObject > heightOld){
            heightOld = heightObject;
        }
    });
    jQuery(classToGet).css('min-height', 'fit-content');
    jQuery(classToGet).css('height', heightOld);
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

function ShowMore() {
    jQuery( ".more-values" ).each(function( index ) {
        var custom_attribute_value = jQuery(this).text();
        if(custom_attribute_value=='') {
            jQuery(this).closest('.spec-row').hide();
        }
    });

   jQuery( ".drawing_url" ).each(function( index ) {
        var custom_attribute_href = jQuery(this).attr("href");
        if(custom_attribute_href == ''){
           jQuery(this).closest('.spec-row').hide();
        }
        if(custom_attribute_href.includes('request')){
           jQuery(this).text("Request Drawing");
        }
    });
}

jQuery( function() {
    //jQuery("#et-divi-customizer-global-cached-inline-styles").html("")

    setsameHeight('.guide-chapter');
    setsameHeight('.home-popular-category');
    /*setsameHeight('.show-subcategories li');*/

    window.addEventListener('resize', function () {
        setTimeout(function () {
                setsameHeight('.guide-chapter');
                setsameHeight('.home-popular-category');
                /*setsameHeight('.show-subcategories li');*/
            },
            1000);

    });

    if(jQuery("table").length){
        jQuery("table").each(function(i, object) {
            if(!jQuery(this).hasClass("table-responsive") && !jQuery(this).hasClass("part-table")){
                jQuery(this).addClass("table-responsive")
            }
        });
    }

    /* SLICK REVIEW ON HOMEPAGE */
    if(jQuery(".products-reviews").length){
        jQuery('.products-reviews').slick({
            infinite: true,
            autoplay: true,
            autoplaySpeed: 3500,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            fade: true,
        });
    }

    /***
     * CHECKOUT PAGE
     ***/
    var woocommerce_form = jQuery( '.woocommerce-cart form' );
    woocommerce_form.on('change', '.qty', function(){
        form = jQuery(this).closest('form');

        // emulates button Update cart click
        jQuery("<input type='hidden' name='update_cart' id='update_cart' value='1'>").appendTo(form);

        // get the form data before disable button...
        formData = form.serialize();

        // disable update cart and proceed to checkout buttons before send ajax request
        jQuery("input[name='update_cart']").val('Updating…').prop('disabled', true);
        jQuery("a.checkout-button.wc-forward").addClass('disabled').html('Updating…');
        jQuery(form).addClass('blockUI blockOverlay')

        // update cart via ajax
        jQuery.post( form.attr('action'), formData, function(resp) {
            // get updated data on response cart
            var shop_table = jQuery('table.shop_table.cart', resp).html();
            var cart_totals = jQuery('.cart-collaterals .cart_totals', resp).html();

            // replace current data by updated data
            jQuery('.woocommerce-cart table.shop_table.cart')
                .html(shop_table)
                .find('.qty')
            //.before('<input type="button" value="-" class="minus">')
            //.after('<input type="button" value="+" class="plus">');
            jQuery('.woocommerce-cart .cart-collaterals .cart_totals').html(cart_totals);
            jQuery(form).removeClass('blockUI blockOverlay')
        });
    }).on('click','.quantity .qty-decrease', function() {
        console.log(("decrease"))
        var current = jQuery(this).next('.qty').val();
        current--;
        jQuery(this).next('.qty').val(current).trigger('change');
    }).on('click','.quantity .qty-increase', function() {
        console.log(("increase"))
        var current = jQuery(this).prev('.qty').val();
        current++;
        jQuery(this).prev('.qty').val(current).trigger('change');
    })
    jQuery( '.woocommerce-cart' ).on( 'click', "a.checkout-button.wc-forward.disabled", function(e) {
        e.preventDefault();
    });

    /* FILTERS BLOGS */
    jQuery(".filters-categories-item").click(function(e){
        let category_id = e.target.id;

        if(jQuery("#"+category_id).hasClass("filters-categories-item-used")){
            jQuery("#"+category_id).removeClass("filters-categories-item-used")
        }else{
            jQuery("#"+category_id).addClass("filters-categories-item-used")
        }

        if(jQuery(".filters-categories-item-used").length === 0){
            jQuery(".et_pb_blog_grid article").removeClass("filters-categories-item-hidden")
        }else{
            jQuery( ".filters-categories-item" ).each(function( index ) {
                let bucle_category_id = jQuery(this)[0].id;

                let category_class = bucle_category_id.replace("filter_category_", "article.category-")
                if(jQuery("#"+bucle_category_id).hasClass("filters-categories-item-used")){
                    jQuery(category_class).removeClass("filters-categories-item-hidden")
                }else{
                    jQuery(category_class).addClass("filters-categories-item-hidden")
                }
            });
        }
    });

    /***
     * SINGLE PRODUCT
     ***/
    /* CUSTOM INPUT NUMBER DIV ACTION */
    var woocommerce_form_pdp = jQuery( '.single-product form.cart' );
    woocommerce_form_pdp.on('click', '.quantity .qty-increase', function(){
        var current = jQuery(this).prev('.qty').val();
        current++;
        jQuery(this).prev('.qty').val(current);
    }).on('click', '.quantity .qty-decrease', function(){
        var current = jQuery(this).next('.qty').val();
        if(current > 0){
            current--;
            jQuery(this).next('.qty').val(current);
        }
    });

    /* ON PRODUCT VARIATION CHANGE, CHANGE ADDITIONAL INFORMATION TABS */
    jQuery(document.body).on('click', '#view_less_button, #view_more_button' ,function(){
        jQuery('.et_pb_tabs_controls li').removeClass("et_pb_tab_active");
        jQuery('.additional_information_tab').addClass("et_pb_tab_active");

        jQuery('.et_pb_tab').css("display", "none");
        jQuery( ".et_pb_all_tabs .et_pb_tab" ).each(function( index ) {
            if(index == 1){
                jQuery(this).css("transition", "fadeIn 5s");
                jQuery(this).css("display", "block");

            }else{
                jQuery(this).css("display", "none");
            }
        });

        jQuery("#more-information-variation .woocommerce-variation.single_variation").html(jQuery(".woocommerce-variation.single_variation").html())

        jQuery('html, body').animate({
            scrollTop: (jQuery(".et_pb_tabs_controls ").offset().top - 150)
        }, 2000);
    });

    /*jQuery(document.body).on('click', '#view_less_button, #view_more_button' ,function(){
        jQuery('#view_more_section').toggle();
        jQuery('#view_more_button').toggle();
        jQuery('#view_less_button').toggle();
    });*/

    /* ON PRODUCT VARIATION CHANGE, CHANGE ADDITIONAL INFORMATION TABS */
    jQuery( ".variations_form" ).change(function(){
        jQuery("#more-information-variation .woocommerce-variation.single_variation").html(jQuery(".woocommerce-variation.single_variation").html())
        ShowMore();
    })

    /* PRODUCT VARIATION ADDITIONAL INFORMATION SHOW ON TABS */
    jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
        var attribute_counter=0;
        jQuery( ".more-values" ).each(function( index ) {
            var custom_attribute_value = jQuery(this).text();
            if(custom_attribute_value==''){
                jQuery(this).closest('.spec-row').hide();
                //jQuery('#view_more_button').hide();
            } else{
                attribute_counter=attribute_counter+1;
            }
            if(attribute_counter<=1){
                jQuery('#view_more_button').hide();
            }else{
                jQuery('#view_more_button').show();
            }
        });

        jQuery( ".drawing_url" ).each(function( index ) {
            var custom_attribute_href = jQuery(this).attr("href");
            if(custom_attribute_href==''){
                jQuery(this).closest('.spec-row').hide();
            }else{
                jQuery('#view_more_button').show();
            }
        });

        jQuery("#more-information-variation .woocommerce-variation.single_variation").html(jQuery(".woocommerce-variation.single_variation").html())
    });

    /* PRODUCT VARIATION PRICES CHANGES */
    jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
        var price = jQuery('.woocommerce-variation-price').html();
        jQuery('.vi-woo-product-price').html(price);
    });
    jQuery( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
        var price = jQuery('.woocommerce-variation-price').html();
        jQuery('.vi-woo-product-price').html(price);
    });

    /*setTimeout(function(){
        console.log("PASA AQUI")
        var attribute_count=0;
        jQuery( ".more-values" ).each(function( index ) {
            var custom_attribute_value = jQuery(this).text();
            if(custom_attribute_value==''){
                jQuery(this).closest('.spec-row').hide();
            }else{
                attribute_count=attribute_count+1;
            }
            if(attribute_count<=1){
                jQuery('#view_more_button').hide();
            }else{
                jQuery('#view_more_button').show();
            }
        });

        jQuery( ".drawing_url" ).each(function( index ) {
            var custom_attribute_href = jQuery(this).attr("href");
            if(custom_attribute_href==''){
                jQuery(this).closest('.spec-row').hide();
            }else{
                jQuery('#view_more_button').show();
            }
        });
    }, 2000);*/

    jQuery('.woocommerce-product-gallery__image > a').attr('data-lightbox', 'woo-image');
    /* ########## OLD ########## */

    /* ########## MENU COLLAPSE ########## */
    jQuery(document).ready(function() {
        jQuery("body ul.et_mobile_menu li.menu-item-has-children, body ul.et_mobile_menu  li.page_item_has_children").append('<a href="#" class="mobile-toggle"></a>');
        jQuery('ul.et_mobile_menu li.menu-item-has-children .mobile-toggle, ul.et_mobile_menu li.page_item_has_children .mobile-toggle').click(function(event) {
            event.preventDefault();
            jQuery(this).parent('li').toggleClass('dt-open');
            jQuery(this).parent('li').find('ul.children').first().toggleClass('visible');
            jQuery(this).parent('li').find('ul.sub-menu').first().toggleClass('visible');
        });
        iconFINAL = 'P';
        jQuery('body ul.et_mobile_menu li.menu-item-has-children, body ul.et_mobile_menu li.page_item_has_children').attr('data-icon', iconFINAL);
        jQuery('.mobile-toggle').on('mouseover', function() {
            jQuery(this).parent().addClass('is-hover');
        }).on('mouseout', function() {
            jQuery(this).parent().removeClass('is-hover');
        })
    });


    var TestTerm = getUrlParameter('s');
    if(TestTerm != ""){
        gtag('event', 'Search', {'event_category' : 'ZeroResults','event_label' : TestTerm});
    }
});

//Start SAN-PDP-Implementation-Oct2023
jQuery(document).ready(function($) {
    // Add review count after product title
    function assignRowID() {
        var rowElement = jQuery('.et_pb_row.et_pb_row_3_tb_body');
        if (rowElement.length > 0) {
            rowElement.attr('id', 'product-info');
        }
    }
    // Reviews section and link interactions
    function handleReviews() {
        var checkElementInterval = setInterval(function() {
            var reviewElements = jQuery('.R-TextBody.R-TextBody--xs.u-textLeft--all.u-marginBottom--xs');

            if (reviewElements.length > 0) {
                var text = reviewElements.text();
                var number = parseInt(text.match(/\d+/)) || 0;

                var titleElement = jQuery('.et_pb_wc_title');
                var newContent = jQuery('<div></div>').addClass('star-section');
                var stars = jQuery('.R-RatingStars.R-RatingStars--md.u-verticalAlign--middle');

                if (stars.length > 0 && number > 0) {
                    newContent.html(`
                        <div>${stars.html()}</div>
                        <div class="reviews-text">
                            <a href="#product-info" class="link-write read-link">${number} Review${number !== 1 ? 's' : ''}</a> |
                            <a href="#product-info" class="link-write review-open">Write a Review</a>
                        </div>
                    `);
                } else {
                    newContent.html(`
                        <div style="margin-top: 5px;">
                            <span class="ricon-percentage-star--0 star__icon star__icon--empty"></span>
                            <span class="ricon-percentage-star--0 star__icon star__icon--empty"></span>
                            <span class="ricon-percentage-star--0 star__icon star__icon--empty"></span>
                            <span class="ricon-percentage-star--0 star__icon star__icon--empty"></span>
                            <span class="ricon-percentage-star--0 star__icon star__icon--empty"></span>
                        </div>
                        <div class="reviews-text">
                            <a href="#product-info" class="link-write read-link">0 Reviews</a> |
                            <a href="#product-info" class="link-write review-open">Write a Review</a>
                        </div>
                    `);
                }

                if (titleElement) {
                    titleElement.append(newContent);
                }
                clearInterval(checkElementInterval);
            }
        }, 500);
    }

    function handleLinkClick(e) {
        e.preventDefault();
        var hash = jQuery(this).attr('href');
        var targetElement = jQuery(hash);
        if (targetElement.length > 0) {
            jQuery('html, body').animate({
                scrollTop: targetElement.offset().top
            }, 500);
			if (jQuery(this).hasClass('review-open')) {
				jQuery('a[href="#tab-reviews"]').click();
				jQuery('.R-Button.R-Button--md.R-Button--primary.u-marginBottom--none').click();
			}
            if (jQuery(this).hasClass('read-link')) {
                jQuery('a[href="#tab-reviews"]').click();
            }
        } else {
            // console.error("Target element not found for smooth scrolling.");
        }
    }

    jQuery(document).on('click', 'a.read-link, a.review-open', handleLinkClick);

    assignRowID();
    handleReviews();

    // Free shipping prop
    $('.single_variation_wrap .woocommerce-variation.single_variation').after(
        '<div class="free-shipping-prop">' +
        '<p><img decoding="async" src="https://storage.googleapis.com/images.trinity.one/SAN/SAN-A-PDPRededesign-V2-July2023/Icon_freeshipping.svg"><b>FREE SHIPPING</b> on orders over $100</p>' +
        '<p id="countdown"></p>' +
        '</div>'
    );

	// Add countdown timer for same-day shipping
    function getTimeRemaining() {
        const now = new Date();
        const currentDay = now.getDay();
        // Check if it's a weekday (Monday to Friday)
        if (currentDay >= 1 && currentDay <= 5) {
            const deadline = new Date();
            // Set the cut-off time to 3 PM CST
            const chicagoTime = new Date(now.toLocaleString('en-US', { timeZone: 'America/Chicago' }));
            deadline.setTime(chicagoTime);
            deadline.setUTCHours(21, 0, 0, 0);
            let timeDiff = deadline - now;
            if (timeDiff < 0) {
                return { hours: 0, minutes: 0 };
            }
            const oneHour = 60 * 60 * 1000;
            const oneMinute = 60 * 1000;
            const hours = Math.floor(timeDiff / oneHour);
            const minutes = Math.floor((timeDiff % oneHour) / oneMinute);
            return { hours, minutes };
        } else {
            return null;
        }
    }
    function initializeClock() {
        const clock = document.getElementById('countdown');
        function updateClock() {
            const t = getTimeRemaining();

            if (t) {
                if (t.hours <= 0 && t.minutes <= 0) {
                    //jQuery('#countdown').html('Order now for next day shipping');
                } else {
                    jQuery('#countdown').html(`Order within <span id="count-time">${t.hours}h ${t.minutes}min</span> for same day shipping`);
                }
            } else {
                // Hide the countdown on non-weekdays
                jQuery('#countdown').hide();
            }
        }
        updateClock();
        const timeinterval = setInterval(updateClock, 1000);
    }
    initializeClock();
});
//End SAN-PDP-Implementation-Oct2023

//add to cart message continue shopping button SAN-CartFlyout-Implementation-Dec2023
document.addEventListener('DOMContentLoaded', function () {
    const continueButtons = document.querySelectorAll('body.single-product .woocommerce-message .continue');

    // Check if the elements are found
    if (continueButtons.length > 0) {
      // Add onclick event to hide the .woocommerce-message when .continue is clicked
      continueButtons.forEach(function (continueButton) {
        continueButton.addEventListener('click', function () {
          var woocommerceMessage = document.querySelector('body.single-product .woocommerce-message');
          if (woocommerceMessage) {
            woocommerceMessage.style.display = 'none';
          }
        });
      });
    }
});

/* Search Results */

// Display the number of search results for products and posts
document.addEventListener('DOMContentLoaded', function () {
  updateSearchResultsCount('.search-results__list--products', '.product');
  updateSearchResultsCount('.search-results__list--posts', 'article');
});

function updateSearchResultsCount(containerSelector, itemSelector) {
  const container = document.querySelector(containerSelector);

  if (container) {
    const itemCount = container.querySelectorAll(itemSelector).length;
    const commonParent = container.closest('.search-results__row');
    const header = commonParent ? commonParent.querySelector('.search-results__heading h2') : null;

    if (header) {
      const countSpan = document.createElement('span');
      countSpan.className = 'search-results__count';
      countSpan.textContent = ` (${itemCount})`;
      header.appendChild(countSpan);
    }
  }
}

// Show More and Less Posts
document.addEventListener('DOMContentLoaded', function () {
  const blogPosts = document.querySelectorAll('.search-results__list--posts article');
  const products = document.querySelectorAll('.search-results__list--products .product');
  const showMorePostsBtns = document.querySelectorAll('.search-results__show-more--posts');
  const showMoreProductsBtns = document.querySelectorAll('.search-results__show-more--products');

  function hideInitialElements(elements) {
    elements.forEach((element, index) => {
      if (index > 3) element.style.display = 'none';
    });
  }

  function toggleElements(button, elements) {
    let buttonText = button.querySelector('span');
    let isShowingMore = buttonText.textContent === 'Show Less';
    elements.forEach((element, index) => {
      if (index > 3) {
        element.style.display = isShowingMore ? 'none' : 'flex';
      }
    });
    buttonText.textContent = isShowingMore ? 'Show More' : 'Show Less';
  }

  // Add event listeners to all buttons with the class for blog posts
  showMorePostsBtns.forEach(showMorePostsBtn => {
    showMorePostsBtn.addEventListener('click', function() {
      toggleElements(this, blogPosts);
    });
  });

  // Add event listeners to all buttons with the class for products
  showMoreProductsBtns.forEach(showMoreProductsBtn => {
    showMoreProductsBtn.addEventListener('click', function() {
      toggleElements(this, products);
    });
  });

  hideInitialElements(blogPosts);
  hideInitialElements(products);
});
