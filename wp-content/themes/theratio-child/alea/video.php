<?php
/**
 * ALEA — verified video + social profiles.
 *
 * Everything here was verified on the live site aleamodular.com (2026-08-06):
 * the video is published on ALEA's own YouTube channel and is already embedded
 * on the live homepage; the profile URLs are the ones the live footer links to.
 *
 * The player is a CLICK-TO-PLAY FACADE: nothing is requested from YouTube until
 * the visitor actually presses play. The poster frame is one of our own
 * catalogue photographs, so a page carries no third-party request, no cookie
 * and no layout shift on load. On click the iframe is injected against
 * youtube-nocookie.com.
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/images.php';

function alea_video() {
	return array(
		'id'       => 'oZRbPuTiJH8',                     // [SITE] embedded on the live homepage
		'title'    => 'How Do We Work!',                 // [SITE] YouTube oEmbed title
		'channel'  => 'Alea Modular Kitchen',            // [SITE] oEmbed author
		'duration' => '1:27',                            // [SITE] schema duration PT1M27S
	);
}

/** Verified profile URLs — used for schema sameAs. */
function alea_social_profiles() {
	return array(
		'https://www.facebook.com/aleamodularkitchen',
		'https://www.instagram.com/aleamodularkitchen/',
		'https://www.youtube.com/channel/UC7_2pqYCC8WrASfm8u3cuow',
		'https://twitter.com/AleaModular',
	);
}

/**
 * Render the click-to-play video facade.
 *
 * @param string $poster_key images.php key used for the still frame.
 * @param string $caption    optional line under the player.
 */
function alea_video_embed( $poster_key = 'kitchen-island', $caption = '' ) {
	$v = alea_video();
	if ( empty( $v['id'] ) ) {
		return '';
	}
	$poster = alea_img_src( $poster_key );
	if ( '' === $poster ) {
		return '';
	}
	$id  = preg_replace( '/[^A-Za-z0-9_-]/', '', $v['id'] );
	$out  = '<figure class="ax-media axp-video">';
	$out .= '<button type="button" class="axp-video__btn" data-yt="' . esc_attr( $id ) . '"'
		. ' aria-label="' . esc_attr( 'Play video: ' . $v['title'] . ' (' . $v['duration'] . ')' ) . '"'
		. ' style="background-image:url(' . esc_url( $poster ) . ')">';
	$out .= '<span class="axp-video__play" aria-hidden="true">'
		. '<svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M8 5v14l11-7z"/></svg>'
		. '</span>';
	$out .= '<span class="axp-video__meta"><span class="ax-mono">' . esc_html( $v['duration'] ) . '</span> '
		. esc_html( $v['title'] ) . '</span>';
	$out .= '</button>';
	if ( $caption ) {
		$out .= '<figcaption class="ax-media__caption">' . esc_html( $caption ) . '</figcaption>';
	}
	$out .= '</figure>';
	return $out;
}

/** Styles + behaviour for the facade. Print once per page that uses it. */
function alea_video_assets() {
	static $done = false;
	if ( $done ) {
		return '';
	}
	$done = true;
	ob_start();
	?>
<style data-no-optimize="1">
.axp-video{margin:0}
.axp-video__btn{position:relative;display:block;width:100%;aspect-ratio:16/9;border:0;padding:0;cursor:pointer;
	background-size:cover;background-position:center;border-radius:3px;overflow:hidden}
.axp-video__btn::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,17,15,.15),rgba(20,17,15,.55))}
.axp-video__play{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:2;
	width:64px;height:64px;border-radius:50%;background:#92003B;color:#fff;display:grid;place-items:center;
	transition:transform .18s ease}
.axp-video__btn:hover .axp-video__play{transform:translate(-50%,-50%) scale(1.06)}
.axp-video__meta{position:absolute;left:14px;bottom:12px;z-index:2;color:#F4F4F1;text-align:left;
	font:600 .8125rem/1.3 -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
.axp-video__frame{width:100%;aspect-ratio:16/9;border:0;display:block;border-radius:3px}
@media (prefers-reduced-motion:reduce){.axp-video__btn:hover .axp-video__play{transform:translate(-50%,-50%)}}
</style>
<script data-no-optimize="1" data-no-defer="1">
(function(){
	try{
		document.addEventListener('click', function(e){
			var b = e.target && e.target.closest ? e.target.closest('.axp-video__btn') : null;
			if (!b || !b.dataset || !b.dataset.yt) { return; }
			var f = document.createElement('iframe');
			f.className = 'axp-video__frame';
			f.setAttribute('allow','accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
			f.setAttribute('allowfullscreen','');
			f.setAttribute('title', b.getAttribute('aria-label') || 'Video');
			f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(b.dataset.yt) + '?autoplay=1&rel=0';
			if (b.parentNode) { b.parentNode.replaceChild(f, b); }
		}, false);
	}catch(err){ /* leave the poster in place */ }
})();
</script>
	<?php
	return (string) ob_get_clean();
}
