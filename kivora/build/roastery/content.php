<?php
/**
 * The demo's writing, kept apart from the machinery that installs it.
 *
 * Brackenmoor Roastery is a small coffee roastery: two house blends, a rotating
 * single origin, a van, and about a hundred and forty wholesale accounts. Every
 * word here is written for that business — the spec forbids lorem ipsum, and a
 * buyer judges a demo by whether the copy sounds like a real company
 * (05-ux-and-quality/16-demo-content.md §5).
 *
 * Everything is fictional: the roastery, the town of Brackenmoor, the people,
 * the customers and the prices.
 *
 * The alt text describes the photograph that is actually there, and several
 * captions were written after the picture was chosen rather than before — which
 * is the right way round. See build/README.md.
 */

return array(

	'site' => array(
		'title'       => 'Brackenmoor Roastery',
		'description' => 'Coffee roasted in Brackenmoor for cafés, offices and anyone who buys a bag',
	),

	'images' => array(
		'hero-roastery' => array(
			'title' => 'Beans off the roaster',
			'alt'   => '',
		),
		'about-roasting' => array(
			'title' => 'Sorting a new lot by hand',
			'alt'   => '',
		),
		'services-cupping' => array(
			'title' => 'Tasting a new lot',
			'alt'   => '',
		),
		'contact-shopfront' => array(
			'title' => 'Fold Lane',
			'alt'   => '',
		),
		'work-corner-cafe' => array(
			'title' => 'The Corner House',
			'alt'   => '',
		),
		'work-bakery' => array(
			'title' => 'Fold Lane Bakery',
			'alt'   => '',
		),
		'work-office-kitchen' => array(
			'title' => 'Kestrel Works',
			'alt'   => '',
		),
		'work-deli' => array(
			'title' => 'Meadow Lane Deli',
			'alt'   => '',
		),
		'work-market-stall' => array(
			'title' => 'Brackenmoor Market',
			'alt'   => '',
		),
		'work-hotel-bar' => array(
			'title' => 'The Harbour Rooms',
			'alt'   => '',
		),
		'blog-green-beans' => array(
			'title' => 'A sack, as it arrives',
			'alt'   => '',
		),
		'blog-grinder' => array(
			'title' => 'Ground coffee',
			'alt'   => '',
		),
		'blog-kettle' => array(
			'title' => 'Pouring water',
			'alt'   => '',
		),
		'blog-latte' => array(
			'title' => 'Milk, poured',
			'alt'   => '',
		),
		'blog-farm' => array(
			'title' => 'Where it grows',
			'alt'   => '',
		),
		'blog-espresso' => array(
			'title' => 'An espresso, pulled',
			'alt'   => '',
		),
		'brackenmoor-roastery-logo' => array(
			'title' => 'Brackenmoor Roastery',
			'alt'   => '',
		),
		'brackenmoor-roastery-icon' => array(
			'title' => 'Brackenmoor Roastery site icon',
			'alt'   => '',
		),
		'client-corner-house' => array(
			'title' => 'The Corner House',
			'alt'   => 'The Corner House',
		),
		'client-fold-lane' => array(
			'title' => 'Fold Lane Bakery',
			'alt'   => 'Fold Lane Bakery',
		),
		'client-kestrel-works' => array(
			'title' => 'Kestrel Works',
			'alt'   => 'Kestrel Works',
		),
		'client-meadow-lane' => array(
			'title' => 'Meadow Lane Deli',
			'alt'   => 'Meadow Lane Deli',
		),
		'client-harbour-rooms' => array(
			'title' => 'The Harbour Rooms',
			'alt'   => 'The Harbour Rooms',
		),
	),

	'categories' => array(
		'roasting' => array( 'Roasting', 'What happens in the drum, and why we do it the way we do.' ),
		'brewing'  => array( 'Brewing', 'Getting it right on a machine that is not ours.' ),
		'sourcing' => array( 'Sourcing', 'Where the coffee comes from, and what we pay for it.' ),
	),

	'posts' => array(

		array(
			'slug'     => 'what-a-roast-date-actually-tells-you',
			'title'    => 'What a roast date actually tells you',
			'category' => 'roasting',
			'tags'     => array( 'freshness', 'roast date', 'wholesale' ),
			'image'    => 'blog-espresso',
			'sticky'   => true,
			'date'     => '2026-08-04 08:15:00',
			'excerpt'  => 'Fresher is not automatically better, and a bag roasted this morning will fight you. Here is the window we aim for, and why it is a window rather than a date.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">Everyone in this trade prints a roast date and almost nobody explains what to do with it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Coffee comes off the roaster full of carbon dioxide. For the first few days it is still letting it go, and that gas gets in the way: on espresso the puck erupts and channels, the shot runs fast and thin, and the result is flat in a way people usually blame on the grinder. Give it a week and the same coffee is a different drink.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">The window</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><strong>Days 1–4.</strong> Too gassy for espresso. Fine on filter if you are patient with it.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Days 5–21.</strong> Where we want it to be when you pull a shot. This is the whole reason we roast twice a week rather than monthly.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Days 22–40.</strong> Still good, quietly losing the top of the aroma. Most shops are drinking here without knowing it.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>After six weeks.</strong> Not stale exactly, but flat, and no grind adjustment brings it back.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Which is why we ask what you get through</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>When somebody opens an account the first question is not what they want, it is how many kilos a week they move. A shop doing 8 kg wants a weekly drop of 8 kg, not a monthly drop of 35 — even though the monthly drop is one delivery instead of four and would suit our van considerably better.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Bags are valve-sealed and one kilogram, which is small enough that an average shop opens a fresh one every day or two. Once it is open the clock speeds up: keep it sealed, keep it out of the fridge, and do not decant a week of coffee into a hopper because it looks tidier.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">And why we will sometimes make you wait</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We do not roast to stock. If you order 20 kg on a Wednesday it goes on Friday\'s roast and reaches you on Saturday, rather than coming out of a bin that was filled a fortnight ago. Occasionally that is inconvenient. It is also the only version of this where the date on the bag means anything.</p>
<!-- /wp:paragraph -->',
		),

		array(
			'slug'     => 'your-water-is-probably-the-problem',
			'title'    => 'Your water is probably the problem',
			'category' => 'brewing',
			'tags'     => array( 'water', 'espresso', 'machines' ),
			'image'    => 'blog-kettle',
			'sticky'   => false,
			'date'     => '2026-07-16 09:40:00',
			'excerpt'  => 'The same coffee, the same grinder and the same machine will taste different in two shops four streets apart. Nine times out of ten the difference is what comes out of the tap.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">A shot is about 98% water, and nobody ever tastes the water on its own before deciding the coffee is wrong.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Two things in water matter here. <strong>Hardness</strong> — mostly calcium and magnesium — is what actually extracts flavour, and what furs up a boiler. <strong>Alkalinity</strong> is the water\'s ability to neutralise acid, and it is the one that quietly ruins coffee: high alkalinity flattens everything, and a bright Kenyan arrives tasting of cardboard.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Brackenmoor is on a hard supply. Four miles up the valley it is soft. Same roast, same grinder, entirely different drink — and both shops assumed the difference was us.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What to do about it</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list">
<!-- wp:list-item -->
<li>Get a test kit. Six pounds, thirty seconds, and it ends the argument.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>If alkalinity is high you want a filter that reduces it, not a plain softener — a softener will protect the boiler and leave the taste exactly where it was.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Change the cartridge on volume rather than on how it tastes. By the time you can taste it you have been serving it for a month.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Write the date on the filter head in marker. Everybody says they will remember.</li>
<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>We check the water when we dial in a new account, and we will tell you when the answer is a filter rather than a different blend. It is a duller recommendation and it costs us a bag a week. It also fixes the actual problem.</p>
<!-- /wp:paragraph -->',
		),

		array(
			'slug'     => 'why-we-do-not-sell-a-dark-roast',
			'title'    => 'Why we do not sell a dark roast',
			'category' => 'roasting',
			'tags'     => array( 'roast profile', 'blends', 'opinions' ),
			'image'    => 'blog-latte',
			'sticky'   => false,
			'date'     => '2026-06-25 07:50:00',
			'excerpt'  => 'We get asked for one most weeks. The honest answer is that what people want from a dark roast is body and sweetness, and burning it is a poor way to get either.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">"Have you got something darker?" usually means "the last one was thin and sour". Those are different problems.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Roast development is not a dial between weak and strong. Take a bean far enough and the sugars stop caramelising and start carbonising: you get bitterness and a smell of burnt toast, and the thing people actually wanted — weight in the mouth, sweetness that survives milk — has gone the other way.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we do instead</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The house blend is two thirds Brazilian and a third Colombian, taken to the end of first crack and held there. That gives the body and the chocolate note without the ash. If a shop wants more weight in a flat white we change the blend proportion or the grind, not the colour.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>There is one exception, and we are honest about it: if you are serving coffee through a superautomatic in an office where nobody will ever adjust anything, a slightly darker roast is more forgiving of a machine that cannot be dialled in. We will roast that as a special. We just will not put it on the price list and pretend it is better.</p>
<!-- /wp:paragraph -->',
		),

		array(
			'slug'     => 'what-we-pay-for-coffee',
			'title'    => 'What we pay for coffee, and what that buys',
			'category' => 'sourcing',
			'tags'     => array( 'pricing', 'sourcing', 'importers' ),
			'image'    => 'blog-farm',
			'sticky'   => false,
			'date'     => '2026-05-19 10:25:00',
			'excerpt'  => 'A roastery our size does not fly anywhere or sign a contract with a farm. Here is the actual chain, the actual numbers, and what the words on the front of a bag are worth.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">We buy about eleven tonnes of green coffee a year. That is far too little to buy at origin, and far too much to be vague about.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Everything comes through two importers who hold stock in a bonded warehouse near the coast. They buy the lot, they publish what they paid the exporter, and we buy sacks off them at a price that includes shipping, warehousing and their margin. We have met one producer, once, at a trade show. Anybody our size telling you they have a relationship with a farm is describing their importer\'s relationship with a farm.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">The numbers, for one sack</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>A 60 kg sack of the Brazilian in the house blend costs us about £310 landed — £5.20 a kilo, green.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Roasting drives off between 15% and 18% of the weight in water, so that kilo is really about £6.30 by the time it is roasted.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Bags, valves, labels, gas, electricity, rent, the van and four wages take it to somewhere near £15.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>We sell it wholesale at £19.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>The single origins run from £7.40 to £11 a kilo green, which is why they are £26 rather than £19. When a lot is genuinely expensive we say what it cost rather than reaching for an adjective.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">On the words</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>"Direct trade" has no definition and no auditor. "Speciality" means a grader scored it above eighty, which most coffee sold in this country would clear. The certification marks do mean something specific, and we carry them where a lot has them and say nothing where it does not.</p>
<!-- /wp:paragraph -->',
		),

		array(
			'slug'     => 'the-grinder-is-the-cheapest-upgrade',
			'title'    => 'The grinder is the cheapest upgrade you are not making',
			'category' => 'brewing',
			'tags'     => array( 'grinders', 'equipment', 'espresso' ),
			'image'    => 'blog-grinder',
			'sticky'   => false,
			'date'     => '2026-04-11 08:05:00',
			'excerpt'  => 'Shops spend four thousand pounds on a machine and eight hundred on the thing that decides how the coffee tastes. Burrs wear out, and worn burrs cannot be dialled around.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">If you can only fix one thing in a shop, fix the grinder.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>An espresso machine\'s job is to hold a temperature and push water at a pressure. A grinder\'s job is to decide the size of every particle, and the spread of those sizes is most of what you taste. A machine that is slightly off makes a shot that is slightly off. A grinder that is worn makes a shot you cannot fix.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Burrs are a consumable</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Flat burrs are done somewhere between 500 and 800 kg depending on the set. Conical burrs last longer and fail more gradually, which is worse, because nobody notices. A shop doing 8 kg a week is through 400 kg a year, so two years is a set.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The symptom is not a bad shot. It is that the shots stop being the same as each other: same dose, same setting, twenty-six seconds and then thirty-four. Everybody blames the coffee, then the barista, then the coffee again.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we do about it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Accounts have their throughput tracked, and when a set is due we say so, fit them, and dial the machine back in on the same visit. Burrs are charged at cost. That is not generosity — a shop serving inconsistent shots off our coffee costs us more than a set of burrs does.</p>
<!-- /wp:paragraph -->',
		),

		array(
			'slug'     => 'a-lot-that-did-not-work',
			'title'    => 'A lot that did not work, and what we did with it',
			'category' => 'sourcing',
			'tags'     => array( 'sourcing', 'mistakes', 'quality' ),
			'image'    => 'blog-green-beans',
			'sticky'   => false,
			'date'     => '2026-03-02 11:00:00',
			'excerpt'  => 'We bought six sacks of a washed Ethiopian on the strength of a sample, and what arrived was not what we tasted. Here is what went wrong, and what happened to the coffee.',
			'content'  => '<!-- wp:paragraph {"fontSize":"h6"} -->
<p class="has-h-6-font-size">This is the post most roasteries do not write, which is exactly why it is worth writing.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>In November we cupped a washed Ethiopian off a sample and liked it enough to take six sacks — nearly a fifth of a quarter\'s single-origin buying for a shop our size. What arrived cupped flat and faintly of cardboard. Same lot number, same warehouse, four weeks later.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What went wrong</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Almost certainly moisture and time. Green coffee is not stable; a delicate washed lot with high moisture content fades faster than a dense one, and this had sat in a warehouse through a mild, damp autumn. The sample had been drawn in September. We tasted September and bought November.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we did</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Not put it on the shelf as a single origin, which was the tempting option — most customers would not have complained, and some would have blamed themselves. We told the importer, who took two sacks back and gave us credit on a third. The rest went into a blend for the office accounts, where it does an honest job with milk, and the bag said so.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What changed</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We now ask for the sample draw date rather than just the lot, and for anything delicate we buy two sacks and go back instead of taking six at once. It costs a little more per kilo and it has already saved us this once.</p>
<!-- /wp:paragraph -->',
		),
	),

	'comments' => array(
		array(
			'post'   => 'what-a-roast-date-actually-tells-you',
			'author' => 'Priya Nandakumar',
			'email'  => 'priya@example.com',
			'date'   => '2026-08-05 20:30:00',
			'text'   => 'We moved from a monthly delivery to weekly after reading something like this, and the difference on espresso was immediate. The awkward part was persuading everyone that a smaller, more frequent order was not more expensive.',
		),
		array(
			'post'   => 'what-a-roast-date-actually-tells-you',
			'author' => 'Nell Faraday',
			'email'  => 'nell@brackenmoorroastery.example',
			'date'   => '2026-08-06 07:45:00',
			'text'   => 'It is not, and it is worth saying plainly: the price per kilo is the same either way. The only thing a monthly drop saves is our diesel, which is why we do not push it.',
		),
		array(
			'post'   => 'the-grinder-is-the-cheapest-upgrade',
			'author' => 'Tomás Beltrán',
			'email'  => 'tomas@example.com',
			'date'   => '2026-04-14 18:20:00',
			'text'   => 'The bit about shots drifting apart rather than simply being bad is the clearest description of worn burrs I have read. We had exactly that for six months and replaced two other things first.',
		),
	),
);
