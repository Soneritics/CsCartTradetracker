{assign var="productCount" value=0}
{foreach from=$order_info.products item=product name=productLoop}
    {assign var="productCount" value=$productCount+$product.amount}
{/foreach}

{assign var="totalProductPrice" value=$order_info.total}
{foreach from=$order_info.taxes item=tax name=taxLoop}
    {assign var="totalProductPrice" value=round($totalProductPrice-$tax.tax_subtotal, 2)}
{/foreach}

<script type="text/javascript" data-no-defer>
    var ttConversionOptions = ttConversionOptions || [];
    ttConversionOptions.push({
        type: 'sales',
        campaignID: '{$tradetrackerCID}',
        productID: '{$tradetrackerPID}',
        transactionID: '{$order_info.order_id}',
        transactionAmount: '{$totalProductPrice}',
        quantity: '{$productCount}',
        email: '{$order_info.email}',
        descrMerchant: '',
        descrAffiliate: '',
        currency: 'EUR'
    });

    (function(ttConversionOptions) {
        var campaignID = 'campaignID' in ttConversionOptions ? ttConversionOptions.campaignID : ('length' in ttConversionOptions && ttConversionOptions.length ? ttConversionOptions[0].campaignID : null);
        var tt = document.createElement('script'); tt.type = 'text/javascript'; tt.async = true; tt.src = '//tm.tradetracker.net/conversion?s=' + encodeURIComponent(campaignID) + '&t=m';
        var s = document.getElementsByTagName('script'); s = s[s.length - 1]; s.parentNode.insertBefore(tt, s);
    })(ttConversionOptions);
</script>
<noscript>
    <img src="//ts.tradetracker.net/?cid={$tradetrackerCID}&amp;pid={$tradetrackerPID}&amp;tid={$order_info.order_id}&amp;tam={$totalProductPrice}&amp;data=&amp;qty={$productCount}&amp;eml=&amp;descrMerchant=&amp;descrAffiliate=&amp;event=sales&amp;currency=EUR" alt="" />
</noscript>
