<?php

class slideshow
{
	function slideshow()
	{

	}

	function home_slides()
	{
		// get the current link to mysql
		$db = my::database();

		$result = 'no slides';

		// lets get 5 posts in the featured category
		// The Query
		$the_query = new WP_Query( 'category_name=featured' );

		if( $the_query->have_posts() )
		{
			$result = array();
			// The Loop
			while ( $the_query->have_posts() ) : $the_query->the_post();
				// probably there would be an easier way ...
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

				$result[get_the_ID()]['title'] = get_the_title();
				$result[get_the_ID()]['desc'] = get_the_excerpt();
				$result[get_the_ID()]['perma'] = get_permalink();
				$result[get_the_ID()]['attachment'] = $image[0];
				
				// a sample of using written sql
				// the get_guid querry is written in the slideshow_sql file
				// the fetch_ prefix is a keyword for the mysql lib to return the first value
				// which is very useful for retrieving single value results without looping a one element array
				$result[get_the_ID()]['guid'] = $db->fetch_get_guid(get_the_ID());
			endwhile;
		}

		// Reset Post Data
		wp_reset_postdata();
		wp_reset_query();


		return $result;
	}
}