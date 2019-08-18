<?php
	/*
	
	Расстановка автоматических тегов для сервиса Pocket
	
	Простой скрипт на PHP с быдло-кодом сканирует сохраненные страницы для отложенного чтения в сервисе Pocket (https://getpocket.com) юзера и раставляет автоматически теги в зависимости от источника страницы и времени чтения.

	Скрипт можно поставить на сервер в CRON, чтобы постоянно обновлять теги для новых страниц.

	(с) https://github.com/07h/pocketAutoTags
	
	*/



	/* Для подключения требуется https://github.com/jshawl/pocket-oauth-php */
	require_once('config.php');



	function toPocket($consumer_key,$access_token, $api_url) {

		$url = 'https://getpocket.com/v3/' . $api_url;
		$data = array(
			'consumer_key' => $consumer_key, 
			'access_token' => $access_token
		);
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);

		return file_get_contents($url, false, $context);
	};






	// Берем страницы без тегов и последние 200 штук (у Pocket API походу есть ограничение в сутки на кол-во возвращаемых записей)
	$result = toPocket($consumer_key,$access_token, 'get?tag=_untagged_&count=200');


	$items = json_decode($result,true)['list'];


	$prefix_tag_time = 'чтение | ';
	$prefix_tag_site = 'сайт | ';

	foreach ( $items as $key => $value  ) {

		$tags = '';


		$t = ceil( $value['listen_duration_estimate']/60 );
		/* теги на длительность чтения */
		if ( $t < 2 && $t != 0 ){
			$tag = $prefix_tag_time . 'меньше 2-х минут';
			print $t . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ($t > 20) {
			$tag = $prefix_tag_time . 'больше 20 минут';
			print $t . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ($t > 10 && $t < 20) {
			$tag = $prefix_tag_time . '10-20 минут';
			print $t . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ($t > 5 && $t < 10) {
			$tag = $prefix_tag_time . '5-10 минут';
			print $t . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));
	
		} elseif ($t > 2 && $t < 5) {
			$tag = $prefix_tag_time . '2-5 минут';
			print $t . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));
		
		};




		$s = $value['given_url'];
		// теги в зависимости от источника статьи
		
		if ( strpos($s, 'youtube.com') ){
			$tag = $prefix_tag_site . 'youtube.com';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'vk.com') ) {
			$tag = $prefix_tag_site . 'vk.com';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'lurkmore') ) {
			$tag = $prefix_tag_site . 'lurkmore';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'vc.ru') ) {
			$tag = $prefix_tag_site . 'vc.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'habr.com') ) {
			$tag = $prefix_tag_site . 'habr.com';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'lenta.ru') ) {
			$tag = $prefix_tag_site . 'lenta.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'exler.ru') ) {
			$tag = $prefix_tag_site . 'exler.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'livejournal.com') ) {
			$tag = $prefix_tag_site . 'livejournal.com';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'xakep.ru') ) {
			$tag = $prefix_tag_site . 'xakep.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'forbes') ) {
			$tag = $prefix_tag_site . 'forbes';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'wikipedia') ) {
			$tag = $prefix_tag_site . 'wikipedia';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'the-village.ru') ) {
			$tag = $prefix_tag_site . 'the-village.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));

		} elseif ( strpos($s, 'batenka.ru') ) {
			$tag = $prefix_tag_site . 'batenka.ru';
			print $s . " - ".$tag."\n";
			toPocket($consumer_key,$access_token, 'send?actions='.urlencode('[{"action":"tags_add","tags":"'.$tag.'","item_id":"'.$value['item_id'].'"}]'));
		};





	};


?>