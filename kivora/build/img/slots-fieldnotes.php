<?php
/**
 * Fieldnotes: slot => search variants, tried in order until the sheet is full.
 *
 * @return array<string, array<int, string>>
 */

return array(
	'walk-footpath' => array( 'hiking trail hills', 'countryside path', 'trail landscape' ),
	'morning-mist'  => array( 'foggy field morning', 'mist landscape', 'fog trees' ),
	'desk-notebook' => array( 'notebook pen desk', 'journal writing', 'notebook coffee' ),
	'coast-cliffs'  => array( 'coastal cliffs ocean', 'sea cliffs', 'rocky coast' ),
	'winter-woods'  => array( 'winter forest path', 'snowy woods', 'forest winter' ),
	'stone-wall'    => array( 'stone wall countryside', 'old stone wall', 'rural fence field' ),
	'rain-window'   => array( 'rain window drops', 'rainy window', 'water droplets glass' ),
	'river-dusk'    => array( 'river sunset', 'lake evening light', 'estuary water dusk' ),
	'about-walking' => array( 'hiker backpack trail', 'person walking nature', 'walking outdoors' ),
	// No portrait slot: the CC0 portraits on offer are identifiable people, and
	// a shipped theme demo should not carry someone's face around the world.
	// The About page wants a place, not a desk: every CC0 "writing desk" is a
	// laptop on white melamine, which belongs to a different website entirely.
	'about-field'   => array( 'moorland heather', 'field gate countryside', 'meadow hills path' ),
);
