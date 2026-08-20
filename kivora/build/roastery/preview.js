const { loadPlaywright } = require( 'c:/xampp/htdocs/kivora/wp-content/themes/Kivora/tests/visual/playwright.js' );
async function main() {
	const { chromium } = loadPlaywright();
	const browser = await chromium.launch();
	const context = await browser.newContext( { viewport: { width: 1440, height: 810 }, deviceScaleFactor: 1 } );
	const page = await context.newPage();
	await page.goto( 'http://localhost/kivora-demo/', { waitUntil: 'networkidle' } );
	await page.screenshot( { path: process.argv[ 2 ] } );
	await browser.close();
}
main();
