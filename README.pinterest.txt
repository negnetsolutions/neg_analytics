# Add to shopify thank you page.

{% if first_time_accessed %}
<script type="text/javascript">
!function(e){if(!window.pintrk){window.pintrk=function()
{window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
n=window.pintrk;n.queue=[],n.version="3.0";var
t=document.createElement("script");t.async=!0,t.src=e;var
r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}
("https://s.pinimg.com/ct/core.js");

pintrk('load','YOUR_TAG_ID', { em: '{{ customer.email }}', }); 
pintrk('page'); 
</script> 
<noscript> 
<img height="1" width="1" style="display:none;" alt=""
src="https://ct.pinterest.com/v3/?tid=YOUR_TAG_ID&noscript=1" /> 
</noscript>

<script>
pintrk('track', 'checkout',{
value: {{ total_price }} / 100,
currency: '{{ currency }}',
order_quantity: 1,
order_id: {{ checkout.id }}
});
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt=""
src="https://ct.pinterest.com/v3/?tid=YOUR_TAG_ID&event=checkout&noscript=1"/>
</noscript>
{% endif %}
