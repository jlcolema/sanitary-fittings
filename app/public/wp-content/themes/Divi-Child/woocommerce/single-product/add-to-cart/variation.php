<?php
/*/**
 * Single variation display
 *
 * This is a javascript-based template for single variations (see https://codex.wordpress.org/Javascript_Reference/wp.template).
 * The values will be dynamically replaced after selecting attributes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;
?>

<script type="text/template" id="tmpl-variation-template">
    <div class="spec-row">
        <div class="spec-name">Description</div>
        <div class="spec-value">{{{ data.variation.variation_description }}}</div>
    </div>
    <div class="spec-row">
        <div class="spec-name">SKU</div>
        <div class="spec-value"><span itemprop="sku">{{{ data.variation.sku }}}</span></div>
    </div>
    <div class="spec-row">
        <div class="spec-name">Availability</div>
        <div class="spec-value" style="color:#f78e1e;">{{{ data.variation.text_field }}}</div>
    </div>

    <div id="view_more_section" style="display:none;">
        <div class="spec-row">
            <div class="spec-name">Sanitary Size</div>
            <div class="spec-value more-values">{{{ data.variation.sanitary_size }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">For Hose Id</div>
            <div class="spec-value more-values">{{{ data.variation.for_hose_id }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Flange OD</div>
            <div class="spec-value more-values">{{{ data.variation.flange_od }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Tube ID</div>
            <div class="spec-value more-values">{{{ data.variation.tube_id }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Wall Thickness</div>
            <div class="spec-value more-values">{{{ data.variation.wall_thickness }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Length</div>
            <div class="spec-value more-values">{{{ data.variation.pipe_size_length }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Weight</div>
            <div class="spec-value more-values">{{{ data.variation.weight }}} lbs.</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Temp. Rating</div>
            <div class="spec-value more-values">{{{ data.variation.temperature_rating }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Pressure Rating</div>
            <div class="spec-value more-values">{{{ data.variation.pressure_rating }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Material</div>
            <div class="spec-value more-values" >{{{ data.variation.material }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Surface Finish</div>
            <div class="spec-value more-values" >{{{ data.variation.surface_finish }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Specification</div>
            <div class="spec-value more-values" >{{{ data.variation.specification }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Certification</div>
            <div class="spec-value more-values" >{{{ data.variation.certification }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Line Drawing</div>
            <div class="spec-value more-values"><a class="drawing_url" href="{{{ data.variation.drawing_url }}}" target="_blank" onclick="gtag('event', 'PDF', {'event_category' : 'Download','event_label' : '{{{ data.variation.sku }}}'});">Download</a></div>
        </div>

        <div class="spec-row">
            <div class="spec-name">OEM</div>
            <div class="spec-value more-values" >{{{ data.variation.oem }}}</div>
        </div>

        <div class="spec-row">
            <div class="spec-name">Extended OEM Part Number</div>
            <div class="spec-value more-values" >{{{ data.variation.ext_oem_part_no }}}</div>
        </div>

        <div class="spec-row">
            <div class="spec-name">MPN</div>
            <div class="spec-value more-values" >{{{ data.variation.mpn }}}</div>
        </div>
        <div class="spec-row">
            <div class="spec-name">Kit Includes</div>
            <div class="spec-value more-values" >{{{ data.variation.kit_includes }}}</div>
        </div>
    </div>

    <a id="view_more_button" onclick="ShowMore()">View More Details</a>
    <a id="view_less_button" style="display:none;" onclick="">View Less Details</a>

    <div class="woocommerce-variation-price" style="display:none;">
        {{{ data.variation.price_html }}}
    </div>
    <div class="woocommerce-variation-availability" style="display:none;">
        {{{ data.variation.availability_html }}}
    </div>
</script>

<script type="text/template" id="tmpl-unavailable-variation-template">
    <p><?php /*_e( 'Please call for pricing and availability.', 'woocommerce' ); */?></p>
</script>