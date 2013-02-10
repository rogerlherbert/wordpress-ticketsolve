<?php

class RHD_TS_UpcomingShows
{
	private $source;
	private $tag;
	private $category;
	private $count;
	private $interval;
	private $cache_name = 'rhd_ts_cache';

	function __construct($account, $tag, $category, $count, $interval)
	{
		$this->tag = $tag;
		$this->category = $category;
		$this->count = $count;
		$this->interval = $interval;

		$params = array();

		if ($tag != '') {
			$params['tag'] = $tag;
		}
		if ($category != '') {
			$params['category'] = $category;
		}

		$qs = (count($params) > 0) ? '?' . http_build_query($params, '', '&') : '';

		$this->source = 'https://'.$account.'.ticketsolve.com/shows.xml'.$qs;
	}

	public function get_shows()
	{
		// get cached version from wp_options to avoid pulling & parsing large XML file every time 
		$cache = json_decode(get_option($this->cache_name),true);
		if ($cache['timestamp'] >= time() - $this->interval) { return $cache['shows']; }

		// no good cache available, so create from scratch
		$doc = $this->get_from_XML($this->source);

		$xpath = new DOMXpath($doc);

		// find all shows for all venues
		$showlist = $xpath->query('//venues/venue/shows/show');

		// make an array with simple data for output
		$shows = array();

		// use the smaller of [total show count] or [requested limit]
		$limit = ($showlist->length > $this->count) ? $this->count : $showlist->length;

		// only process the first n shows according to limit above, NB this is in document or date order?
		for ($i=0; $i < $limit; $i++) {
			$show = $showlist->item($i);

			// basic template
			$thisshow = array(
			"id" => "",
			"name" => "",
			"url" => "",
			"venue" => "",
			"images" => array(),
			"tags" => array(),
			"category" => "",
			"event" => array()
			);

			// show ID
			$thisshow['id'] = trim($xpath->query(".", $show)->item(0)->getAttribute("id")); 

			// show title
			$thisshow['name'] = trim($xpath->query("./name", $show)->item(0)->nodeValue); 

			// booking page
			$thisshow['url'] = trim($xpath->query("./url", $show)->item(0)->nodeValue);

			// show venue
			$thisshow['venue'] = trim($xpath->query("../../name", $show)->item(0)->nodeValue); 

			// image URLs
			foreach($xpath->query("./images/image", $show) as $img_group) {
				$img = array();
				foreach($xpath->query("./url", $img_group) as $img_ele) {
					$img[$img_ele->getAttribute("size")] = $img_ele->nodeValue;
				}
				$thisshow['images'][] = $img;
			}

			// show tags
			foreach($xpath->query("./tags/tag", $show) as $tag_ele) {
				$thisshow['tags'][] = trim($tag_ele->nodeValue);
			}

			// show category
			$thisshow['category'] = trim($xpath->query("./event_category", $show)->item(0)->nodeValue); 

			// get event data only for first event of first 10 shows max
			// this requires pulling a separate XML file per show, so keep it reasonable
			if ($i < 10) {
				$thisshow['event'] = $this->get_event_from_XML(trim($xpath->query("./events/event/feed/url", $show)->item(0)->nodeValue));
			}

			$shows[] = $thisshow;
		}

		// cache results
		update_option($this->cache_name, json_encode(array('timestamp' => time(), 'shows' => $shows)));

		return $shows;
	}

	public function get_from_XML($url)
	{
		$doc = new DOMDocument();
		$doc->load($url, LIBXML_NOCDATA);

		return $doc;
	}

	public function get_event_from_XML($event_xml_url)
	{
		$doc = $this->get_from_XML($event_xml_url);

		$xpath = new DOMXpath($doc);

		$event['id'] = trim($xpath->query("@id")->item(0)->nodeValue);
		$event['dtstart'] = strtotime(trim($xpath->query("//date_time")->item(0)->nodeValue));

		return $event;
	}
}

function rhd_upcomingshows()
{
	$params = get_option("rhd_ts_options");

	$list = new RHD_TS_UpcomingShows($params['subdomain'], $params['tag'], $params['category'], $params['count'], $params['interval']);

	$showlist = $list->get_shows();

	if (count($showlist) > 0) {
		echo "\n<ul id=\"upcoming\" class=\"vcalendar\">\n";
		foreach($showlist as $show) {

			$img = (isset($show['images'][0]['thumb'])) ? "<img class=\"attach\" src=\"{$show['images'][0]['thumb']}\" alt=\"".htmlentities($show['name'])."\">\n\t\t" : "";
			$tags = (count($show['tags']) > 0) ? " ".implode(' ', $show['tags']) : "";
			$date = (isset($show['event']['dtstart'])) ? "<span class=\"dtstart\"><abbr class=\"value\" title=\"".date('c',$show['event']['dtstart'])."\">".date('D jS M Y',$show['event']['dtstart'])."</abbr></span> at " : "";
			echo "\t<li id=\"show_{$show['id']}\" class=\"vevent".htmlentities($tags)."\">\n\t\t$img<a href=\"{$show['url']}\" class=\"summary url\">".htmlentities($show['name'])."</a><br/>\n";
			echo "\t\t$date<span class=\"location\">".htmlentities($show['venue'])."</span>\n\t</li>\n";
		}
		echo "</ul>\n";
	} else {
		echo "<p>No shows</p>";
	}
}

?>