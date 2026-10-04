<?php
/**
 * Creative Studio refresh: the file shipped for each slot, and its alt text.
 *
 * Every choice was made by looking at the contact sheet in sheets/; the three
 * rawpixel picks were re-checked full-size for the provider's watermark and
 * are clean. Alt text describes the photograph that is actually there.
 *
 * inner-6 borrows work-case1's sheet with 'from'.
 *
 * @return array<string, array{n:int, alt:string, from?:string}>
 */

return array(
	'landing-1' => array( 'n' => 5, 'alt' => 'Four colleagues working on laptops around a wooden table', 'from' => 'studio-hero' ),
	'landing-2' => array( 'n' => 1, 'alt' => 'Hand sketching wireframes in a notebook on a wooden desk', 'from' => 'studio-desk' ),
	'landing-3' => array( 'n' => 6, 'alt' => 'Team gathered around laptops, seen from above', 'from' => 'studio-meet' ),
	'landing-4' => array( 'n' => 2, 'alt' => 'Developer working at a desktop monitor, seen from behind', 'from' => 'studio-code' ),
	'landing-5' => array( 'n' => 3, 'alt' => 'Desk flatlay with keyboard, phone and notebook', 'from' => 'studio-detail' ),
	'inner-1'   => array( 'n' => 1, 'alt' => 'Project timeline with sticky notes on a whiteboard', 'from' => 'svc-strategy' ),
	'inner-2'   => array( 'n' => 1, 'alt' => 'Creative desk with lamp, speakers and guitar', 'from' => 'svc-design' ),
	'inner-3'   => array( 'n' => 2, 'alt' => 'Laptop showing code on a desk', 'from' => 'svc-build' ),
	'inner-4'   => array( 'n' => 2, 'alt' => 'Laptop, notebook and coffee on a wooden desk, top view', 'from' => 'work-case1' ),
	'inner-5'   => array( 'n' => 1, 'alt' => 'Phone resting on wireframe sketches beside a green marker', 'from' => 'work-case2' ),
	'inner-6'   => array( 'n' => 4, 'alt' => 'Person reviewing analytics charts on a laptop', 'from' => 'work-case1' ),
	'inner-7'   => array( 'n' => 2, 'alt' => 'Colourful sticky notes arranged on a wall', 'from' => 'work-tall' ),
	'inner-8'   => array( 'n' => 3, 'alt' => 'Hand pointing at red colour swatches', 'from' => 'about-story' ),
);
