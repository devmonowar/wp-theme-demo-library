const { loadPlaywright, VIEWPORTS, horizontalOverflow, collectErrors } = require( 'c:/xampp/htdocs/kivora/wp-content/themes/Kivora/tests/visual/playwright.js' );
const path = require( 'node:path' );
const fs = require( 'node:fs' );

const OUT = path.join( __dirname, 'shots' );
const BASE = 'http://localhost/kivora-demo';
const PAGES = {
	home: '/',
	services: '/services/',
	wholesale: '/services/wholesale-coffee/',
	stockists: '/stockists/',
	pricing: '/pricing/',
	about: '/about/',
	journal: '/journal/',
	post: '/what-a-roast-date-actually-tells-you/',
	category: '/category/brewing/',
	contact: '/contact/',
	privacy: '/privacy-policy/',
};

async function main() {
	const { chromium } = loadPlaywright();
	fs.mkdirSync( OUT, { recursive: true } );
	const browser = await chromium.launch();
	const only = process.argv[ 2 ];

	for ( const [ name, urlPath ] of Object.entries( PAGES ) ) {
		if ( only && only !== name ) continue;

		for ( const [ vp, viewport ] of Object.entries( VIEWPORTS ) ) {
			const context = await browser.newContext( { viewport } );
			const page = await context.newPage();
			const errors = collectErrors( page );
			const res = await page.goto( BASE + urlPath, { waitUntil: 'networkidle' } );
			// Scroll the whole page first: the theme lets WordPress add
			// loading="lazy", so a fullPage screenshot of an unscrolled page
			// shows blank gaps where images below the fold never loaded.
			await page.evaluate( async () => {
				for ( let y = 0; y < document.body.scrollHeight; y += 400 ) {
					window.scrollTo( 0, y );
					await new Promise( ( r ) => setTimeout( r, 60 ) );
				}
				window.scrollTo( 0, 0 );
			} );
			await page.waitForLoadState( 'networkidle' );

			const overflow = await horizontalOverflow( page );
			await page.screenshot( { path: path.join( OUT, `${ name }-${ vp }.png` ), fullPage: vp === 'desktop' } );
			const real = errors.filter( ( e ) => ! e.includes( 'favicon' ) );
			console.log( `${ res.status() === 200 && overflow === 0 && ! real.length ? 'PASS' : 'FAIL' } ${ name.padEnd( 10 ) } ${ vp.padEnd( 8 ) } HTTP ${ res.status() } overflow ${ overflow }px ${ real.length ? JSON.stringify( real.slice( 0, 2 ) ) : '' }` );
			await context.close();
		}
	}

	await browser.close();
}
main();
