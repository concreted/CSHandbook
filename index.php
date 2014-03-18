<?php
// the markdown parser and RSS feed builder.
require 'vendor/autoload.php';

use \Michelf\MarkdownExtra;


function parse($str){
	//[======]   =>     <br><hr><br>
	$str = preg_replace('/\[\=+\]/',"<br><hr><br>",$str);

	$str = preg_replace('/\[\[([A-Za-z\_\s\']+)\]\]/','<a href="../view/$1">$1</a>',$str);

	return MarkdownExtra::defaultTransform($str);
}


// Load the configuration file
//config('source', 'local_config.ini');
config([
    'dispatch.views' => 'templates',
    'dispatch.url' => 'http://localhost/wikialg'
    ]);


// The front page of the blog.
// This will match the root url
on('GET','/', function () {
    //echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    render("main",[],false);
});


on('GET','/view/:page',function($page){

	$content = file_get_contents('data/view/'.$page.'.txt');

	$content = parse($content);

	$title = preg_replace('\_',' ',$page);
	
	render("view",['title'=>$title,'body' => $content],false);
});

on('GET','/:page',function($page){

	$path  = 'data/'.$page.'.txt';
	$title = preg_replace('/\_/',' ',$page);

	if (file_exists($path)){
		$content = file_get_contents($path);
		$content = parse($content);

		render("list",['page'=>$page,'title'=>$title,'body'=>$content],false);
	}
	else {
		redirect('./edit/'.$page);
	}
	
});

on('GET','/edit/:page',function($page){
	$path  = 'data/'.$page.'.txt';
	$title = preg_replace('/\_/',' ',$page);

	if (file_exists($path)){
		$content = file_get_contents($path);
		
		render("edit",['page'=>$page,'title'=>$title,'body'=>$content],false);
	}
	else {
		render("edit",['page'=>$page,'title'=>$title,'body'=>''],false);
	}
	});
	
	on('POST','/save/:page',function($page){
		$path  = 'data/'.$page.'.txt';
		file_put_contents($path, params('content'));
		redirect('../'.$page);
	});
	
	dispatch();



?>



