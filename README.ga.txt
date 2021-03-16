# Add shopify thank you page:

{% if first_time_accessed %}
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'GA_MEASUREMENT_ID');

var shipping_price = '{{shipping_price | money_without_currency }}';
shipping_price = shipping_price.replace(",", ".");

var total_price = '{{total_price | money_without_currency }}';
total_price = total_price.replace(",", ".");

var tax_price = '{{tax_price | money_without_currency }}';
tax_price = tax_price.replace(",", ".");

gtag('event', 'purchase', {
currency: "{{shop.currency}}",
transaction_id: '{{order.transactions[0].id}}',
shipping: shipping_price,
value: total_price,
tax: tax_price
})
</script>
{% endif %}
