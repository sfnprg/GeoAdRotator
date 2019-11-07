# GeoAdRotator
A simple ad server/rotator with geotargeting functionalities based on user IP address.
IP can be faken or inaccurate so this system is not 100% proof but should do the job most of the times.

You have flexibility to create and display ads with any format and country.

Uses free [freegeoip.app](https://freegeoip.app) API to convert user IP to country which is limited to 15,000 queries per hour.

@author  [Yes We Web](https://www.yesweweb.com/)

## Setup
You need to create a textual files file containing all the ad codes (tags, images, iframes, ...) in a new line which should be separated by tilde (`~`). Codes in *default* files will be delivered to any country.

Example of naming convention for ad code files:
|Filename |Description|
|--|--|
|`ad_default_300x250.txt`|contains *default* ads (displayed in any country) in 300x250 format|
|`ad_IT_300x250.txt`|contains ads for Italian users in 300x250 format            |
|`ad_ES_728x90.txt`|contains ads for Spanish users in 728x90 format|
It uses ISO Alpha-2 (2 letter code) to identify countries.

In your code you need to include the **GeoAdRotator** and initialize it:
`require_once (__DIR__ . "/GeoAdRotator/GeoAdRotator.php");`
`$adServerRotator = new GeoAdRotator();`

Where you want to display a random ad, you should add code in the following format:
`echo $adServerRotator->serve( AD_SIZE, NUMBER_OF_ADS);`

 - *AD_SIZE*: is the size of the ad you want to display (e.g. 300x250)
 - *NUMBER_OF_ADS*: number of ads to be displayed (optional, default value *1*)

For example:
`echo $adServerRotator->serve("300x250", 1);`

## Disclaimer
Project created mainly for personal use so WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED.
