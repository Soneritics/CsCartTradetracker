{assign var="productCount" value=0}
{foreach from=$order_info.products item=product name=productLoop}
    {assign var="productCount" value=$productCount+$product.amount}
{/foreach}

<script type="text/javascript">
    var ttConversionOptions = ttConversionOptions || [];
    ttConversionOptions.push({
        type: 'sales',
        campaignID: '{$tradetrackerCID}',
        productID: '{$tradetrackerPID}',
        transactionID: '{$order_info.order_id}',
        transactionAmount: '{$order_info.total}',
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
    <img src="//ts.tradetracker.net/?cid={$tradetrackerCID}&amp;pid={$tradetrackerPID}&amp;tid={$order_info.order_id}&amp;tam={$order_info.total}&amp;data=&amp;qty={$productCount}&amp;eml=&amp;descrMerchant=&amp;descrAffiliate=&amp;event=sales&amp;currency=EUR" alt="" />
</noscript>
