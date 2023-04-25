# nlink
PHP script to easily redirect users to your game in their local Nintendo eShop® according to their IP.
Instead of having a links for each local Nintendo eShop®, just share one on your posts!

If you use it, ping me in Twitter at @rawrlabgames!
	
# Configuration
1. Change the $secret variable to whatever you like and the $base_folder.
2. Download geoip2.phar and rename it so it will include this secret value in its name.
3. Download GeoIP2 Lite Country database and rename it so it will include this secret value in its name.
4. You should have those 4 files in the same folder: 
	geoip2-$secret.phar
	GeoLite2-Country-$secret.mmdb
	.htaccess
	index.php
5. Configure the variables from below
6. Create your worldwide Nintendo eShop URLs!
  URL examples:
  - For games in the list: 
    https://rawrlab.com/nlink/murtop
  - For games not in the list (requires enabling $public_mode): 
    https://rawrlab.com/nlink/0100123019BEA000
    
    
Nintendo eShop and Nintendo are trademarks of Nintendo.
