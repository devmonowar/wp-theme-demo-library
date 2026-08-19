<?php
/**
 * Every word of Demo 2, in one place.
 *
 * "Fieldnotes" -- a personal journal of walking, writing and photographs. Demo 2
 * of the two v1 demos (07-ai-and-roadmap/03-v1-demo-strategy.md): the niche,
 * text-led one, deliberately smaller and quieter than Creative Studio.
 *
 * Alt text is written from the photograph that actually landed in each slot, not
 * from the slot's name. Image keys map to img/build-assets.php.
 *
 * @return array<string, mixed>
 */

return array(

	'site' => array(
		'title'       => 'Fieldnotes',
		'tagline'     => 'Walking, writing, and paying attention',
		'author_name' => 'Rowan Ellis',
		'author_bio'  => 'Rowan Ellis writes about walking and the places walking leads to. Most of what is here started as a note made standing up, in weather, and was finished later at a desk with the coat still on the back of the chair.',
	),

	/*
	 * Categories. Three is enough to show the archive working without turning
	 * the sidebar into a filing cabinet.
	 */
	'categories' => array(
		'walks'       => array(
			'name'        => 'Walks',
			'description' => 'Routes, and what turned up on them.',
		),
		'notebook'    => array(
			'name'        => 'Notebook',
			'description' => 'Notes on writing, kit, and working outdoors.',
		),
		'photographs' => array(
			'name'        => 'Photographs',
			'description' => 'Pictures that did not need a thousand words.',
		),
	),

	'tags' => array( 'walking', 'weather', 'winter', 'coast', 'writing', 'notebooks', 'light', 'rivers' ),

	/*
	 * Posts. One sticky, one with a comment thread, all with excerpts and a
	 * featured image -- the combination the blog templates actually have to
	 * render.
	 */
	'posts' => array(

		'reservoir' => array(
			'title'    => 'The long way round to the reservoir',
			'category' => 'walks',
			'tags'     => array( 'walking', 'weather' ),
			'image'    => 'walk-footpath',
			'sticky'   => true,
			'excerpt'  => 'There is a direct path and there is the one along the top. The direct path is forty minutes shorter and I have taken it perhaps twice.',
			'content'  => array(
				'There is a direct path to the reservoir and there is the one that goes along the top. The direct path is forty minutes shorter. I have taken it perhaps twice, both times in rain heavy enough to make the decision for me.',
				'The long way starts badly, up a lane with nothing to look at but hedge, and for ten minutes you wonder why you bothered. Then the hedge stops. The whole valley is there at once, and the reservoir at the bottom of it looking like something spilled.',
				'I have never managed to photograph that moment. The picture always comes out as a field with some water in it, which is accurate and completely wrong.',
				'What the long way gives you is time to stop thinking about the thing you were thinking about when you set off. By the top of the lane it is still there. By the second gate it has usually gone, and something more interesting has taken its place -- most often a sentence, arriving whole, for a piece I had given up on.',
				'I have started carrying the notebook in a pocket rather than the bag for exactly this reason. A sentence that has to wait for a rucksack to come off is a sentence that does not survive.',
			),
			'comments' => array(
				array(
					'author'  => 'Ines Vogel',
					'content' => 'The bit about the sentence not surviving the rucksack is exactly right. I keep a card in my sleeve pocket for the same reason and everyone thinks it is very strange.',
				),
				array(
					'author'  => 'Rowan Ellis',
					'is_site' => true,
					'content' => 'A card in the sleeve is better than what I do, which is write on the back of the map until the map is unusable.',
				),
				array(
					'author'  => 'Peter Lammert',
					'content' => 'Walked this route last spring on your recommendation and took the short way. You are right, it is a field with some water in it.',
				),
			),
		),

		'what-i-carry' => array(
			'title'    => 'What I carry',
			'category' => 'notebook',
			'tags'     => array( 'notebooks', 'writing' ),
			'image'    => 'desk-notebook',
			'excerpt'  => 'A short list, arrived at slowly, mostly by leaving things at home and not missing them.',
			'content'  => array(
				'This list took about six years to get short. Everything on it earned its place by being missed on the one walk I left it behind.',
				'A pocket notebook, unlined. Lined paper makes me write sentences; unlined paper lets me write a shape of a hill and three words beside it, which is more often what I want at the time.',
				'One pencil, and one spare, because a pencil works wet and a pen does not. This is the whole argument and it is not close.',
				'A camera small enough that carrying it is not a decision. The best camera is the one you have, and the second best is the one you left at home because it was heavy.',
				'Everything else -- the flask, the spare socks, the map in a case -- changes with the season. The three above have not changed in years, which is how I know they are the list.',
			),
		),

		'fog-ridge' => array(
			'title'    => 'Fog on the ridge, and what it hides',
			'category' => 'walks',
			'tags'     => array( 'walking', 'weather', 'winter' ),
			'image'    => 'morning-mist',
			'excerpt'  => 'A view you cannot see is still a view. It is just doing something else with your attention.',
			'content'  => array(
				'Twice this month the ridge has been under fog by the time I got up to it, and both times I stayed longer than I would have on a clear day.',
				'A view you cannot see is still a view. It is just doing something else with your attention. Without the far distance to look at you start noticing what is within ten feet: the way wet grass lies down in one direction, a fence post with the grain raised by sixty winters, your own breath going somewhere.',
				'Sound changes too. Fog does not actually muffle very much, but it takes away the visual explanation for every noise, so a sheep two fields off arrives as a genuine surprise.',
				'I came down the same way both times and could not have told you afterwards whether the path was steep.',
			),
		),

		'writing-outdoors' => array(
			'title'    => 'Notes on writing outdoors',
			'category' => 'notebook',
			'tags'     => array( 'writing', 'notebooks', 'weather' ),
			'image'    => 'rain-window',
			'excerpt'  => 'It is not romantic and it is not comfortable, and it produces work I cannot get any other way.',
			'content'  => array(
				'People imagine a bench with a view. What actually happens is standing up, half in the lee of a wall, writing four words with cold hands and putting the notebook away again.',
				'It is not romantic and it is not comfortable. It also produces work I cannot get any other way, because the note has to be short, and a short note is forced to be about the thing rather than around it.',
				'At a desk I can write three paragraphs approaching a subject. In a field I get one line, and the line is usually the subject.',
				'The transcription afterwards matters as much as the note. I copy them out the same evening, indoors, while the weather is still attached to the handwriting. Left a week, a fieldnote becomes a stranger\'s shopping list.',
			),
		),

		'stone-wall' => array(
			'title'    => 'A wall built by someone else',
			'category' => 'walks',
			'tags'     => array( 'walking' ),
			'image'    => 'stone-wall',
			'excerpt'  => 'Nobody signs a wall. You can still tell, after a mile of it, roughly what kind of day they were having.',
			'content'  => array(
				'The wall runs for something over a mile along the top field and then simply stops, in the middle of nothing, as if the money did.',
				'Nobody signs a wall. You can still tell, after a mile of it, roughly what kind of day the builder was having: there is a stretch near the gate where the stones are chosen with obvious care, and a stretch further on where they are chosen with obvious speed.',
				'It has stood either way. That is the part I keep coming back to. The careful stretch and the hurried stretch are both still there, holding the same field in, a hundred and some years after the argument about how much care was warranted.',
			),
		),

		'year-end-light' => array(
			'title'    => 'Light at the end of the year',
			'category' => 'photographs',
			'tags'     => array( 'light', 'winter' ),
			'image'    => 'winter-woods',
			'excerpt'  => 'For about three weeks the sun never gets properly up, and everything it touches looks like it is being told something.',
			'content'  => array(
				'For about three weeks either side of the shortest day the sun never gets properly up. It comes in sideways through the trees all afternoon, and everything it touches looks like it is being told something.',
				'These are the easiest photographs of the year to take and the hardest to take well. The light does so much of the work that you can point the camera almost anywhere and get something that looks competent, which is a trap.',
				'The ones I keep are nearly always the ones where I waited for something to happen in the light rather than photographing the light itself.',
			),
		),

		'rain-excuse' => array(
			'title'    => 'Rain, and the excuse it gives',
			'category' => 'notebook',
			'tags'     => array( 'weather', 'writing' ),
			'image'    => 'coast-cliffs',
			'excerpt'  => 'Bad weather is the only reliable way I have found to make an afternoon indoors feel earned.',
			'content'  => array(
				'Bad weather is the only reliable way I have found to make an afternoon indoors feel earned, and I have stopped being embarrassed about needing that.',
				'The work gets done either way. But it gets done more easily when the alternative has been ruled out by something outside my control, which is a strange thing to admit about a job that consists entirely of choosing what to do next.',
				'So: an hour out in it first, however unpleasant, then the rest of the day at the desk with the coat drying in the hall. The hour is not for the walk. The hour is for the permission.',
			),
		),

		'estuary' => array(
			'title'    => 'The estuary in November',
			'category' => 'photographs',
			'tags'     => array( 'rivers', 'light', 'coast' ),
			'image'    => 'river-dusk',
			'excerpt'  => 'Twice a day the whole place changes its mind about whether it is land or water.',
			'content'  => array(
				'Twice a day the whole place changes its mind about whether it is land or water, and in November it does it in almost no light at all.',
				'I have walked out along the wall at the wrong end of the afternoon more times than is sensible. The tide comes across the flats faster than it looks -- not dangerously, where I go, but fast enough that a photograph taken on the way out is of a different place from the one taken on the way back.',
				'This is the only landscape I photograph where I do not mind the pictures being unclear. Unclear is what it is like.',
			),
		),
	),

	/*
	 * Pages. The homepage is assembled from the theme's own patterns; the rest
	 * are ordinary pages, which is the point of a demo like this.
	 */
	'pages' => array(

		'home' => array(
			'title'      => 'Home',
			'pattern'    => 'kivora/page-blog-home',
			// The pattern opens with its own headline; the page title above it
			// would be a second one saying only "Home".
			'hide_title' => true,
		),

		'about' => array(
			'title' => 'About',
			'image' => 'about-field',
			'lead'  => 'I have been walking the same twenty square miles for eleven years and writing about it for eight.',
			'body'  => array(
				'Fieldnotes is a journal, not a guide. There are no routes to download and no gear scores. What is here is what I noticed, written down as close to the noticing as I could manage.',
				'The walks are mostly within an hour of home, on purpose. Going far is easy to write about because everything is new; going the same way for the eleventh time and still finding something is the harder and more interesting problem, and it is the one this site is about.',
				'The photographs are taken on the same walks with a small camera. They are not illustrations of the writing and the writing is not a caption for them. Sometimes they are about the same day and disagree about it.',
				'If something here is useful to you, take it. If you want to tell me I have a hill\'s name wrong, please do -- it has happened twice and I was grateful both times.',
			),
		),

		'journal' => array(
			'title' => 'Journal',
			'body'  => array(),
		),

		'photographs' => array(
			'title'  => 'Photographs',
			'lead'   => 'A few from the last year, in no order that means anything.',
			'body'   => array(
				'Everything here was taken within a morning\'s walk of the house. Nothing has been moved, added or taken away, and the only adjustments are the ones a darkroom would have allowed.',
			),
			'closing' => 'Prints are not for sale, mostly because I have never got round to working out how. Ask if you want one anyway.',
		),

		'contact' => array(
			'title' => 'Contact',
			'lead'  => 'Corrections, recommendations, and arguments about paths are all welcome.',
			'body'  => array(
				'The quickest way to reach me is email, and I answer nearly all of it, though rarely on the day it arrives.',
				'I do not take sponsored posts, gear for review, or guest articles. This is not a policy I expect anyone to admire; it is just that the site is small enough to stay simple and I would like to keep it that way.',
			),
			'email'  => 'hello@example.com',
			'postal' => array( 'Fieldnotes', 'PO Box 41', 'Kendal', 'LA9 0AA' ),
		),

		'privacy' => array(
			'title' => 'Privacy policy',
			'body'  => array(
				'This site collects as little as it can.',
				'If you leave a comment, the name, email address and website you type into the form are stored, along with your comment and the date. The email address is not published and is not used for anything except replying to you.',
				'Comments may be checked against an automated spam service. Nothing here sets advertising cookies or shares anything with an advertising network.',
				'Server logs record the address of each request and are kept for thirty days.',
				'If you would like anything about you removed, write to the address on the contact page and it will be removed.',
			),
		),
	),

	/*
	 * Menus. Two locations, both filled -- the theme registers a footer location
	 * that a demo leaving it empty would never exercise.
	 */
	'menus' => array(
		'primary' => array(
			'name'  => 'Main Menu',
			'items' => array( 'home', 'journal', 'photographs', 'about', 'contact' ),
		),
		'footer'  => array(
			'name'  => 'Footer Menu',
			'items' => array( 'about', 'contact', 'privacy' ),
		),
	),

	/*
	 * Theme settings. Deliberately not the same as Creative Studio's: a reader
	 * comparing the two demos should be able to see that these controls do
	 * something. The sidebar sits on the left here and disappears on a single
	 * post, which is the arrangement a text-led blog actually wants.
	 */
	'settings' => array(
		'header_sticky'             => false,
		'header_transparent'        => false,
		'live_search_enabled'       => true,
		'sidebar_layout'            => 'left',
		'sidebar_layout_singular'   => 'none',
		'sidebar_sticky'            => true,
		'widget_title_visibility'   => true,
		'blog_show_excerpt'         => true,
		'blog_excerpt_length'       => 42,
		'blog_show_meta'            => true,
		'single_show_reading_time'  => true,
		'single_show_author_bio'    => true,
		'single_show_related_posts' => true,
		'single_show_share_links'   => true,
		// No fictional name here: this string lands in the footer of whoever
		// imports the demo, and it should read correctly on their site rather
		// than crediting someone who does not exist.
		'footer_copyright_text'     => '&copy; {year} {site_name} — words and photographs, free to reuse with credit.',
	),
);
