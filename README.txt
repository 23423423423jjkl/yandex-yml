Module implements an export of any XML view in YML format for market.yandex.ru.

After installation:

1) enable this module,

2) go to settings page (admin/config/yandex_market_xml) and choose a vocabulary and a view, which provides valid xml;
view must contain tags from YML specification (http://help.yandex.ru/partnermarket/offers.xml), tags 'id', 'type', 
'available', 'bid' will be processed as offer attributes (even if they are tags in view output);
you can use Views Data Export module to provide valid products XML

3) go to yandex_market_xml page at your site and watch the result.

There is no dependency from any commerce module - you can use any storage for products, you have to provide XML 
view.
But for default there is only one plugin - for retrieving currencies (in src/plugins/Commerce.php).
You may add your custom plugin for any system that you like (in src/plugins directory) and choose it in module settings.

Produced by rostechcom.ru.

I spent time for getting this module flexible, useful and commerce independent. It will be great if you donate some part
of your profit to Yandex Money 410011277443381.