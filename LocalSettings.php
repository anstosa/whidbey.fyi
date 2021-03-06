<?php

$isDebugging = getenv("DEBUG") === "true";

# Debugging
error_reporting($isDebugging ? -1 : 0);
ini_set('display_errors', $isDebugging ? 1 : 0);

# This file was automatically generated by the MediaWiki 1.35.0
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if (!defined('MEDIAWIKI')) {
	exit;
}


## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "Whidbey FYI";
$wgMetaNamespace = "Meta";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";
$wgScriptExtension = ".php";
$wgArticlePath = "/$1";
$wgUsePathInfo = true;

## The protocol and server name to use in fully-qualified URLs
$wgServer = "https://wiki.whidbey.fyi";

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

# Infobox templates
$wgUseInstantCommons = true;

## The URL paths to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogos = ['1x' => "$wgResourceBasePath/images/Logo.png"];

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = false; # UPO

$wgSMTP = [
	'host' => "tls://" . getenv("SMTP_HOST"),
	'IDHost' => getenv("SMTP_HOST"),
	'port' => 465,
	'username' => getenv("SMTP_USERNAME"),
	'password' => getenv("SMTP_PASSWORD"),
	'auth' => true
];

$wgEmergencyContact = "admin@whidbey.fyi";
$wgPasswordSender = "admin@whidbey.fyi";

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEmailAuthentication = true;

## Database settings
$url = parse_url(getenv("DATABASE_URL"));
$wgDBtype = "postgres";
$wgDBserver = $url["host"];
$wgDBname = substr($url["path"], 1);
$wgDBuser = $url["user"];
$wgDBpassword = $url["pass"];

# Postgres specific settings
$wgDBport = "5432";
$wgDBmwschema = "mediawiki";

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = [];

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgGenerateThumbnailOnParse = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgFileExtensions = array_merge(
	$wgFileExtensions,
	[
		'pdf', 'ppt', 'doc', 'docx', 'xls', 'xlsx', 'json'
	]
);

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = false;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "C.UTF-8";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publicly accessible from the web.
$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = "en";

$wgSecretKey = getenv("SECRET_KEY");

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = getenv("UPGRADE_KEY");

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-nc-sa/4.0/";
$wgRightsText = "Creative Commons Attribution-NonCommercial-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-nc-sa.png";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "Vector";
$wgMFDefaultSkinClass = 'SkinMinerva'; // use Minerva skin (You need to download and install it separately, otherwise you'll get an exception)

# Jobs
$wgJobRunRate = 1;

# Editor
# $wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";
# $wgVisualEditorUseSingleEditTab = true;
$wgEnableRestAPI = true;



# Permissions
$wgGroupPermissions['user']['writeapi'] = true;

# Maps
// $egMapsDefaultService = 'googlemaps3';
// $egMapsGMaps3Controls = [
// 	'pan',
// 	'zoom',
// 	'type',
// 	'scale',
// ];
$egMapsDefaultGeoService = 'google';
$egMapsGMaps3ApiKey = getenv('GOOGLE_API_KEY');
$egMapsDistanceUnit = 'mi';

# SMW
$smwgConfigFileDir = "/app";

# AWS
$wgAWSRegion = 'us-west-2';
$wgAWSBucketName = "images.wiki.whidbey.fyi";
$wgAWSBucketDomain = '$1';

# Analytics
$wgHeadScriptCode = <<<'START_END_MARKER'
<script async src="https://www.googletagmanager.com/gtag/js?id=G-64L2YQTKCR"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-64L2YQTKCR');
</script>
START_END_MARKER;

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin('Vector');
wfLoadSkin('chameleon');
wfLoadSkin('MinervaNeue');

# Enabled extensions. Most of the extensions are enabled by adding
# wfLoadExtension( 'ExtensionName' );
# to LocalSettings.php. Check specific extension documentation for more details.
# The following extensions were automatically enabled:
wfLoadExtension('AWS');
wfLoadExtension('Bootstrap');
wfLoadExtension('CategoryTree');
wfLoadExtension('Cite');
wfLoadExtension('CiteThisPage');
wfLoadExtension('CodeEditor');
wfLoadExtension('ConfirmEdit');
wfLoadExtension('Gadgets');
wfLoadExtension('HeadScript');
wfLoadExtension('ImageMap');
wfLoadExtension('InputBox');
wfLoadExtension('Interwiki');
wfLoadExtension('LocalisationUpdate');
wfLoadExtension('Maps');
wfLoadExtension('MobileFrontend');
wfLoadExtension('MultimediaViewer');
wfLoadExtension('Nuke');
wfLoadExtension('OATHAuth');
wfLoadExtension('PageImages');
wfLoadExtension('ParserFunctions');
wfLoadExtension('PdfHandler');
wfLoadExtension('Poem');
wfLoadExtension('Renameuser');
wfLoadExtension('ReplaceText');
wfLoadExtension('Scribunto');
wfLoadExtension('SecureLinkFixer');
wfLoadExtension('SpamBlacklist');
wfLoadExtension('SyntaxHighlight_GeSHi');
wfLoadExtension('TemplateData');
wfLoadExtension('TemplateStyles');
wfLoadExtension('TextExtracts');
wfLoadExtension('TitleBlacklist');
wfLoadExtension('VisualEditor');
wfLoadExtension('WikiEditor');

# End of automatically generated settings.
# Add more configuration options below.

enableSemantics('wiki.whidbey.fyi');

# Debugging
$wgShowExceptionDetails = $isDebugging;
$wgShowDBErrorBacktrace = $isDebugging;
$wgShowSQLErrors = $isDebugging;
