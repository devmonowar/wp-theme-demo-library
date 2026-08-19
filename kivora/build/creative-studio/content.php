<?php
/**
 * The demo's writing, kept apart from the machinery that installs it.
 *
 * Northline Studio is a small web studio: three people, project work, a
 * maintenance retainer. Every word here is written for that studio — the
 * spec forbids lorem ipsum, and a buyer judges a demo by whether the copy
 * sounds like a real business (05-ux-and-quality/16-demo-content.md §5).
 */

return array(

	'site' => array(
		'title'       => 'Northline Studio',
		'description' => 'Web design and development for small teams',
	),

	'images' => array(
		'studio-team-reviewing-work' => array(
			'title' => 'The studio reviewing work in progress',
			'alt'   => 'Three colleagues around a table looking over printed page designs together',
		),
		'studio-planning-session' => array(
			'title' => 'Planning session at the wall',
			'alt'   => 'A woman at a whiteboard covered in sticky notes, talking through a plan with her colleagues',
		),
		'services-working-session' => array(
			'title' => 'A working session',
			'alt'   => 'Hands, notebooks and a laptop on a table during a working session',
		),
		'contact-city-street' => array(
			'title' => 'The street the studio is on',
			'alt'   => 'A city street lined with tall buildings on an overcast morning',
		),
		'team-mari-lindqvist' => array(
			'title' => 'Mari Lindqvist',
			'alt'   => '',
		),
		'team-tom-becker' => array(
			'title' => 'Tom Becker',
			'alt'   => '',
		),
		'team-daniel-reyes' => array(
			'title' => 'Daniel Reyes',
			'alt'   => '',
		),
		'blog-writing-the-brief' => array(
			'title' => 'Writing the brief',
			'alt'   => 'Someone writing notes in a paper notebook beside a cup of coffee',
		),
		'blog-drawings-and-laptop' => array(
			'title' => 'Drawings and a laptop',
			'alt'   => 'Hands typing on a laptop surrounded by printed technical drawings',
		),
		'blog-code-on-screen' => array(
			'title' => 'Code on screen',
			'alt'   => 'A screen of JavaScript in a dark editor theme',
		),
		'blog-workshop-notes' => array(
			'title' => 'Workshop notes',
			'alt'   => 'People sorting coloured sticky notes across a white table',
		),
		'blog-desk-tools' => array(
			'title' => 'Desk tools',
			'alt'   => 'A keyboard, notebook, pens and headphones arranged on a wooden desk',
		),
		'blog-quiet-office' => array(
			'title' => 'A quiet office',
			'alt'   => 'An empty desk by a window looking out over a city skyline',
		),
		'northline-studio-logo' => array(
			'title' => 'Northline Studio logo',
			'alt'   => 'Northline Studio',
		),
		'northline-studio-icon' => array(
			'title' => 'Northline Studio site icon',
			'alt'   => '',
		),
	),

	'categories' => array(
		'design'      => array( 'Design', 'How we make decisions about layout, type and colour.' ),
		'development' => array( 'Development', 'Front-end work, performance and the tools we keep.' ),
		'practice'    => array( 'Practice', 'How the studio runs: briefs, budgets and working with clients.' ),
	),

	'posts' => array(

		array(
			'slug'     => 'what-a-discovery-week-looks-like',
			'title'    => 'What a discovery week actually looks like',
			'category' => 'practice',
			'tags'     => array( 'process', 'discovery', 'clients' ),
			'image'    => 'blog-workshop-notes',
			'sticky'   => true,
			'date'     => '-6 days',
			'excerpt'  => 'Five days, three people, and one question: what is this site actually for? Here is how we spend the week, hour by hour.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>Every project we take on starts the same way: a week with no design in it. Clients are sometimes surprised by that, so it is worth writing down what the five days hold and why they earn their place in the budget.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Monday: the question behind the project</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We start with the reason someone picked up the phone. A new site is never the goal — the goal is more enquiries, fewer support emails, a product people can find. We write that sentence down and pin it to the wall, and it settles most of the arguments that follow.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Tuesday and Wednesday: the content you already have</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two days go into reading the existing site properly — every page, every PDF, every form. It is unglamorous and it is where the surprises live: the three service pages nobody links to, the FAQ that answers the question the sales team is asked most, the eight-year-old news post still taking search traffic.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>What each page is for, in one line.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>What we keep, what we rewrite, what we delete.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Who owns the words for each section after launch.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Thursday: the shape of it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Only now do we draw anything, and what we draw is a sitemap and a set of page outlines — headings, in order, with a sentence about what each one has to do. If the outline does not read well, no amount of layout will save the page.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Friday: the plan and the price</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We finish the week with a plan that fits the budget rather than a wish list that does not. Sometimes that means cutting a section everyone liked on Monday. It is a much cheaper place to make that decision than halfway through build.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>The week that feels like a delay is the week that stops the project drifting for a month.</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->
HTML
		),

		array(
			'slug'     => 'designing-for-the-content-you-have',
			'title'    => 'Designing for the content you actually have',
			'category' => 'design',
			'tags'     => array( 'content', 'layout', 'typography' ),
			'image'    => 'blog-writing-the-brief',
			'date'     => '-13 days',
			'excerpt'  => 'A layout that only works with the perfect headline is not a layout. It is a photograph of one.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>Most designs are drawn with the best possible content: a six-word headline, a photograph shot for the space, three products with names of exactly the same length. Then the site launches, someone writes a fourteen-word headline, and the whole thing sags.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Design with the worst case in the room</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We keep a folder of the real thing: the longest product name, the client's actual staff photographs, the press release nobody edited. Every layout gets tested against those before it is signed off. A card that holds a two-line title and a four-line one without changing height is worth more than one that looks perfect in a mockup.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Let the type do the work</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A clear type scale removes most of the decisions. When headings, body text and captions have obvious sizes and spacing, an editor adding a page a year from now falls into the right pattern without being told. That is the real test of a design system: what happens when the designer has left the building.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Leave room for the boring pages</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Terms, delivery, accessibility statements, the page listing every branch — these get the least design attention and a surprising share of the traffic. We style them first, not last, so they never look like an afterthought.</p>
<!-- /wp:paragraph -->
HTML
		),

		array(
			'slug'     => 'a-page-weight-budget-you-can-keep',
			'title'    => 'A page-weight budget you can actually keep',
			'category' => 'development',
			'tags'     => array( 'performance', 'budgets', 'front-end' ),
			'image'    => 'blog-code-on-screen',
			'date'     => '-20 days',
			'excerpt'  => 'Setting a performance budget is easy. Keeping it eighteen months later, after four campaigns and a new tracking script, is the hard part.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>We give every project a page-weight budget on day one: how many kilobytes the home page is allowed to weigh, fully loaded, on a mid-range phone. Ours is usually under 150&nbsp;KB. Writing the number down is the easy half.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Budgets are spent by people, not by code</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sites rarely get slow in one commit. They get slow through a chat widget in March, a heat-map script in June, a video header for a campaign that ended in August and was never taken down. Nobody makes a bad decision; the sum of the decisions is bad.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we do instead</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>The budget lives in the handover document, with the name of the person who owns it.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Any new third-party script is a decision with a cost attached, in kilobytes and in milliseconds.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Every quarter we re-measure and send a one-page note. It takes twenty minutes and has saved several sites.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>None of this is clever. It is just the difference between a fast site at launch and a fast site at its second birthday.</p>
<!-- /wp:paragraph -->
HTML
		),

		array(
			'slug'     => 'choosing-type-that-survives',
			'title'    => 'Choosing type that survives real content',
			'category' => 'design',
			'tags'     => array( 'typography', 'accessibility' ),
			'image'    => 'blog-desk-tools',
			'date'     => '-27 days',
			'excerpt'  => 'System fonts, a modest scale, and line lengths that hold up on a phone in daylight.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>We ship most sites on system fonts. Not for austerity — because they arrive instantly, they render the way the reader's device intends, and they cover far more languages than a webfont subset ever does.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A scale with fewer steps</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Four heading sizes and one body size is enough for almost every site we build. Extra steps get used inconsistently, and inconsistency is what makes a page feel amateur long before anyone can say why.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Measure, contrast, and the daylight test</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Around 65 characters a line, comfortably over the AA contrast threshold, and readable on a phone held at arm's length outdoors. That last one is not in any specification, and it has caught more problems than any tool we own.</p>
<!-- /wp:paragraph -->
HTML
		),

		array(
			'slug'     => 'small-teams-short-feedback-loops',
			'title'    => 'Small teams, short feedback loops',
			'category' => 'practice',
			'tags'     => array( 'process', 'clients' ),
			'image'    => 'blog-drawings-and-laptop',
			'date'     => '-34 days',
			'excerpt'  => 'Why we show work every Thursday, even when it is unfinished — especially when it is unfinished.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>There are three of us. That is small enough that nobody can hide a problem for long, which turns out to be the main advantage of a studio this size.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Thursday show-and-tell</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every Thursday we put the work on a screen and walk the client through it: what moved, what did not, what we got wrong. Half an hour, no slides. Clients who were nervous about a fixed price stop being nervous around week two, because they can see exactly where the money went.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Unfinished on purpose</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Showing polished work invites polish-level feedback — colours, corners, wording. Showing rough work invites the feedback that actually matters: this section is in the wrong order, that page is for a customer we do not have. We would rather hear it in week two than week nine.</p>
<!-- /wp:paragraph -->
HTML
		),

		array(
			'slug'     => 'shipping-without-a-build-step',
			'title'    => 'Shipping a site without a build step',
			'category' => 'development',
			'tags'     => array( 'front-end', 'tooling', 'performance' ),
			'image'    => 'blog-quiet-office',
			'date'     => '-41 days',
			'excerpt'  => 'Modern CSS and native modules got good enough that a lot of projects no longer need a bundler at all.',
			'content'  => <<<'HTML'
<!-- wp:paragraph -->
<p>A few years ago every site we handed over came with a toolchain, and every toolchain came with an expiry date. Then the platform caught up: custom properties, nesting, container queries, native modules. For a site of this size, a bundler now mostly adds a thing that can break.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we gave up</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Minification, mostly, and a handful of conveniences. On a stylesheet this small the saving is measured in single-digit kilobytes, which is not worth a dependency tree that needs patching twice a year.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we got back</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A codebase the client's next developer can read in the browser, edit in place, and understand without installing anything. Eighteen months after handover that is worth more than any build-time optimisation we removed.</p>
<!-- /wp:paragraph -->
HTML
		),
	),

	'comments' => array(
		array(
			'post'   => 'what-a-discovery-week-looks-like',
			'author' => 'Priya Nair',
			'email'  => 'priya@example.com',
			'date'   => '-4 days',
			'text'   => 'We ran something close to this before our rebuild and the sitemap week paid for itself twice over. The part we skipped — writing down who owns each page after launch — is exactly the part that went wrong six months later.',
		),
		array(
			'post'   => 'what-a-discovery-week-looks-like',
			'author' => 'Mari Lindqvist',
			'email'  => 'mari@example.com',
			'date'   => '-3 days',
			'text'   => 'That ownership line is the one we add to every handover now. It takes a minute in the meeting and saves an argument a year later.',
		),
		array(
			'post'   => 'a-page-weight-budget-you-can-keep',
			'author' => 'Tom Becker',
			'email'  => 'tom@example.com',
			'date'   => '-18 days',
			'text'   => 'The quarterly re-measure is the whole trick. Nobody notices a site getting 20 KB heavier; everybody notices it being 400 KB heavier two years on.',
		),
	),
);
