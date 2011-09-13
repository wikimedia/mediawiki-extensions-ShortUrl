<?php
/**
 * Aliases for special pages
 *
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'ShortUrl' => array( 'ShortUrl' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'ShortUrl' => array( 'مسار_قصير' ),
);

/** German (Deutsch) */
$specialPageAliases['de'] = array(
	'ShortUrl' => array( 'Saubere_URL' ),
);

/** Persian (فارسی) */
$specialPageAliases['fa'] = array(
	'ShortUrl' => array( 'نشانی_کوتاه' ),
);

/** Indonesian (Bahasa Indonesia) */
$specialPageAliases['id'] = array(
	'ShortUrl' => array( 'UrlPendek' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'ShortUrl' => array( 'Kuerz_URL' ),
);

/** Macedonian (Македонски) */
$specialPageAliases['mk'] = array(
	'ShortUrl' => array( 'КраткаUrl' ),
);

/** Nedersaksisch (Nedersaksisch) */
$specialPageAliases['nds-nl'] = array(
	'ShortUrl' => array( 'Kort_webadres' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'ShortUrl' => array( 'KorteURL' ),
);

/** Traditional Chinese (‪中文(繁體)‬) */
$specialPageAliases['zh-hant'] = array(
	'ShortUrl' => array( '縮寫的URL' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;