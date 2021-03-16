# Add shopify thank you page:

{% if first_time_accessed %}
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','//connect.facebook.net/en_US/fbevents.js');

    fbq('init', 'INSERTFBPIXELID');
    fbq('track', "PageView");
    fbq('track', 'Purchase', {
	  value: '{{ total_price | money_without_currency }}',
	  currency: '{{ shop.currency }}', 
	  order_id: '{{ order_number }}',
	  content_ids: [{% for line in order.line_items %}'{{ line.sku }}'{% unless forloop.last == true %}, {% endunless %}{% endfor %}],
	  content_type: 'product',
	  num_items: '{{ item_count }}'  
});
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=INSERTFBPIXELID&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
{% endif %}
