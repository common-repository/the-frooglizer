=== The Frooglizer ===
Contributors: WordpressManiac
Donate link: http://www.psp-tubes.net/wordpress/plugin-to-mine-keyword-data-from-froogle
Tags: frooglizer, froogle-izer, keywords, admin, niche, plugin, keyword, froogle, shopping, tool, datamine, google, random
Requires at least: 2.5
Tested up to: 2.6.2
Stable tag: 0.4

Fun little plugin to data mine new niche keyword ideas from Froogle and Google Suggest.

== Description ==

This is just a fun little plugin that datamines keywords.

Ideal for people that struggle to think out-of-the-box for new niche ideas to build web sites.

It uses 2 sources at the moment.

Source number 1 is Froogle (Google shopping site). The keywords come from actual searches that users on Froogle are entering.

Source number 2 is Google Suggest. Here you can enter keywords, or partial keywords, and you will get a list of the most searched for terms, as well as an estimate of the number of competing pages on Google for each keyword. Handy for some slightly more detailed keyword research. What you are ideally looking for is heavily searched for keywords, that have less competing pages.

See also: [More Handy Plugins for eBay (EPN) and phpBay](http://www.psp-tubes.net/wordpress/).

== Installation ==

Upload to your WP plugins folder, and activate.

Note: Requires you to have cURL on your server. Most hosts have this already.

Note: People hosting on GoDaddy need to edit 1 clearly marked variable in the plugin code.

== Frequently Asked Questions ==

1) Where do the keywords come from ?

They come from Froogle (Google shopping site), and from Google Suggest.

2) Can I enter partial keywords into the Google Suggest section ?

Yes.

Entering "golf c" will display keywords starting with the word "golf" and a second word starting with the letter "c".

3) How do I get more keywords ?

Just click the "Froogle-izer" link again, and you will get another screen full of keywords.

4) I have a wide screen. Can I see more than 125 keywords at a time ?

Yes.

Look in the code for the "$numberofcurls" variable. It will be around about line 200. The default value is 5. You can increase or decrease this as you like. The number of keywords returned is 25 x $numberofcurls.