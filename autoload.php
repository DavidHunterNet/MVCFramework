<?php

spl_autoload_register( function( $class )
{
	$class = str_replace( "\\", "/", $class );
	
	$class_path = __DIR__ . '/' . $class . '.php';
	
	if( file_exists( $class_path ) )
		require_once $class_path;
});